import fs from 'node:fs';
import path from 'node:path';
import { siteVarsStyle, type SiteConfig } from './renderSite';

/**
 * Server-side homepage renderer built on the real Anchored-Peptides design,
 * de-branded and tokenized into `src/lib/templates/base-home.html`.
 *
 * The template contains `var(--ap-*)` color/font tokens plus `__PLACEHOLDER__`
 * markers; this fills them from a SiteConfig so every brand renders the same
 * rich layout with its own palette, fonts, name, logo, hero, and copy.
 */

const TEMPLATE_PATH = path.join(process.cwd(), 'src', 'lib', 'templates', 'base-home.html');
let cached: string | null = null;
function template(): string {
  if (cached == null) cached = fs.readFileSync(TEMPLATE_PATH, 'utf8');
  return cached;
}

const esc = (s: unknown): string =>
  String(s ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');

const safeUrl = (u?: string): string => (u && /^(https?:|data:image\/)/i.test(u) ? u : '');
const copy = (c: Record<string, string>, k: string, d: string) => (c[k]?.trim() ? c[k].trim() : d);

/** A neutral "vials" hero used when the brand hasn't supplied a hero image. */
const HERO_PLACEHOLDER =
  'data:image/svg+xml;utf8,' +
  encodeURIComponent(
    `<svg xmlns="http://www.w3.org/2000/svg" width="540" height="460"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e9e3d4"/><stop offset="1" stop-color="#d8d0bd"/></linearGradient></defs><rect width="540" height="460" rx="20" fill="url(#g)"/><g fill="#fff" stroke="#cfc7b3" stroke-width="1.5"><rect x="180" y="120" width="70" height="220" rx="12"/><rect x="270" y="90" width="80" height="250" rx="12"/><rect x="360" y="130" width="60" height="210" rx="12"/></g></svg>`,
  );

function logoMarkup(brandName: string, logoUrl?: string): string {
  const brand = esc(brandName);
  const url = safeUrl(logoUrl);
  if (url) {
    return `<a href="/" style="display:flex;align-items:center;gap:11px;text-decoration:none;"><img src="${esc(url)}" alt="${brand}" style="height:36px;width:auto;display:block;"></a>`;
  }
  const initial = esc((brandName.trim()[0] ?? 'B').toUpperCase());
  return (
    `<a href="/" style="display:flex;align-items:center;gap:11px;text-decoration:none;">` +
    `<span style="width:34px;height:34px;border-radius:9px;background:var(--ap-olive);color:#fff;display:flex;align-items:center;justify-content:center;font:600 17px var(--ap-serif);">${initial}</span>` +
    `<span style="font:600 20px/1 var(--ap-serif);color:var(--ap-ink);letter-spacing:.2px;">${brand}</span></a>`
  );
}

function fontsLink(url?: string): string {
  if (!url || !/^https:\/\//.test(url)) return '';
  return (
    `<link rel="preconnect" href="https://fonts.googleapis.com">` +
    `<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>` +
    `<link href="${esc(url)}" rel="stylesheet">`
  );
}

/** Remove a marker-wrapped block: <!--AP:NAME--> … <!--/AP:NAME--> */
function stripSection(h: string, name: string): string {
  const open = h.indexOf(`<!--AP:${name}-->`);
  const close = h.indexOf(`<!--/AP:${name}-->`);
  if (open < 0 || close < 0) return h;
  return h.slice(0, open) + h.slice(close + `<!--/AP:${name}-->`.length);
}

/** Decorative fallback tile art when a selling-point image wasn't generated. */
function bentoFallback(n: number): string {
  const shift = [0, 40, 80][n - 1] ?? 0;
  return (
    'data:image/svg+xml;utf8,' +
    encodeURIComponent(
      `<svg xmlns="http://www.w3.org/2000/svg" width="800" height="800"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#4a4c3c"/><stop offset="1" stop-color="#2c2e22"/></linearGradient></defs><rect width="800" height="800" fill="url(#g)"/><circle cx="${560 - shift}" cy="${230 + shift}" r="170" fill="#ffffff" opacity="0.05"/><circle cx="${240 + shift}" cy="${580 - shift}" r="230" fill="#ffffff" opacity="0.04"/></svg>`,
    )
  );
}

/** Render the full standalone homepage HTML for a brand. */
export function renderHomeHtml(cfg: SiteConfig): string {
  let h = template();

  if (cfg.showCategories === false) h = stripSection(h, 'CATEGORIES');

  const points = (cfg.sellingPoints ?? []).map((p) => p.trim()).filter(Boolean).slice(0, 3);
  if (points.length < 3) {
    h = stripSection(h, 'BENTO');
  } else {
    points.forEach((p, i) => {
      h = h.split(`__SP${i + 1}__`).join(esc(p));
      h = h.split(`__SPIMG${i + 1}__`).join(safeUrl(cfg.sellingPointImages?.[i]) || bentoFallback(i + 1));
    });
  }

  const subs: Array<[string, string]> = [
    ['__LOGO__', logoMarkup(cfg.brandName, cfg.logoUrl)],
    ['__HERO_IMAGE__', safeUrl(cfg.heroImageUrl) || HERO_PLACEHOLDER],
    ['__HERO_EYEBROW__', esc(copy(cfg.copy, 'hero_eyebrow', 'Research-Grade Quality'))],
    ['__HERO_H1__', esc(copy(cfg.copy, 'hero_h1', 'Peptides That'))],
    ['__HERO_H1_EM__', esc(copy(cfg.copy, 'hero_h1_em', 'Perform'))],
    ['__HERO_SUB__', esc(copy(cfg.copy, 'hero_sub', 'Third-party tested peptides for serious researchers. Purity you can trust, dispatched fast.'))],
    ['__CTA1__', esc(copy(cfg.copy, 'hero_cta_primary', 'Browse Catalog'))],
    ['__CTA2__', esc(copy(cfg.copy, 'hero_cta_secondary', 'Learn More'))],
    ['__FOUNDER_TAGLINE__', esc(copy(cfg.copy, 'tagline', 'Backed by testing. Built on trust.'))],
    ['__STORY_H1__', esc(copy(cfg.copy, 'story_h1', 'Why we started —'))],
    ['__STORY_H2__', esc(copy(cfg.copy, 'story_h2', 'and never looked back'))],
    // __BRAND__ appears in many spots (nav-adjacent text, standard eyebrow, footer); do it last.
    ['__BRAND__', esc(cfg.brandName || 'Peptides')],
  ];
  for (const [from, to] of subs) h = h.split(from).join(to);

  h = h.replace(/<title>[^<]*<\/title>/, `<title>${esc(cfg.brandName)} — Research Peptides</title>`);
  const head = `${fontsLink(cfg.fonts.url)}<style>:root{${siteVarsStyle(cfg.tokens)}}</style>`;
  h = h.replace('</head>', `${head}</head>`);
  return h;
}
