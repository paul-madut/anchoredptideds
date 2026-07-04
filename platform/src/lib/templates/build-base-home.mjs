import fs from 'node:fs';

const SRC = './rendered.html';
const OUT = process.argv[2] || '/Users/paulmadut/Documents/anchoredptideds/platform/src/lib/templates/base-home.html';

let h = fs.readFileSync(SRC, 'utf8');

// 1) Strip DC template attributes (cosmetic noise)
h = h.replace(/ data-dc-tpl="[^"]*"/g, '');

// 2) Remove inlined @font-face blocks (fonts served via Google link instead)
h = h.replace(/@font-face\s*\{[^}]*\}/g, '');

// 3) Swap the ~3MB hero PNG for a placeholder token; neutralize its alt
h = h.replace(/data:image\/png;base64,[A-Za-z0-9+/=]+/g, '__HERO_IMAGE__');
h = h.replace(/Anchored Peptides research peptide vials/g, '__BRAND__ research peptides');

// 4) Tokenize the palette. Rendered DOM uses rgb(r, g, b); map each to an --ap-* var.
const MAP = [
  ['236, 231, 218', '--ap-bg'],    ['244, 240, 230', '--ap-bg2'],   ['251, 249, 243', '--ap-bg3'],
  ['239, 234, 221', '--ap-bg2'],   ['241, 236, 221', '--ap-bg2'],   ['231, 226, 211', '--ap-bg'],
  ['44, 46, 34', '--ap-ink'],      ['60, 62, 50', '--ap-ink2'],
  ['62, 65, 46', '--ap-olive'],    ['74, 77, 56', '--ap-olive-h'],  ['65, 81, 60', '--ap-olive'],  ['71, 74, 53', '--ap-olive'],
  ['79, 107, 74', '--ap-green-ok'],
  ['51, 53, 42', '--ap-dark'],     ['38, 38, 31', '--ap-dark2'],    ['51, 53, 42', '--ap-dark'],
  ['220, 213, 196', '--ap-border'],['201, 193, 172', '--ap-border2'],['220, 214, 198', '--ap-border'],
  ['228, 222, 206', '--ap-border'],['226, 220, 201', '--ap-border2'],['231, 226, 211', '--ap-border'],
  ['43, 86, 135', '--ap-blue'],    ['168, 80, 59', '--ap-rust'],
  ['132, 124, 105', '--ap-muted'], ['92, 88, 72', '--ap-muted2'],   ['110, 100, 82', '--ap-taupe'],
  ['154, 150, 130', '--ap-muted2'],['154, 146, 118', '--ap-muted2'],['168, 159, 137', '--ap-muted2'],
  // Tail: subtle cream/border/gold variants surfaced after the first pass.
  ['217, 211, 194', '--ap-cream2'],['218, 211, 196', '--ap-cream2'],['195, 187, 168', '--ap-border2'],
  ['227, 221, 204', '--ap-border'], ['189, 183, 166', '--ap-cream3'], ['183, 175, 155', '--ap-cream3'],
  ['42, 44, 34', '--ap-ink'],       ['236, 230, 216', '--ap-bg'],     ['110, 100, 83', '--ap-taupe'],
  ['194, 146, 46', '--ap-gold'],  ['216, 178, 90', '--ap-gold'],
  ['74, 76, 62', '--ap-olive-h'], ['71, 73, 56', '--ap-olive'],  ['90, 92, 76', '--ap-muted2'],
  ['199, 192, 172', '--ap-border2'],['185, 179, 160', '--ap-cream3'],['126, 122, 104', '--ap-muted'],
];
for (const [rgb, tok] of MAP) {
  h = h.split(`rgb(${rgb})`).join(`var(${tok})`);
}

// 4b) Tokenize fonts so the brand's chosen pairing flows in (default = refined).
h = h.split('&quot;Hanken Grotesk&quot;').join('var(--ap-sans)');
h = h.split("'Hanken Grotesk', system-ui, sans-serif").join('var(--ap-sans)');
h = h.split('Newsreader, serif').join('var(--ap-serif)');
h = h.split(' Newsreader;').join(' var(--ap-serif);');
h = h.split(' Newsreader ').join(' var(--ap-serif) ');

