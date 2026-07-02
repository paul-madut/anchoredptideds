/** Small hex color helpers for the intake custom-palette controls. */

export function isHex(s: string): boolean {
  return /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(s.trim());
}

function toRgb(hex: string): [number, number, number] {
  let h = hex.replace('#', '');
  if (h.length === 3) h = h.split('').map((c) => c + c).join('');
  const n = parseInt(h, 16);
  return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
}

function toHex(r: number, g: number, b: number): string {
  const c = (v: number) => Math.max(0, Math.min(255, Math.round(v))).toString(16).padStart(2, '0');
  return `#${c(r)}${c(g)}${c(b)}`.toUpperCase();
}

/** Lighten (+) or darken (−) a hex color by a 0..1 amount toward white/black. */
export function shade(hex: string, amount: number): string {
  if (!isHex(hex)) return hex;
  const [r, g, b] = toRgb(hex);
  if (amount >= 0) return toHex(r + (255 - r) * amount, g + (255 - g) * amount, b + (255 - b) * amount);
  const a = 1 + amount;
  return toHex(r * a, g * a, b * a);
}

/** Perceived luminance 0..1 (for choosing readable on-color text). */
export function luminance(hex: string): number {
  const [r, g, b] = toRgb(hex);
  return (0.299 * r + 0.587 * g + 0.114 * b) / 255;
}

/**
 * The four primary colors a user tweaks, mapped to the tokens they should drive,
 * with sensible derived tokens so the whole palette stays coherent.
 */
export function deriveFromPrimary(primary: {
  bg?: string; ink?: string; accent?: string; dark?: string;
}): Record<string, string> {
  const out: Record<string, string> = {};
  if (primary.bg && isHex(primary.bg)) {
    out['--ap-bg'] = primary.bg;
    out['--ap-bg2'] = shade(primary.bg, 0.35);
    out['--ap-bg3'] = shade(primary.bg, 0.6);
    out['--ap-bg-card'] = shade(primary.bg, 0.35);
    out['--ap-border'] = shade(primary.bg, -0.08);
    out['--ap-border2'] = shade(primary.bg, -0.16);
  }
  if (primary.ink && isHex(primary.ink)) {
    out['--ap-ink'] = primary.ink;
    out['--ap-ink2'] = shade(primary.ink, 0.12);
    out['--ap-muted'] = shade(primary.ink, 0.42);
    out['--ap-muted2'] = shade(primary.ink, 0.58);
  }
  if (primary.accent && isHex(primary.accent)) {
    out['--ap-olive'] = primary.accent;
    out['--ap-olive-h'] = shade(primary.accent, -0.12);
    // Keep on-accent text readable.
    out['--ap-cream'] = luminance(primary.accent) > 0.6 ? '#141414' : '#F5F2EA';
  }
  if (primary.dark && isHex(primary.dark)) {
    out['--ap-dark'] = primary.dark;
    out['--ap-dark2'] = shade(primary.dark, -0.2);
  }
  return out;
}

/** Human labels for the full advanced token list (color tokens only). */
export const TOKEN_LABELS: [string, string][] = [
  ['--ap-bg', 'Page background'],
  ['--ap-bg2', 'Card background'],
  ['--ap-bg3', 'Hover / lightest'],
  ['--ap-ink', 'Primary text'],
  ['--ap-ink2', 'Heading text'],
  ['--ap-muted', 'Secondary text'],
  ['--ap-olive', 'Primary / buttons'],
  ['--ap-olive-h', 'Primary hover'],
  ['--ap-dark', 'Dark section'],
  ['--ap-dark2', 'Footer / darkest'],
  ['--ap-cream', 'On-dark text'],
  ['--ap-border', 'Borders'],
  ['--ap-border2', 'Strong borders'],
  ['--ap-blue', 'Checkout accent'],
  ['--ap-rust', 'Sale accent'],
  ['--ap-gold', 'Star rating'],
];
