import { NextRequest, NextResponse } from 'next/server';
import { createSupabaseAdminClient } from '@/lib/supabase/admin';
import { buildHeroPrompt } from '@/lib/heroPrompt';
import { PRESET_BY_KEY } from '@/lib/presets';
import { hasOpenAI, generateImageBytes } from '@/lib/openai';

export const maxDuration = 120;

/**
 * POST /api/hero-image
 *   { businessName?, positioning?, presetKey?, vibe?, count? }
 *
 * Generates 1–3 hero image options via the configured image provider, uploads
 * them to the `hero-images` Storage bucket, and returns their paths + public
 * URLs for the intake preview to slot into the live theme render.
 *
 * NOTE: the image provider is pluggable. Fill in callImageProvider() for your
 * chosen model (set IMAGE_API_URL / IMAGE_API_KEY). It must return raw PNG
 * bytes. Callable by anonymous intake users — add rate limiting before launch.
 */
export async function POST(req: NextRequest) {
  const body = await req.json().catch(() => ({}));
  const count = Math.min(Math.max(Number(body.count) || 3, 1), 3);
  const preset = body.presetKey ? PRESET_BY_KEY[body.presetKey] : undefined;
  const prompt = buildHeroPrompt({
    businessName: body.businessName,
    positioning: body.positioning,
    preset,
    vibe: body.vibe,
  });

  if (!hasOpenAI() && (!process.env.IMAGE_API_URL || !process.env.IMAGE_API_KEY)) {
    return NextResponse.json(
      { ok: false, error: 'Image provider not configured (set OPENAI_API_KEY in .env.local).' },
      { status: 501 },
    );
  }

  const db = createSupabaseAdminClient();
  const results: { path: string; url: string }[] = [];

  for (let i = 0; i < count; i++) {
    let bytes: Uint8Array;
    try {
      bytes = await callImageProvider(prompt, i);
    } catch (e) {
      return NextResponse.json({ ok: false, error: `Generation failed: ${(e as Error).message}` }, { status: 502 });
    }

    // Path avoids Date.now(); a random suffix keeps options distinct per request.
    const path = `pending/${crypto.randomUUID()}.png`;
    const { error } = await db.storage.from('hero-images').upload(path, bytes, {
      contentType: 'image/png',
      upsert: false,
    });
    if (error) return NextResponse.json({ ok: false, error: error.message }, { status: 500 });

    const { data } = db.storage.from('hero-images').getPublicUrl(path);
    results.push({ path, url: data.publicUrl });
  }

  return NextResponse.json({ ok: true, prompt, images: results });
}

/**
 * Adapter for the image model. Prefers OpenAI when OPENAI_API_KEY is set;
 * otherwise falls back to a generic IMAGE_API_URL provider. Returns PNG bytes.
 * `seed` varies options within one request.
 */
async function callImageProvider(prompt: string, seed: number): Promise<Uint8Array> {
  if (hasOpenAI()) {
    // Nudge variety across the 1-3 options since the API takes no seed.
    const variants = ['', ' Slightly wider framing.', ' Warmer light, closer crop.'];
    return generateImageBytes(prompt + (variants[seed] ?? ''));
  }
  const res = await fetch(process.env.IMAGE_API_URL!, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${process.env.IMAGE_API_KEY}`,
    },
    body: JSON.stringify({ prompt, size: '1024x1024', n: 1, seed }),
  });
  if (!res.ok) throw new Error(`provider HTTP ${res.status}`);

  const ct = res.headers.get('content-type') ?? '';
  if (ct.startsWith('image/')) {
    return new Uint8Array(await res.arrayBuffer());
  }
  // JSON shapes: { url } or { data: [{ b64_json | url }] }
  const json = await res.json();
  const item = json?.data?.[0] ?? json;
  if (item?.b64_json) return Uint8Array.from(Buffer.from(item.b64_json, 'base64'));
  if (item?.url) {
    const img = await fetch(item.url);
    return new Uint8Array(await img.arrayBuffer());
  }
  throw new Error('unrecognized provider response');
}
