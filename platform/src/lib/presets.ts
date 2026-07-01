/**
 * Design presets — the curated token sets a customer picks from during intake.
 *
 * Each preset is a full map of the theme's `--ap-*` CSS custom properties plus a
 * font pairing. These are the SAME variables the WordPress theme reads
 * (ap/anchored-peptides/assets/css/tokens.css), so the intake preview and the
 * deployed site render from one source of truth. At deploy the chosen preset's
 * `tokens` become the site's `ap_brand_tokens` option and `fonts` become
 * `ap_fonts_url`.
 *
 * `mono` (black & white) is the default. Themes are declared as compact color
 * seeds and expanded by mk(); radii + layout are constant across all presets.
 */

export type Tokens = Record<string, string>;
export interface Fonts {
  url: string;
  serif: string;
  sans: string;
}
export interface Preset {
  key: string;
  label: string;
  description: string;
  tokens: Tokens;
  fonts: Fonts;
  sort: number;
}

// Radii + layout are identical for every preset.
const STRUCTURE: Tokens = {
  '--ap-r': '16px',
  '--ap-rs': '12px',
  '--ap-rx': '8px',
  '--ap-pill': '40px',
  '--ap-maxw': '1280px',
};

const GOOGLE = (families: string) => `https://fonts.googleapis.com/css2?${families}&display=swap`;

// Reusable font pairings.
const FONTS: Record<string, Fonts> = {
  refined: {
    url: GOOGLE('family=Hanken+Grotesk:wght@400;500;600;700&family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;1,6..72,400;1,6..72,600'),
    serif: "'Newsreader', Georgia, 'Times New Roman', serif",
    sans: "'Hanken Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif",
  },
  editorial: {
    url: GOOGLE('family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;1,9..144,400&family=Inter:wght@400;500;600;700'),
    serif: "'Fraunces', Georgia, serif",
    sans: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif",
  },
  clinical: {
    url: GOOGLE('family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@400;500;600;700'),
    serif: "'Instrument Serif', Georgia, serif",
    sans: "'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif",
  },
  techno: {
    url: GOOGLE('family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600&family=Space+Grotesk:wght@400;500;600;700'),
    serif: "'Fraunces', Georgia, serif",
    sans: "'Space Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif",
  },
  geometric: {
    url: GOOGLE('family=Sora:wght@400;500;600;700&family=Inter:wght@400;500;600'),
    serif: "'Sora', -apple-system, BlinkMacSystemFont, sans-serif",
    sans: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif",
  },
  plex: {
    url: GOOGLE('family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@400;500;600;700'),
    serif: "'IBM Plex Sans', -apple-system, sans-serif",
    sans: "'IBM Plex Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif",
  },
};

interface Seed {
  key: string;
  label: string;
  description: string;
  sort: number;
  fonts: Fonts;
  // Surfaces
  bg: string; bg2: string; bg3: string;
  // Ink / text
  ink: string; ink2?: string; muted: string; muted2?: string;
  // Primary brand (maps to --ap-olive)
  accent: string; accentH: string;
  // Dark sections
  dark: string; dark2: string; taupe?: string;
  // On-dark text
  cream: string; cream2: string; cream3: string;
  // Lines
  border: string; border2: string;
  // Functional accents (default to the shipped values unless overridden)
  blue?: string; rust?: string; gold?: string; greenOk?: string;
}

/** Expand a compact color seed into the full `--ap-*` token map. */
function mk(s: Seed): Preset {
  return {
    key: s.key,
    label: s.label,
    description: s.description,
    sort: s.sort,
    fonts: s.fonts,
    tokens: {
      '--ap-bg': s.bg, '--ap-bg2': s.bg2, '--ap-bg3': s.bg3, '--ap-bg-card': s.bg2,
      '--ap-ink': s.ink, '--ap-ink2': s.ink2 ?? s.ink, '--ap-muted': s.muted, '--ap-muted2': s.muted2 ?? s.muted,
      '--ap-olive': s.accent, '--ap-olive-h': s.accentH, '--ap-dark': s.dark, '--ap-dark2': s.dark2, '--ap-taupe': s.taupe ?? s.muted,
      '--ap-cream': s.cream, '--ap-cream2': s.cream2, '--ap-cream3': s.cream3,
      '--ap-border': s.border, '--ap-border2': s.border2,
      '--ap-blue': s.blue ?? '#2B5687', '--ap-blue-h': '#24496f',
      '--ap-rust': s.rust ?? '#A8503B', '--ap-gold': s.gold ?? '#B0863A', '--ap-green-ok': s.greenOk ?? '#3E6F4E',
      '--ap-serif': s.fonts.serif, '--ap-sans': s.fonts.sans,
      ...STRUCTURE,
    },
  };
}

