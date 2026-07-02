import { NextRequest, NextResponse } from 'next/server';
import { createSupabaseAdminClient } from '@/lib/supabase/admin';
import { PRESET_BY_KEY } from '@/lib/presets';

/**
 * POST /api/intake — public. Persists a completed intake as a `site_requests`
 * row (status 'submitted'). Handles the optional logo upload (base64 -> Storage)
 * server-side so the client never needs write access to buckets or tables.
 */
export async function POST(req: NextRequest) {
  const body = await req.json().catch(() => null);
  if (!body || typeof body !== 'object') {
    return NextResponse.json({ ok: false, error: 'Invalid body' }, { status: 400 });
  }
  if (!body.business_name || !body.customer_email) {
    return NextResponse.json({ ok: false, error: 'Brand name and email are required.' }, { status: 400 });
  }

  const db = createSupabaseAdminClient();
  const preset = body.preset_key ? PRESET_BY_KEY[body.preset_key] : undefined;

  // Optional logo: decode the data URL and store it.
  let logoPath: string | null = null;
  if (typeof body.logo_data_url === 'string' && body.logo_data_url.startsWith('data:')) {
    const m = body.logo_data_url.match(/^data:([^;]+);base64,(.+)$/);
    if (m) {
      const [, mime, b64] = m;
      const ext = extFromMime(mime);
      const path = `pending/${crypto.randomUUID()}.${ext}`;
      const { error } = await db.storage.from('logos').upload(path, Buffer.from(b64, 'base64'), { contentType: mime, upsert: false });
      if (!error) logoPath = path;
    }
  }

  const { data, error } = await db
    .from('site_requests')
    .insert({
      status: 'submitted',
      customer_name: body.customer_name ?? null,
      customer_email: body.customer_email,
      business_name: body.business_name,
      positioning: body.positioning ?? null,
      answers: body.answers ?? {},
      emphasis_categories: body.emphasis_categories ?? [],
      preset_key: body.preset_key ?? null,
      tokens: body.tokens ?? {},
      fonts: preset?.fonts ?? {},
      copy: body.copy ?? {},
      logo_path: logoPath,
      hero_image_path: body.hero_image_path ?? null,
    })
    .select('id')
    .single();

  if (error) return NextResponse.json({ ok: false, error: error.message }, { status: 500 });
  return NextResponse.json({ ok: true, id: data.id });
}

function extFromMime(mime: string): string {
  if (mime.includes('svg')) return 'svg';
  if (mime.includes('png')) return 'png';
  if (mime.includes('webp')) return 'webp';
  if (mime.includes('jpeg') || mime.includes('jpg')) return 'jpg';
  return 'png';
}
