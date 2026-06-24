# Building a New Peptide Store — Setup & Scaling Guide

> A complete blueprint for standing up a new WooCommerce peptide store from the Natty Vision theme + custom plugins, plus a playbook for making sites #3, #4, #5… progressively faster.
>
> **Import into Notion:** drag this `.md` file into a Notion page (or *Import → Markdown*). Headings, tables, and checkboxes convert automatically.

---

## 0. TL;DR — The one thing to understand

The folders you have are the **custom code layer only** (~15–20% of a live store). A working site is **four layers**:

| Layer | What it is | Do you have it? |
|---|---|---|
| 1. Custom code | Theme + 10 custom plugins | ✅ Yes (exported) |
| 2. Runtime | WordPress + WooCommerce + ~40 third-party plugins (several paid) | ❌ Must install |
| 3. Content | Products, categories, pages, media, menus | ❌ Must create |
| 4. Config & keys | Woo settings, permalinks, Omnisend/AffiliateWP/payment keys | ❌ Must configure |

**Dropping the folders into a fresh WordPress gives you a site that loads but looks broken** (empty shop, dead homepage cards, empty COA library, non-functional quiz) until Layers 2–4 are done.

---

## 1. Architecture — how this site is built

- **Theme (`natty-vision`)** renders everything *except* the homepage: shop archive, single product, COA library, header/footer, blog stubs, WooCommerce overrides.
- **Plugin (`natty-vision-homepage`)** renders the marketing homepage (one big template) + the logo admin setting + the branded thank-you page + shared product-lookup helpers.
- **9 additional custom plugins** layer on quiz, affiliate, SEO, revenue reporting, image upload, checkout, and influencer landing pages.
- **Design system** = warm beige palette + 3 fonts (Instrument Serif, Neue Montreal, DM Mono). The CSS tokens (`:root{--bg:#f2f0eb…}`) are **duplicated in several files** — see §7 efficiency note.
- **Product data model** = a custom meta box (`_nv_*` fields), no ACF dependency, with a JSON→real-WooCommerce-variations generator.

---

## 2. Asset inventory — what you have vs. what you need

### 2A. Custom code you HAVE ✅

**Theme:** `natty-vision/` (active theme, v1.3.0)

**Custom plugins (10):**

| Plugin | Role | In your folders |
|---|---|---|
| Natty Vision Homepage | Homepage template + logo setting | ✅ original |
| Natty Vision Quiz Popup | 3-step quiz → Omnisend → `QUIZ10` coupon | ✅ exported |
| NV Affiliate Coupon Sync & Portal Restyle | AffiliateWP coupon sync + portal | ✅ exported |
| Custom Checkout | Checkout field/sync customizations | ✅ exported |
| NattyVision Payment Method Revenue | Revenue-by-payment reporting | ✅ exported |
| NV Bulk Image Uploader | Bulk product imagery | ✅ exported |
| Natty Vision Brand SEO | Brand schema/SEO | ✅ exported |
| Connor Landing Template | Influencer landing page template | ✅ exported |
| NV — Connor Sinann Guide | Influencer landing | ✅ exported |
| NV — Devin Ascension Guide | Influencer landing | ✅ exported |

> Exported plugins live in `nv/exported-plugins/`. **Media these reference (images in `wp-content/uploads`) is NOT included.**

### 2B. Third-party plugins you NEED ❌

Essential = site breaks without it. 💲 = paid license required.

| Plugin | Purpose | Essential? | 💲 |
|---|---|---|---|
| **WooCommerce** | E-commerce core | **Essential** | Free |
| Payment gateway (PayPal + **Interac e-Transfer / EMT**) | Take payment | **Essential** | Free/varies |
| Advanced Custom Fields (+ ACF Photo Gallery Field) | Used across the site | Essential | Free/Pro |
| All in One SEO | Meta/OG tags (theme defers to it) | Essential | Free/Pro |
| Omnisend for WooCommerce | Email + powers quiz & back-in-stock | High | Free tier |
| AffiliateWP (+ Affiliate Area Tabs, Portal, Recurring Referrals) | Affiliate program | Optional | 💲 |
| CheckoutWC | Custom checkout UX | Optional | 💲 |
| YITH WooCommerce Subscription / Wishlist / Compare | Subs + wishlist + compare | Optional | 💲 |
| Advanced Coupons + Smart Coupons | Coupon logic | Optional | 💲 |
| Product Tabs for WooCommerce | Extra product tabs | Optional | Free |
| PDF Invoices & Packing Slips | Order PDFs | Optional | Free |
| MonsterInsights | Google Analytics | Optional | Free/Pro |
| WP Mail SMTP | Reliable email delivery | Recommended | Free |
| Akismet / Jetpack | Spam / utilities | Optional | Free |
| WPvivid Backup | Backups | Recommended | Free |
| One Click Demo Import | **Seed content fast** (see §7) | Recommended | Free |
| Page builders: Elementor, Slider Revolution, Salient/WPBakery suite | Only if you reuse builder pages | Optional | 💲 |

