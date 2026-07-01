import type { Preset } from './presets';

/**
 * Build an image-generation prompt for a hero visual from the customer's
 * choices. Peptide hero art reads best as clean, premium product/lab imagery —
 * we bias toward that and weave in the palette + brand vibe.
 */
export function buildHeroPrompt(opts: {
  businessName?: string;
  positioning?: string;
  preset?: Preset;
  vibe?: string;
}): string {
  const paletteWords = opts.preset ? `${opts.preset.label.toLowerCase()} palette` : 'warm neutral palette';
  const vibe = opts.vibe || opts.positioning || 'premium, research-grade, trustworthy';
  return [
    'Editorial product hero photograph of pharmaceutical-grade peptide research vials',
    'on a clean minimal surface, soft studio lighting, shallow depth of field,',
    `${paletteWords}, ${vibe} mood,`,
    'no text, no logos, no watermarks, high detail, premium e-commerce hero composition,',
    'plenty of negative space on one side for overlay copy.',
  ].join(' ');
}