// 5) De-brand the header logo (anchor emoji + wordmark) → a fillable placeholder.
const logoRe = /<a style="display: flex; align-items: center; gap: 11px;[^>]*>[\s\S]*?PEPTIDES<\/span>\s*<\/span>\s*<\/a>/;
h = h.replace(logoRe, '__LOGO__');

// 6) Navigation. The DC runtime drove ALL navigation with onclick bindings
// ({{ goShop }}, {{ goCart }}, …) that died in the static capture. Re-wire every
// interactive element to the real WordPress URLs (theme scaffolds /shop/, /learn/,
// /coa-library/; WooCommerce adds /cart/, /my-account/; header filter uses
// /shop/?category=<slug> with the catalog slugs weight-loss/energy/healing/skin/brain/stacks/supplies).

// 6a) Top nav anchors.
const NAV = { 'Shop': '/shop/', 'Learn': '/learn/', 'COAs &amp; Testing': '/coa-library/', 'Help &amp; Support': '/learn/' };
for (const [label, href] of Object.entries(NAV)) {
  h = h.split(`letter-spacing: 0.2px;">${label}</a>`).join(`letter-spacing: 0.2px;" href="${href}">${label}</a>`);
}

// 6b) Remaining dead anchors (section "view all" links + footer columns), matched by label.
const A_LINKS = {
  'View all 40+ →': '/shop/', 'Shop all →': '/shop/',
  'View COAs': '/coa-library/', 'Start learning': '/learn/', 'Explore tools': '/learn/',
  'All Peptides': '/shop/', 'Best Sellers': '/shop/', 'Blends': '/shop/?category=stacks',
  'Nasal Sprays': '/shop/', 'Accessories': '/shop/?category=supplies',
  'Peptide Learning Centre': '/learn/', 'COA Lookup': '/coa-library/',
  'Dosing Calculator': '/learn/', 'Storage &amp; Handling': '/learn/',
  'Shipping &amp; Delivery': '/learn/', 'Payment Instructions': '/learn/',
  'Returns &amp; Reships': '/learn/', 'Contact Us': '/my-account/',
};
for (const [label, href] of Object.entries(A_LINKS)) {
  // labels may be bare or wrapped in <span class="sc-interp">, with an optional trailing arrow
  const re = new RegExp(`<a style="([^"]*)">((?:<span class="sc-interp">)?${label.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}(?:</span>)?(?:\\s*→)?)</a>`, 'g');
  h = h.replace(re, `<a style="$1" href="${href}">$2</a>`);
}

// 6c) Buttons/divs/icon-spans → onclick navigation (same mechanism the DC runtime used).
const nav = (url) => ` onclick="location.href='${url}'"`;
const addOnclickToNearest = (marker, openTag, url) => {
  let from = 0;
  while (true) {
    const i = h.indexOf(marker, from);
    if (i < 0) break;
    const open = h.lastIndexOf(openTag, i);
    // only when this marker's nearest container really is that tag (not a product card)
    const cardOpen = h.lastIndexOf('<div class="scp1"', i);
    if (open >= 0 && open > cardOpen) {
      const end = h.indexOf('>', open);
      if (end > 0 && !h.slice(open, end).includes('onclick')) {
        h = h.slice(0, end) + nav(url) + h.slice(end);
      }
    }
    from = i + marker.length;
  }
};

h = h.replace('cursor: pointer;">⚲</span>', `cursor: pointer;"${nav('/shop/')}>⚲</span>`);
h = h.replace('cursor: pointer;">⚹</span>', `cursor: pointer;"${nav('/my-account/')}>⚹</span>`);
addOnclickToNearest('🛒', '<button', '/cart/');
// (the CTA labels are still the original copy at this point; step 7 swaps them to __CTA1__/__CTA2__)
addOnclickToNearest('Browse Catalog</button>', '<button', '/shop/');
addOnclickToNearest('Learn More</button>', '<button', '/learn/');
addOnclickToNearest('See All Products →</button>', '<button', '/shop/');
addOnclickToNearest('See our testing process</button>', '<button', '/coa-library/');