export const PRESETS: Preset[] = [
  // Default — pure black & white, fully monochrome (functional accents forced to ink).
  mk({
    key: 'mono', label: 'Monochrome', description: 'Pure black & white. Editorial, timeless, high-contrast.', sort: 0, fonts: FONTS.editorial,
    bg: '#F4F4F4', bg2: '#FAFAFA', bg3: '#FFFFFF', ink: '#141414', ink2: '#222222', muted: '#666666', muted2: '#8A8A8A',
    accent: '#141414', accentH: '#000000', dark: '#1A1A1A', dark2: '#0D0D0D', taupe: '#555555',
    cream: '#F5F5F5', cream2: '#C9C9C9', cream3: '#9A9A9A', border: '#E4E4E4', border2: '#D0D0D0',
    blue: '#141414', rust: '#141414', gold: '#141414', greenOk: '#141414',
  }),
  mk({
    key: 'ap-olive', label: 'Anchored Olive', description: 'Warm cream, deep olive, refined serif. The flagship look.', sort: 1, fonts: FONTS.refined,
    bg: '#ECE7DA', bg2: '#F4F0E6', bg3: '#FBF9F3', ink: '#2C2E22', ink2: '#3C3E32', muted: '#6E6A5C', muted2: '#8A8676',
    accent: '#3E412E', accentH: '#4A4D38', dark: '#33352A', dark2: '#2C2E22', taupe: '#6E6453',
    cream: '#F4F0E6', cream2: '#D9D3C2', cream3: '#B9B3A0', border: '#DCD5C4', border2: '#C9C1AC',
  }),
  mk({
    key: 'nv-beige', label: 'Clinical Beige', description: 'Bright beige, emerald accents, mono detailing. Lab-precise.', sort: 2, fonts: FONTS.clinical,
    bg: '#F2F0EB', bg2: '#F8F7F3', bg3: '#FCFBF8', ink: '#1E211C', ink2: '#2C302A', muted: '#5E6358', muted2: '#7C8074',
    accent: '#2D6A4F', accentH: '#255A43', dark: '#1F3A2E', dark2: '#16241C', taupe: '#5E6358',
    cream: '#F2F0EB', cream2: '#CFD4C9', cream3: '#A7AE9F', border: '#E0DDD4', border2: '#CBC7BB', greenOk: '#2D6A4F',
  }),
  mk({
    key: 'graphite', label: 'Graphite', description: 'Cool greys, chartreuse accent, mono type. Bold and clinical.', sort: 3, fonts: FONTS.plex,
    bg: '#E9EAE6', bg2: '#F3F3F0', bg3: '#FAFAF8', ink: '#191A17', ink2: '#26271F', muted: '#585A50', muted2: '#787A6E',
    accent: '#3C4A1E', accentH: '#4A5A26', dark: '#20241A', dark2: '#141610', taupe: '#585A50',
    cream: '#E9EAE6', cream2: '#C7C9BF', cream3: '#9C9E92', border: '#D9DAD2', border2: '#C2C4B8', gold: '#8A9A2E', greenOk: '#5A7D2E',
  }),
  mk({
    key: 'navy-mono', label: 'Midnight Navy', description: 'Cool off-white, deep navy, technical accents. Premium.', sort: 4, fonts: FONTS.techno,
    bg: '#EEF0F3', bg2: '#F6F7F9', bg3: '#FCFCFD', ink: '#141A24', ink2: '#20293A', muted: '#5A6473', muted2: '#7B8494',
    accent: '#1E3A5F', accentH: '#182F4D', dark: '#16233A', dark2: '#0F1826', taupe: '#4A5568',
    cream: '#EEF0F3', cream2: '#C6CCD6', cream3: '#9AA2B0', border: '#DBDFE6', border2: '#C3C9D3', blue: '#1E3A5F',
  }),
  mk({
    key: 'crimson', label: 'Crimson', description: 'Soft blush surfaces, bold crimson accent. Confident.', sort: 5, fonts: FONTS.editorial,
    bg: '#F6EFEF', bg2: '#FBF6F6', bg3: '#FFFCFC', ink: '#241A1A', ink2: '#332525', muted: '#6E5A5A', muted2: '#8F7A7A',
    accent: '#9E2B2B', accentH: '#872323', dark: '#2E1717', dark2: '#1F0F0F', taupe: '#6E5A5A',
    cream: '#F6EFEF', cream2: '#E0C9C9', cream3: '#BFA0A0', border: '#EAD9D9', border2: '#D8C0C0', rust: '#9E2B2B',
  }),
  mk({
    key: 'violet', label: 'Royal Violet', description: 'Cool lilac tint, rich violet accent. Modern luxe.', sort: 6, fonts: FONTS.editorial,
    bg: '#F1EFF6', bg2: '#F8F6FB', bg3: '#FDFCFF', ink: '#1E1A28', ink2: '#2C2540', muted: '#5F5873', muted2: '#807A94',
    accent: '#5B3A9E', accentH: '#4E3187', dark: '#241A3A', dark2: '#170F26', taupe: '#5F5873',
    cream: '#F1EFF6', cream2: '#D0C9E0', cream3: '#ABA0BF', border: '#E2DAEE', border2: '#CDC0D8', blue: '#5B3A9E',
  }),
  mk({
    key: 'emerald', label: 'Emerald', description: 'Crisp mint surfaces, vivid emerald accent. Fresh, medical.', sort: 7, fonts: FONTS.clinical,
    bg: '#EBF1EE', bg2: '#F4F8F6', bg3: '#FBFDFC', ink: '#14211B', ink2: '#1F332A', muted: '#566B60', muted2: '#77948A',
    accent: '#0F7A55', accentH: '#0C6547', dark: '#123A2C', dark2: '#0B241A', taupe: '#566B60',
    cream: '#EBF1EE', cream2: '#C6D6CE', cream3: '#9AB0A6', border: '#D9E6E0', border2: '#C0D3CB', greenOk: '#0F7A55',
  }),
  mk({
    key: 'ocean', label: 'Ocean Teal', description: 'Pale aqua surfaces, deep teal accent. Calm and clean.', sort: 8, fonts: FONTS.techno,
    bg: '#EAF0F2', bg2: '#F3F8F9', bg3: '#FBFDFD', ink: '#142124', ink2: '#1F3338', muted: '#566B70', muted2: '#778E94',
    accent: '#0E6E7A', accentH: '#0B5B65', dark: '#123338', dark2: '#0B2024', taupe: '#566B70',
    cream: '#EAF0F2', cream2: '#C6D6DA', cream3: '#9AB0B4', border: '#D9E6E8', border2: '#C0D3D6', blue: '#0E6E7A',
  }),
  mk({
    key: 'amber', label: 'Amber Gold', description: 'Warm sand surfaces, amber-gold accent. Rich and inviting.', sort: 9, fonts: FONTS.geometric,
    bg: '#F5F0E6', bg2: '#FAF6EE', bg3: '#FEFCF7', ink: '#241F14', ink2: '#33301F', muted: '#6E6552', muted2: '#8F8770',
    accent: '#B5852A', accentH: '#9C7123', dark: '#33301F', dark2: '#1F1D0F', taupe: '#6E6552',
    cream: '#F5F0E6', cream2: '#E0D5BC', cream3: '#BFB393', border: '#EAE1CC', border2: '#D8CCB0', gold: '#B5852A',
  }),
  mk({
    key: 'rose', label: 'Rosewood', description: 'Blush surfaces, deep rose accent. Elegant and warm.', sort: 10, fonts: FONTS.editorial,
    bg: '#F7EFF1', bg2: '#FCF6F8', bg3: '#FFFCFD', ink: '#241A1E', ink2: '#33252B', muted: '#6E5A61', muted2: '#8F7A82',
    accent: '#A83B5E', accentH: '#8F3250', dark: '#33171F', dark2: '#1F0F14', taupe: '#6E5A61',
    cream: '#F7EFF1', cream2: '#E4C9D2', cream3: '#C6A0AD', border: '#EDD9DF', border2: '#DCC0C8', rust: '#A83B5E',
  }),
  mk({
    key: 'warm-sans', label: 'Warm Modern', description: 'Soft warm neutrals, terracotta accent, all-sans. Friendly.', sort: 11, fonts: FONTS.geometric,
    bg: '#F3EEE8', bg2: '#F9F5F0', bg3: '#FDFBF8', ink: '#2A2320', ink2: '#3A322D', muted: '#6B6058', muted2: '#8C8177',
    accent: '#B25B3C', accentH: '#9C4D31', dark: '#33261F', dark2: '#241A15', taupe: '#6B6058',
    cream: '#F3EEE8', cream2: '#DBD1C6', cream3: '#B6A99C', border: '#E4DBD1', border2: '#D0C5B8',
  }),
];

export const PRESET_BY_KEY: Record<string, Preset> = Object.fromEntries(
  PRESETS.map((p) => [p.key, p]),
);