> The live site runs **~60 active plugins total**. Most of the Salient/WPBakery/Elementor/Slider stack is **legacy from the site's original theme** and is *not* required by the `natty-vision` custom theme. For a clean new build you can likely skip them.

### 2C. Runtime / hosting requirements ❌

| Requirement | Value (from live site) |
|---|---|
| WordPress | 7.0 (use current stable) |
| PHP | ≥ 7.4 (theme requirement; 8.1+ recommended) |
| Database | MySQL / MariaDB |
| Web server | Apache or Nginx |
| HTTPS / SSL | **Required** for checkout |
| Permalinks | Pretty (live uses *Day and name*; *Post name* also fine) |

---

## 3. ⚠️ Hardcoded "connection points" — these MUST line up

These couplings are baked into the code. If they don't match, parts of the site silently break. **This is the #1 cause of a "why is my homepage empty" problem.**

1. **Category slugs are hardcoded** in `archive-product.php` and the homepage. You must create product categories with these exact slugs:
   `weight-loss`, `energy`, `healing`, `skin`, `brain`, `stacks` (and optionally `supplies`), ideally under a parent `peptides`.
2. **The homepage references products by exact NAME** — e.g. `nv_product_url('Retatrutide')`, `nv_product_price('BPC-157')`, `'KLOW Blend'`. If a product with that name doesn't exist, the card falls back to `/shop/` with no price. → For a new catalog, **either create products with those names or edit the homepage template** (better: data-drive it — see §7).
3. **Product meta keys** (`_nv_badge`, `_nv_eyebrow`, `_nv_tagline`, `_nv_spec_1..4`, `_nv_variants`, `_nv_unit`, `_nv_coa_url`, `_nv_coa_lot`, `_nv_coa_purity`, `_nv_specs_html`, `_nv_shipping_html`, `_nv_storage_html`, `_nv_disclaimer`) must be filled per product or sections fall back/hide.
4. **`_nv_coa_url` is the trigger for the COA Library** — a product only appears there if it has this set.
5. **Variations** require clicking **"⚡ Generate variations from JSON"** on each product (converts to Variable + creates per-strength SKUs/price/stock).
6. **Shared option `nv_logo_id`** links the theme logo and homepage/plugin logo.
7. **Pages + templates + a static front page** must be set (see §5).
8. **Integrations assumed present:** Omnisend (`window.omnisend` for quiz + back-in-stock), AIOSEO (meta tags). Quiz needs an Omnisend API key configured under **Tools → Quiz Popup Settings**.

---

## 4. The product data model (reference)

Each product carries a custom meta box ("Natty Vision Product"). Key fields:

| Field key | Meaning |
|---|---|
| `_nv_badge` | Featured pill (e.g. "Top seller") |
| `_nv_eyebrow` | Small uppercase tagline (falls back to category) |
| `_nv_title_em` | Italic accent word in the title |
| `_nv_tagline` | Under-title line (falls back to short description) |
| `_nv_spec_1..4` | Up to 4 checkmark spec rows; `{mg}` injects active strength |
| `_nv_unit` | Strength unit: mg / ml / iu / mcg / g |
| `_nv_variants` | **JSON** array: `[{"mg":5,"price":69.99,"sku":"","stock":"in"}]` |
| `_nv_sku_prefix` | SKU prefix (e.g. `MTSC` → `MTSC10`) |
| `_nv_purity_label`, `_nv_price_suffix` | Display labels |
| `_nv_coa_url` | Kovera (lab) verify link → required for COA Library |
| `_nv_coa_lot` / `_nv_coa_purity` / `_nv_coa_tested` | COA card metadata |
| `_nv_specs_html` / `_nv_shipping_html` / `_nv_storage_html` | Optional product tab content |
| `_nv_disclaimer` | Per-product disclaimer override |

**Site-wide defaults** live in **Customizer → Natty Vision — General**: announcement bar, default disclaimer, consistent feature line.

---

## 5. Step-by-step: stand up a NEW site (checklist)

### Phase 1 — Infrastructure
- [ ] Provision hosting (PHP ≥ 7.4, MySQL), domain, SSL
- [ ] Install WordPress
- [ ] **Settings → Permalinks** → set to *Post name* (or *Day and name*)