// Category cards → filtered shop (display names → catalog slugs).
const CATS = {
  'Healing &amp; Recovery': 'healing', 'Growth Hormone': 'energy', 'Metabolic': 'weight-loss',
  'Cognitive': 'brain', 'Cosmetic': 'skin', 'Longevity': 'stacks',
};
for (const [label, slug] of Object.entries(CATS)) {
  addOnclickToNearest(`<span class="sc-interp">${label}</span>`, '<button', `/shop/?category=${slug}`);
}

// Product cards → shop.
h = h.split('<div class="scp1"').join(`<div class="scp1"${nav('/shop/')}`);

// 6d) The static cart badge showed a hardcoded "4" — meaningless on a fresh store; hide it.
h = h.replace(/(🛒\s*<span style="position: absolute;[^"]*)"/, '$1; display: none;"');

// 7) Hero copy → tokens (defaults filled by the renderer).
h = h.replace('>Research-Grade Quality<', '>__HERO_EYEBROW__<');
h = h.replace('>Peptides That<', '>__HERO_H1__<');
h = h.replace('>Stay Grounded<', '>__HERO_H1_EM__<');
h = h.replace('Third-party HPLC-tested peptides for serious researchers. Purity you can trust, dispatched same-day from Canada.', '__HERO_SUB__');
h = h.replace('>Browse Catalog<', '>__CTA1__<');
h = h.replace('>Learn More<', '>__CTA2__<');

// 8) Neutralize remaining Anchored-Peptides-specific brand/geo/personal copy.
const NEUTRALIZE = [
  ['Anchored Peptides', '__BRAND__'],
  ['The Anchored Standard', 'The __BRAND__ Standard'],
  ['Ships From Canada', 'Fast Dispatch'],
  ['dispatched same-day from Canada', 'dispatched same-day'],
  ['Domestic fulfillment &amp; support', 'Reliable fulfillment &amp; support'],
  ['Canadian', 'In-house'],
  ['Stay true, stay anchored. God bless. 🙏', '__FOUNDER_TAGLINE__'],
  ['How We Found Peptides —', '__STORY_H1__'],
  ['and Never Looked Back', '__STORY_H2__'],
  // Founder story — replace the Anchored founders' personal narrative with neutral, editable copy.
  ['Founded by a husband and wife after having two kids and entering our mid-30s, we found ourselves fighting for energy, stamina, and ways to keep up with the busyness of parenthood. Husband, being the sole provider, worked long weeks — sometimes up to 60 hours. Wife was dealing with post-partum hair loss and skin discoloration, melasma to be exact.',
   'We started with a simple frustration: it was too hard to find research peptides you could actually trust. Inconsistent labeling, no real testing, slow shipping. We knew serious researchers deserved better.'],
  ['We came across peptides through a family friend who had seen huge benefits from doing their own research. They were healthier, happier, and had surplus energy for their kids. We dove in and began doing our own research — and never looked back. We immediately felt the impact a few weeks in and saw the changes in our bodies and energy levels.',
   'So we built a source held to a higher standard — independent HPLC and mass-spec testing on every batch, honest labeling, and fast, dependable fulfillment. What you order is exactly what arrives.'],
  ['Our mission is to share the research that helped us, in hopes that doing your own research would allow you to find the same changes we did — for the better.',
   'Our mission is straightforward: give researchers materials they can rely on, and the data to back them up — every order, every time.'],
  // Footer tagline
  ["Canada's source for third-party HPLC-tested research peptides. Shipped same-day from our In-house fulfillment centre.",
   'A trusted source for third-party HPLC-tested research peptides, shipped fast from our own fulfillment centre.'],
  ['Interac e-Transfer · Same-day dispatch · Canada-wide', 'Secure checkout · Same-day dispatch · Fast shipping'],
];
for (const [from, to] of NEUTRALIZE) h = h.split(from).join(to);

