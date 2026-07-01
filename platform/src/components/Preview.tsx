'use client';

import type { Tokens, Fonts } from '@/lib/presets';

export interface PreviewProps {
  tokens: Tokens;
  fonts: Partial<Fonts>;
  brandName: string;
  logoUrl?: string;
  heroImageUrl?: string;
  copy: Record<string, string>;
}

const C = (copy: Record<string, string>, k: string, d: string) => (copy[k]?.trim() ? copy[k] : d);

/**
 * Hybrid live preview. Renders a faithful snapshot of the real theme's homepage
 * hero + trust + category sections, driven entirely by the same `--ap-*` tokens
 * the WordPress theme consumes. The customer's AI hero image and copy slot in,
 * so what they approve matches the deployed site (structure) with their own art.
 *
 * Rendered inside an <iframe srcDoc> by IntakePreview for full style isolation.
 */
export default function Preview({ tokens, fonts, brandName, logoUrl, heroImageUrl, copy }: PreviewProps) {
  return (
    <div className="pv-root" style={{ ...(objFromVars(tokens)) }}>
      {fonts.url ? <link rel="stylesheet" href={fonts.url} /> : null}
      <style>{PV_CSS}</style>

      <header className="pv-nav">
        <div className="pv-logo">
          {logoUrl ? (
            <img src={logoUrl} alt={brandName} />
          ) : (
            <>
              <svg width="26" height="26" viewBox="0 0 40 40" fill="none" stroke="var(--ap-olive)" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round">
                <circle cx="20" cy="9" r="3.5" /><line x1="20" y1="12.5" x2="20" y2="31" /><line x1="13" y1="16" x2="27" y2="16" /><path d="M11 24c0 6 9 9.5 9 9.5s9-3.5 9-9.5" />
              </svg>
              <span className="pv-wordmark">{brandName}</span>
            </>
          )}
        </div>
        <nav className="pv-links"><span>Shop</span><span>Learn</span><span>COA Library</span></nav>
        <span className="pv-cart">Cart</span>
      </header>

      <section className="pv-hero">
        <div className="pv-hero-c">
          <p className="pv-eyebrow">{C(copy, 'hero_eyebrow', 'Research-grade quality')}</p>
          <h1>
            {C(copy, 'hero_h1', 'Peptides That')} <em>{C(copy, 'hero_h1_em', 'Stay Grounded')}</em>
          </h1>
          <p className="pv-sub">{C(copy, 'hero_sub', 'Third-party HPLC-tested peptides for serious researchers. Purity you can trust.')}</p>
          <div className="pv-btns">
            <a className="pv-btn">{C(copy, 'hero_cta_primary', 'Browse Catalog')}</a>
            <a className="pv-btn-o">{C(copy, 'hero_cta_secondary', 'Learn More')}</a>
          </div>
          <div className="pv-stats">
            {[
              [C(copy, 'hero_stat1_num', '99.9%'), C(copy, 'hero_stat1_label', 'Purity')],
              [C(copy, 'hero_stat2_num', '20k+'), C(copy, 'hero_stat2_label', 'Researchers')],
              [C(copy, 'hero_stat3_num', '24h'), C(copy, 'hero_stat3_label', 'Dispatch')],
            ].map(([n, l], i) => (
              <div key={i} className="pv-stat"><b>{n}</b><span>{l}</span></div>
            ))}
          </div>
        </div>
        <div className="pv-hero-media">
          {heroImageUrl ? <img src={heroImageUrl} alt="" /> : <div className="pv-hero-ph">hero image</div>}
          <span className="pv-badge"><b>{C(copy, 'hero_badge_title', 'HPLC Verified')}</b><small>{C(copy, 'hero_badge_sub', 'Batch COA available')}</small></span>
        </div>
      </section>

      <section className="pv-trust">
        {[
          ['Third-Party HPLC Tested', '99%+ every batch'],
          ['Fast Dispatch', 'Same-day shipping'],
          ['Reship Guarantee', 'Full protection'],
          ['Verified COAs', 'Every lot'],
        ].map(([a, b], i) => (
          <div key={i} className="pv-trust-i"><b>{a}</b><small>{b}</small></div>
        ))}
      </section>

      <section className="pv-cats">
        <p className="pv-eyebrow">Browse by goal</p>
        <div className="pv-cat-grid">
          {['Weight Loss', 'Energy', 'Healing', 'Skin', 'Brain', 'Stacks'].map((c) => (
            <div key={c} className="pv-cat"><span className="pv-cat-ico" /><b>{c}</b></div>
          ))}
        </div>
      </section>
    </div>
  );
}

