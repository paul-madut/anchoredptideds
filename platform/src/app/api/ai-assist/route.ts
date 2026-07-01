import { NextRequest, NextResponse } from 'next/server';
import { hasOpenAI, chatJSON } from '@/lib/openai';
import { PRESETS } from '@/lib/presets';

export const maxDuration = 60;

const CATEGORY_SLUGS = ['weight-loss', 'energy', 'healing', 'skin', 'brain', 'stacks'];

/**
 * POST /api/ai-assist — the "Let AI choose" button on each intake step.
 *   { step: 'brand'|'focus'|'theme'|'copy', context: {...} }
 * Returns only the fields relevant to that step, validated against allowed values.
 */
export async function POST(req: NextRequest) {
  if (!hasOpenAI()) {
    return NextResponse.json({ ok: false, error: 'AI not configured (set OPENAI_API_KEY in .env.local).' }, { status: 501 });
  }

  const body = await req.json().catch(() => ({}));
  const step: string = body.step;
  const ctx = body.context ?? {};

  const system =
    'You are a brand strategist for a premium research-peptide e-commerce store. ' +
    'Respond ONLY with a JSON object matching the requested keys. Keep copy concise, ' +
    'premium, and compliant (research-use framing; never medical claims).';

  try {
    if (step === 'brand') {
      const out = await chatJSON(
        system,
        `Invent a brand for a peptide store. Return {"business_name": string (1-3 words, brandable), ` +
          `"positioning": string (<=12 words, the vibe/positioning)}. ` +
          `Seed idea (optional): ${JSON.stringify(ctx)}`,
      );
      return NextResponse.json({ ok: true, business_name: str(out.business_name), positioning: str(out.positioning) });
    }

    if (step === 'focus') {
      const out = await chatJSON(
        system,
        `Choose 2-4 product focus categories from this exact list: ${JSON.stringify(CATEGORY_SLUGS)}. ` +
          `Return {"emphasis": string[]}. Brand: ${JSON.stringify(ctx)}`,
      );
      const emphasis = (Array.isArray(out.emphasis) ? out.emphasis : []).filter((s: unknown) => CATEGORY_SLUGS.includes(s as string));
      return NextResponse.json({ ok: true, emphasis });
    }

    if (step === 'theme') {
      const keys = PRESETS.map((p) => p.key);
      const out = await chatJSON(
        system,
        `Pick the single best theme key for this brand from: ` +
          `${JSON.stringify(PRESETS.map((p) => ({ key: p.key, label: p.label, desc: p.description })))}. ` +
          `Return {"preset_key": string}. Brand: ${JSON.stringify(ctx)}`,
      );
      const preset_key = keys.includes(out.preset_key as string) ? (out.preset_key as string) : keys[0];
      return NextResponse.json({ ok: true, preset_key });
    }

    if (step === 'copy') {
      const out = await chatJSON(
        system,
        `Write homepage hero copy. Return {"hero_eyebrow": string, "hero_h1": string, ` +
          `"hero_h1_em": string (the emphasized tail of the headline), "hero_sub": string (<=22 words), ` +
          `"hero_cta_primary": string (<=3 words), "hero_cta_secondary": string (<=3 words), ` +
          `"tagline": string (<=6 words)}. Brand: ${JSON.stringify(ctx)}`,
      );
      return NextResponse.json({
        ok: true,
        copy: {
          hero_eyebrow: str(out.hero_eyebrow),
          hero_h1: str(out.hero_h1),
          hero_h1_em: str(out.hero_h1_em),
          hero_sub: str(out.hero_sub),
          hero_cta_primary: str(out.hero_cta_primary),
          hero_cta_secondary: str(out.hero_cta_secondary),
          tagline: str(out.tagline),
        },
      });
    }

    return NextResponse.json({ ok: false, error: `Unknown step: ${step}` }, { status: 400 });
  } catch (e) {
    return NextResponse.json({ ok: false, error: (e as Error).message }, { status: 502 });
  }
}

function str(v: unknown): string {
  return typeof v === 'string' ? v : '';
}