// 9) Wrap removable sections in comment markers so the renderer can strip them
// per-brand (e.g. hide the goal-category grid for strictly research-framed sites).
function wrapSection(containedText, name) {
  const at = h.indexOf(containedText);
  if (at < 0) { console.error(`wrapSection: "${containedText}" not found`); return; }
  const open = h.lastIndexOf('<section', at);
  if (open < 0) { console.error(`wrapSection: no <section before "${containedText}"`); return; }
  // find the matching </section>, accounting for nesting
  let depth = 0, i = open;
  const re = /<section\b|<\/section>/g;
  re.lastIndex = open;
  let end = -1, m;
  while ((m = re.exec(h))) {
    depth += m[0] === '</section>' ? -1 : 1;
    if (depth === 0) { end = m.index + '</section>'.length; break; }
  }
  if (end < 0) { console.error(`wrapSection: unbalanced sections for "${containedText}"`); return; }
  h = h.slice(0, open) + `<!--AP:${name}-->` + h.slice(open, end) + `<!--/AP:${name}-->` + h.slice(end);
}
wrapSection('Find your research category', 'CATEGORIES');

// 10) Bento "selling points" section — three tiles (one tall, two stacked), each an
// AI-designed image with the brand's selling point overlaid. Not part of the original
// design; injected before the founder story. The renderer strips it when a brand has
// no selling points.
const bentoTile = (n, tall) => `
      <div style="position: relative; border-radius: 18px; overflow: hidden; background: var(--ap-dark); min-height: ${tall ? '100%' : '0'};">
        <img src="__SPIMG${n}__" alt="" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block;">
        <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(20,20,14,0) 30%, rgba(20,20,14,0.78) 100%);"></div>
        <div style="position: relative; display: flex; flex-direction: column; justify-content: flex-end; height: 100%; padding: clamp(18px, 2.4vw, 30px); min-height: ${tall ? '380px' : '220px'};">
          <div style="width: 26px; height: 2px; background: var(--ap-gold); margin-bottom: 12px;"></div>
          <div style="font: 500 clamp(17px, 1.9vw, 24px) / 1.25 var(--ap-serif); color: var(--ap-cream); letter-spacing: 0.1px;">__SP${n}__</div>
        </div>
      </div>`;
const bento = `<!--AP:BENTO--><section style="background: var(--ap-bg2); border-top: 1px solid var(--ap-border); padding: clamp(48px, 7vw, 84px) 0;">
    <div style="max-width: 1280px; margin: 0px auto; padding: 0px clamp(16px, 4vw, 40px);">
      <div style="text-align: center; margin-bottom: clamp(26px, 4vw, 44px);">
        <div style="font: 600 11px var(--ap-sans); letter-spacing: 3px; color: var(--ap-muted); text-transform: uppercase; margin-bottom: 12px;">The Difference</div>
        <h2 style="font: 500 clamp(28px, 4vw, 44px) / 1.1 var(--ap-serif); color: var(--ap-ink); margin: 0px;">Why researchers choose __BRAND__</h2>
      </div>
      <div class="ap-bento" style="display: grid; grid-template-columns: 1.15fr 1fr; gap: clamp(12px, 1.6vw, 20px);">
        ${bentoTile(1, true)}
        <div style="display: grid; gap: clamp(12px, 1.6vw, 20px);">
          ${bentoTile(2, false)}
          ${bentoTile(3, false)}
        </div>
      </div>
    </div>
    <style>@media (max-width: 760px) { .ap-bento { grid-template-columns: 1fr !important; } }</style>
  </section><!--/AP:BENTO-->`;
{
  const storyAt = h.indexOf('__STORY_H1__');
  const storyOpen = storyAt >= 0 ? h.lastIndexOf('<section', storyAt) : -1;
  if (storyOpen < 0) console.error('bento: story section not found');
  else h = h.slice(0, storyOpen) + bento + '\n  ' + h.slice(storyOpen);
}

fs.writeFileSync(OUT, h);
console.error(`base template → ${OUT} (${(h.length / 1024).toFixed(0)} KB)`);
const leftover = (h.match(/rgb\(\d+, \d+, \d+\)/g) || []);
console.error('remaining rgb() literals (not tokenized):', [...new Set(leftover)].length, '→', [...new Set(leftover)].slice(0, 12).join(' '));
console.error('placeholders present:', ['__BRAND__','__LOGO__','__HERO_IMAGE__','__HERO_H1__','__HERO_H1_EM__','__HERO_SUB__','__HERO_EYEBROW__','__CTA1__','__CTA2__'].filter(p=>h.includes(p)).join(' '));