### Phase 2 — Plugins & theme
- [ ] Install + activate **WooCommerce**
- [ ] Run the WooCommerce setup wizard (store address, currency, payment, shipping, tax)
- [ ] Install required plugins from §2B (at minimum: payment gateway, ACF, AIOSEO, Omnisend)
- [ ] Upload + activate the **`natty-vision` theme**
- [ ] Upload + activate the **10 custom plugins**

### Phase 3 — Taxonomy & catalog
- [ ] Create product categories with the **exact slugs** from §3.1
- [ ] Create products; fill the `_nv_*` meta box; add featured images
- [ ] Click **"Generate variations from JSON"** on each variable product
- [ ] Set `_nv_coa_url` on products that should appear in the COA Library

### Phase 4 — Pages & navigation
- [ ] Create a **Homepage** page → Template = *Natty Vision Homepage* → set as static front page (**Settings → Reading**)
- [ ] Create a **COA Library** page → Template = *COA Library*
- [ ] Confirm WooCommerce pages exist: **Shop, Cart, Checkout, My account**
- [ ] (If using affiliates) **Affiliate Area / Login / Registration** pages
- [ ] Content pages: **About, FAQ, Refund and Returns Policy**, Shipping
- [ ] Build the primary menu + footer menu
- [ ] Upload logo (**Natty Vision** admin menu) → sets `nv_logo_id`

### Phase 5 — Integrations & keys
- [ ] **Tools → Quiz Popup Settings** → add Omnisend API key + list
- [ ] Configure AIOSEO (titles, sitemap, schema)
- [ ] Configure payment gateway keys (PayPal / Interac / etc.)
- [ ] (Optional) AffiliateWP, MonsterInsights/GA, WP Mail SMTP

### Phase 6 — Polish & launch
- [ ] **Customizer → Natty Vision — General**: announcement bar, disclaimer, feature line
- [ ] Test: homepage cards resolve, shop filters, product variants switch price/stock, COA library populates, quiz captures email, checkout completes
- [ ] Set up backups (WPvivid)
- [ ] Go live

---

## 6. Reference: how the original (Natty Vision) is configured

| Setting | Value |
|---|---|
| Active theme | Natty Vision (v1.3.0) |
| Front page | Static page "Homepage" |
| Permalinks | `/%year%/%monthnum%/%day%/%postname%/` |
| Base country | Canada — Ontario |
| Currency | USD ($) |
| Selling to | All countries |
| Payments | PayPal + Interac e-Transfer/EMT |
| Categories | parent `peptides` → `brain, energy, healing, skin, stacks, weight-loss` + `supplies` |
| Product count | ~40–45 |
| WordPress | 7.0 |
| Active plugins | ~60 |

Key content pages present: Homepage, COA Library, Cart, Checkout, My account, Affiliate Area/Login/Registration, About, FAQ, Refund and Returns Policy, plus influencer landing pages (e.g. `90dayconnor`) and an Interac thank-you page (`EThankYou`).

---

## 7. ⚡ Efficiency & scaling playbook (sites #3, #4, #5…)

The goal: turn a multi-day manual build into **"fill in a config + a product list → run one process → 90% live."** Do the one-time refactors first; they pay off on every subsequent site.

### 7.1 One-time refactors (do once, benefit forever)
1. **Centralize the design system.** Pull the `:root{}` tokens + font links into ONE shared stylesheet enqueued everywhere (theme, homepage plugin, thank-you page). A new brand becomes: edit ~25 CSS variables + swap fonts + logo. *(Currently duplicated in 3+ files.)*
2. **Data-drive the homepage.** Replace hardcoded product names (`nv_product_url('Retatrutide')`, KLOW, etc.) with WooCommerce queries (featured / by-category / a "bestseller" tag). This **eliminates connection point §3.2** — adding products auto-populates the homepage.
3. **Auto-create on plugin activation.** On activate: register the product categories (slugs always match), create the Homepage + COA pages and assign templates, set the static front page. Removes the most error-prone manual steps.
4. **Parameterize brand constants.** Move lab name (Kovera), coupon code, base URLs, disclaimer, social links into one config (extend the existing Customizer pattern). Removes hardcoded "Natty Vision" strings.
5. **Rename prefixes per brand** (`nv_` / `_nv_` → new brand) via find-and-replace, or keep generic.

### 7.2 Seed content automatically
- The original already uses **One Click Demo Import** + **NV Bulk Image Uploader** — replicate this. Ship a **demo-content package** (products + meta + images + pages) so a fresh install populates itself.
- **WooCommerce CSV import** maps custom fields (`meta:_nv_badge`, `meta:_nv_variants`, …). One `products.csv` = the whole catalog, including the JSON variants column.