/** Convert token map to a React style object of CSS custom properties. */
function objFromVars(tokens: Tokens): React.CSSProperties {
  const out: Record<string, string> = {};
  for (const [k, v] of Object.entries(tokens)) out[k] = v;
  return out as React.CSSProperties;
}

const PV_CSS = `
.pv-root{background:var(--ap-bg);color:var(--ap-ink);font-family:var(--ap-sans);max-width:var(--ap-maxw);margin:0 auto}
.pv-root *{box-sizing:border-box}
.pv-nav{display:flex;align-items:center;justify-content:space-between;padding:16px 28px;border-bottom:1px solid var(--ap-border)}
.pv-logo{display:flex;align-items:center;gap:8px}
.pv-logo img{height:30px;width:auto}
.pv-wordmark{font-family:var(--ap-serif);font-weight:600;font-size:19px;color:var(--ap-ink)}
.pv-links{display:flex;gap:22px;color:var(--ap-muted);font-size:14px}
.pv-cart{font-size:14px;color:var(--ap-ink)}
.pv-hero{display:grid;grid-template-columns:1.1fr .9fr;gap:32px;align-items:center;padding:56px 28px}
.pv-eyebrow{text-transform:uppercase;letter-spacing:.14em;font-size:12px;color:var(--ap-muted);margin:0 0 10px}
.pv-hero h1{font-family:var(--ap-serif);font-weight:500;font-size:clamp(34px,5vw,54px);line-height:1.05;margin:0 0 16px;color:var(--ap-ink)}
.pv-hero h1 em{font-style:italic;color:var(--ap-olive)}
.pv-sub{color:var(--ap-muted);font-size:16px;line-height:1.5;max-width:440px;margin:0 0 24px}
.pv-btns{display:flex;gap:12px;margin-bottom:28px}
.pv-btn{background:var(--ap-olive);color:var(--ap-cream);padding:12px 22px;border-radius:var(--ap-pill);font-size:14px;font-weight:600}
.pv-btn-o{border:1px solid var(--ap-border2);color:var(--ap-ink);padding:12px 22px;border-radius:var(--ap-pill);font-size:14px;font-weight:600}
.pv-stats{display:flex;gap:28px}
.pv-stat b{font-family:var(--ap-serif);font-size:26px;color:var(--ap-ink);display:block}
.pv-stat span{font-size:12px;color:var(--ap-muted)}
.pv-hero-media{position:relative}
.pv-hero-media img{width:100%;border-radius:var(--ap-r);display:block;aspect-ratio:1/1;object-fit:cover}
.pv-hero-ph{width:100%;aspect-ratio:1/1;border-radius:var(--ap-r);background:var(--ap-bg3);border:1px dashed var(--ap-border2);display:flex;align-items:center;justify-content:center;color:var(--ap-muted2);font-size:13px}
.pv-badge{position:absolute;left:16px;bottom:16px;background:var(--ap-bg2);border:1px solid var(--ap-border);border-radius:var(--ap-rs);padding:8px 12px;display:flex;flex-direction:column;box-shadow:0 6px 20px rgba(0,0,0,.08)}
.pv-badge b{font-size:13px;color:var(--ap-ink)}
.pv-badge small{font-size:11px;color:var(--ap-muted)}
.pv-trust{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;padding:24px 28px;background:var(--ap-bg2);border-top:1px solid var(--ap-border);border-bottom:1px solid var(--ap-border)}
.pv-trust-i b{display:block;font-size:13px;color:var(--ap-ink)}
.pv-trust-i small{font-size:12px;color:var(--ap-muted)}
.pv-cats{padding:48px 28px}
.pv-cat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:16px}
.pv-cat{display:flex;align-items:center;gap:12px;padding:18px;background:var(--ap-bg2);border:1px solid var(--ap-border);border-radius:var(--ap-rs)}
.pv-cat b{font-family:var(--ap-serif);font-weight:600;font-size:17px}
.pv-cat-ico{width:34px;height:34px;border-radius:50%;background:var(--ap-olive);opacity:.85;flex:0 0 auto}
@media(max-width:720px){.pv-hero{grid-template-columns:1fr}.pv-trust{grid-template-columns:repeat(2,1fr)}.pv-cat-grid{grid-template-columns:1fr 1fr}}
`;
