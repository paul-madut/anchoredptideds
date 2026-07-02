/**
 * Standalone site renderer — the single source of truth for the homepage markup
 * shared by the in-app <Preview> (client) and the generated static HTML artifact
 * (server). Driven entirely by the theme's `--ap-*` tokens, so the preview, the
 * downloadable HTML, and the deployed WordPress site all read the same design.
 *
 * All interpolated values are HTML-escaped — copy comes from public intake, so
 * this prevents stored XSS in the admin preview / generated file.
 */

export interface SiteConfig {
  tokens: Record<string, string>;
  fonts: { url?: string; serif?: string; sans?: string };
  brandName: string;
  logoUrl?: string;
  heroImageUrl?: string;
  copy: Record<string, string>;
}

const esc = (s: unknown): string =>
  String(s ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');

/** Only allow http(s)/data image URLs into src attributes. */
const safeUrl = (u?: string): string => {
  if (!u) return '';
  return /^(https?:|data:image\/)/i.test(u) ? esc(u) : '';
};

const c = (copy: Record<string, string>, k: string, d: string) => (copy[k]?.trim() ? copy[k] : d);

/** Inline `style` string of the `--ap-*` custom properties for the root element. */
export function siteVarsStyle(tokens: Record<string, string>): string {
  return Object.entries(tokens)
    .filter(([k]) => /^--ap-[a-z0-9-]+$/.test(k))
    .map(([k, v]) => `${k}:${String(v).replace(/[<>{};]/g, '')}`)
    .join(';');
}

/** The inner homepage markup (everything inside the .ap-root wrapper), incl. styles. */
export function siteInnerHtml(cfg: SiteConfig): string {
  const { fonts, brandName, logoUrl, heroImageUrl, copy } = cfg;
  const logo = safeUrl(logoUrl);
  const hero = safeUrl(heroImageUrl);
  const fontImport = fonts.url && /^https:\/\//.test(fonts.url) ? `@import url("${esc(fonts.url)}");\n` : '';
  const monogram = esc((brandName.trim()[0] ?? 'B').toUpperCase());

  const trust: [string, string][] = [
    ['Third-Party HPLC Tested', '99%+ every batch'],
    ['Fast Dispatch', 'Same-day shipping'],
    ['Reship Guarantee', 'Full protection'],
    ['Verified COAs', 'Every lot'],
  ];
  const cats = ['Weight Loss', 'Energy', 'Healing', 'Skin', 'Brain', 'Stacks'];
  const stats: [string, string][] = [
    [c(copy, 'hero_stat1_num', '99.9%'), c(copy, 'hero_stat1_label', 'Purity')],
    [c(copy, 'hero_stat2_num', '20k+'), c(copy, 'hero_stat2_label', 'Researchers')],
    [c(copy, 'hero_stat3_num', '24h'), c(copy, 'hero_stat3_label', 'Dispatch')],
  ];

  return `<style>${fontImport}${SITE_CSS}</style>
<header class="ap-nav">
  <div class="ap-logo">
    ${logo ? `<img src="${logo}" alt="${esc(brandName)}">` : `<span class="ap-mark">${monogram}</span><span class="ap-wordmark">${esc(brandName)}</span>`}
  </div>
  <nav class="ap-links"><a href="/shop">Shop</a><a href="/learn">Learn</a><a href="/coa-library">COA Library</a></nav>
  <a class="ap-cart" href="/cart">Cart</a>
</header>
<section class="ap-hero">
  <div class="ap-hero-c">
    <p class="ap-eyebrow">${esc(c(copy, 'hero_eyebrow', 'Research-grade quality'))}</p>
    <h1>${esc(c(copy, 'hero_h1', 'Peptides That'))} <em>${esc(c(copy, 'hero_h1_em', 'Stay Grounded'))}</em></h1>
    <p class="ap-sub">${esc(c(copy, 'hero_sub', 'Third-party HPLC-tested peptides for serious researchers. Purity you can trust.'))}</p>
    <div class="ap-btns">
      <a class="ap-btn" href="/shop">${esc(c(copy, 'hero_cta_primary', 'Browse Catalog'))}</a>
      <a class="ap-btn-o" href="/learn">${esc(c(copy, 'hero_cta_secondary', 'Learn More'))}</a>
    </div>
    <div class="ap-stats">${stats.map(([n, l]) => `<div class="ap-stat"><b>${esc(n)}</b><span>${esc(l)}</span></div>`).join('')}</div>
  </div>
  <div class="ap-hero-media">
    ${hero ? `<img src="${hero}" alt="">` : `<div class="ap-hero-ph">hero image</div>`}
    <span class="ap-badge"><b>${esc(c(copy, 'hero_badge_title', 'HPLC Verified'))}</b><small>${esc(c(copy, 'hero_badge_sub', 'Batch COA available'))}</small></span>
  </div>
</section>
<section class="ap-trust">${trust.map(([a, b]) => `<div class="ap-trust-i"><b>${esc(a)}</b><small>${esc(b)}</small></div>`).join('')}</section>
<section class="ap-cats">
  <p class="ap-eyebrow">Browse by goal</p>
  <div class="ap-cat-grid">${cats.map((cat) => `<a class="ap-cat" href="/shop"><span class="ap-cat-ico"></span><b>${esc(cat)}</b></a>`).join('')}</div>
</section>`;
}

/** A complete, standalone HTML document for the generated artifact / staging view. */
export function renderSiteHtml(cfg: SiteConfig): string {
  return `<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>${esc(cfg.brandName)} — preview</title>
</head>
<body class="ap-root" style="${siteVarsStyle(cfg.tokens)}">
${siteInnerHtml(cfg)}
</body>
</html>`;
}

export const SITE_CSS = `
.ap-root{background:var(--ap-bg);color:var(--ap-ink);font-family:var(--ap-sans);max-width:var(--ap-maxw);margin:0 auto}
.ap-root *{box-sizing:border-box}
.ap-nav{display:flex;align-items:center;justify-content:space-between;padding:16px 28px;border-bottom:1px solid var(--ap-border)}
.ap-logo{display:flex;align-items:center;gap:8px}
.ap-logo img{height:30px;width:auto}
.ap-mark{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:var(--ap-olive);color:var(--ap-cream);font-family:var(--ap-sans);font-weight:700;font-size:16px;line-height:1;flex:0 0 auto}
.ap-wordmark{font-family:var(--ap-serif);font-weight:600;font-size:19px;color:var(--ap-ink)}
.ap-links{display:flex;gap:22px;color:var(--ap-muted);font-size:14px}
.ap-cart{font-size:14px;color:var(--ap-ink)}
.ap-hero{display:grid;grid-template-columns:1.1fr .9fr;gap:32px;align-items:center;padding:56px 28px}
.ap-eyebrow{text-transform:uppercase;letter-spacing:.14em;font-size:12px;color:var(--ap-muted);margin:0 0 10px}
.ap-hero h1{font-family:var(--ap-serif);font-weight:500;font-size:clamp(34px,5vw,54px);line-height:1.05;margin:0 0 16px;color:var(--ap-ink)}
.ap-hero h1 em{font-style:italic;color:var(--ap-olive)}
.ap-sub{color:var(--ap-muted);font-size:16px;line-height:1.5;max-width:440px;margin:0 0 24px}
.ap-btns{display:flex;gap:12px;margin-bottom:28px}
.ap-btn{background:var(--ap-olive);color:var(--ap-cream);padding:12px 22px;border-radius:var(--ap-pill);font-size:14px;font-weight:600}
.ap-btn-o{border:1px solid var(--ap-border2);color:var(--ap-ink);padding:12px 22px;border-radius:var(--ap-pill);font-size:14px;font-weight:600}
.ap-stats{display:flex;gap:28px}
.ap-stat b{font-family:var(--ap-serif);font-size:26px;color:var(--ap-ink);display:block}
.ap-stat span{font-size:12px;color:var(--ap-muted)}
.ap-hero-media{position:relative}
.ap-hero-media img{width:100%;border-radius:var(--ap-r);display:block;aspect-ratio:1/1;object-fit:cover}
.ap-hero-ph{width:100%;aspect-ratio:1/1;border-radius:var(--ap-r);background:var(--ap-bg3);border:1px dashed var(--ap-border2);display:flex;align-items:center;justify-content:center;color:var(--ap-muted2);font-size:13px}
.ap-badge{position:absolute;left:16px;bottom:16px;background:var(--ap-bg2);border:1px solid var(--ap-border);border-radius:var(--ap-rs);padding:8px 12px;display:flex;flex-direction:column;box-shadow:0 6px 20px rgba(0,0,0,.08)}
.ap-badge b{font-size:13px;color:var(--ap-ink)}
.ap-badge small{font-size:11px;color:var(--ap-muted)}
.ap-trust{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;padding:24px 28px;background:var(--ap-bg2);border-top:1px solid var(--ap-border);border-bottom:1px solid var(--ap-border)}
.ap-trust-i b{display:block;font-size:13px;color:var(--ap-ink)}
.ap-trust-i small{font-size:12px;color:var(--ap-muted)}
.ap-cats{padding:48px 28px}
.ap-cat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:16px}
.ap-cat{display:flex;align-items:center;gap:12px;padding:18px;background:var(--ap-bg2);border:1px solid var(--ap-border);border-radius:var(--ap-rs)}
.ap-cat b{font-family:var(--ap-serif);font-weight:600;font-size:17px}
.ap-cat-ico{width:34px;height:34px;border-radius:50%;background:var(--ap-olive);opacity:.85;flex:0 0 auto}
@media(max-width:720px){.ap-hero{grid-template-columns:1fr}.ap-trust{grid-template-columns:repeat(2,1fr)}.ap-cat-grid{grid-template-columns:1fr 1fr}}
`;