### 7.3 Automate provisioning with WP-CLI (the real "minimal human interaction" path)
A single script can do almost all of Phases 1–5:
```bash
wp core install ...
wp plugin install woocommerce --activate
wp theme activate <brand-theme>
wp plugin activate <custom-plugins...>
wp term create product_cat "Weight Loss" --slug=weight-loss --parent=<peptides_id>
wp import products.csv           # or: wp wc product create ...
wp post create --post_type=page --page_template=... --post_title="Homepage"
wp option update show_on_front page && wp option update page_on_front <id>
wp rewrite structure '/%postname%/' --hard
# Run the JSON→variations step headlessly:
wp eval 'nv_generate_variations_for_product($pid, json_decode($json,true));'
```
Wrap it in **Docker Compose** (WP + DB + an entrypoint that runs the script) → each new site is `docker compose up` + point a domain at it.

### 7.4 The AI-prompt pipeline ("if I know the correct prompts")
Per-site inputs are all generatable text/data:
- **Prompt → `brand.json`**: name, palette (the ~25 tokens), fonts, niche, lab name, coupon.
- **Prompt → `products.csv`**: names, categories, copy, taglines, specs, prices, variants JSON — all `_nv_` fields.
- **Prompt → tailored WP-CLI script** for that brand.

Steady state: *describe the new brand + niche → AI emits `brand.json` + `products.csv` → provisioner runs → site is 90% live.*

### 7.5 Honest blockers to true zero-touch
These always need a human/account: **payment gateway keys**, **domain + DNS + SSL**, **real product photography**, **legal pages review**, and **per-site integration keys** (Omnisend, etc.).

### 7.6 Recommended order of operations
1. Refactor the existing folders (§7.1) → a reusable, reskinnable starter.
2. Build the demo importer + `products.csv` schema (§7.2).
3. Build the WP-CLI + Docker provisioner (§7.3).
4. Per new site: generate `brand.json` + `products.csv` via prompts (§7.4) → run provisioner → finish keys/payment/legal by hand.

---

## 8. When you bring the new design (next step)

To make the new theme/plugins connect on the first try, provide:
1. **Product catalog** (names, categories, prices, strengths/variants, COA links) — even rough; lets me seed + match the homepage.
2. **Which features carry over** — affiliate? quiz? subscriptions? COA library? (each implies specific plugins/config).
3. **Brand basics** — name, logo, palette/fonts (the design covers most).
4. **How hands-off** you want setup — manual, or auto-create-on-activation + demo importer.

---

## Appendix A — Exported custom plugin file map

```
nv/exported-plugins/
├── nattyvision-quiz-popup/            nattyvision-quiz-popup.php (140 KB)
├── natty-affiliate-coupon-sync/       natty-affiliate-coupon-sync.php (333 KB)
├── custom-checkout/                   custom-checkout-sync-settings.php (42 KB)
├── nattyvision-payment-revenue/       nattyvision-payment-revenue.php (12 KB)
├── nv-image-uploader/                 nv-image-uploader.php (20 KB)
├── nattyvision-brand-seo/             nattyvision-brand-seo.php (4.6 KB)
├── connor-landing-template/           connor-landing-template.php + templates/connor-landing.php + readme.txt
├── nv-connorsinann-landing/           nv-connorsinann-landing.php + landing.html (1.2 MB)
└── devin-landing/                     nv-devin-landing.php + landing.html (114 KB)
```

## Appendix B — Theme file map

```
natty-vision/
├── functions.php            setup, enqueues, logo, cart fragment, SEO cleanup
├── header.php / footer.php
├── style.css                theme header
├── inc/
│   ├── woocommerce.php       Woo wrapper overrides, share-button removal
│   ├── meta-box.php          the _nv_* product meta box (field registry)
│   ├── variations-generator.php  JSON → real WC variations (AJAX)
│   └── customizer.php        announce bar, disclaimer, feature line
├── woocommerce/
│   ├── single-product.php    rich product page (pills, COA, back-in-stock)
│   ├── archive-product.php   shop grid + category filter pills
│   └── content-product.php   product card
├── page-coa-library.php      COA Library template
├── assets/css/main.css       design system (~23 KB)
└── assets/js/main.js         variant pills + tabs

natty-vision-homepage/  (plugin)
├── natty-vision-homepage.php  template registration + logo setting + product helpers
├── template.php              the homepage (~62 KB, inline CSS + hardcoded sections)
├── images/                   hero, molecule, shipping, purity, support, glass
└── woocommerce/checkout/thankyou.php  branded order-received page
```
