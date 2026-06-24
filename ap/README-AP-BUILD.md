# Anchored Peptides — WordPress build (site #2)

This folder contains a **drop-in WordPress theme + plugin** that turn the Anchored Peptides Claude Design into a working WooCommerce storefront. It mirrors the proven Natty Vision architecture but is built as an **improved golden master** (see “What’s improved” below).

> Built from `AP/Anchored Peptides (standalone).html`. The design’s embedded HTML was decoded, its images extracted, and its views ported to WordPress/WooCommerce templates. CSS was visually verified against the original render.

## What’s in here

```
ap/
├── anchored-peptides/                 ← THEME  (wp-content/themes/)
│   ├── style.css                      theme header (styles live in assets/css)
│   ├── functions.php                  setup, fonts, enqueues, logo, helpers (ap_*)
│   ├── header.php / footer.php        announcement marquee + nav / footer
│   ├── index.php page.php single.php 404.php
│   ├── page-coa-library.php           Template: COA Library
│   ├── page-learn.php                 Template: Learn / Knowledge Hub
│   ├── inc/
│   │   ├── woocommerce.php            Woo wrapper overrides + ap_render_product_card()
│   │   ├── meta-box.php               per-product _ap_* meta box (field registry)
│   │   ├── variations-generator.php   JSON → real WC variations (AJAX)
│   │   └── customizer.php             marquee, disclaimer, spec pill, footer blurb
│   ├── woocommerce/
│   │   ├── single-product.php         rich product page (pills, tabs, FAQ, COA, related)
│   │   ├── archive-product.php        shop: sidebar category filter + grid
│   │   └── content-product.php        product card (delegates to shared renderer)
│   └── assets/
│       ├── css/tokens.css             design tokens — SINGLE source of truth
│       ├── css/main.css               full component stylesheet
│       ├── js/main.js                 variant pills, tabs, accordion, qty, Woo sync
│       └── images/                    hero-vials.png, product-vial.png
│
└── anchored-peptides-homepage/        ← PLUGIN (wp-content/plugins/)
    ├── anchored-peptides-homepage.php main file: logo settings, helpers,
    │                                   activation scaffolder, template routing
    ├── template.php                   Home view (DATA-DRIVEN from WooCommerce)
    ├── images/                        hero-vials.png, product-vial.png
    └── woocommerce/checkout/thankyou.php  branded order-received page
```

## Design system

- **Fonts:** Newsreader (serif headings) + Hanken Grotesk (sans body) — loaded via Google Fonts.
- **Palette (tokens.css):** cream `#ECE7DA/#F4F0E6`, ink `#2C2E22`, olive `#3E412E`, dark `#33352A`, taupe `#6E6453`, blue accent `#2B5687`, rust `#A8503B`.
- **All tokens live in ONE file** (`assets/css/tokens.css`), enqueued by both the theme and the plugin. Reskin = edit ~30 variables.

## What’s improved vs. the Natty Vision build

These apply the efficiency lessons from `nv/NEW-SITE-SETUP-GUIDE.md` §7:

1. **Data-driven homepage.** Best-sellers and category cards pull live from WooCommerce (`ap_homepage_products()` / `ap_homepage_categories()`). **No hardcoded product names** — adding products auto-populates the homepage. Mark products *Featured* to control the top 10 (falls back to top-rated).
2. **Auto-create on activation.** Activating the plugin creates the product categories (with the exact slugs the templates expect), creates the **Home / COA Library / Learn** pages with their templates, sets the static front page, and sets pretty permalinks. Idempotent — safe to re-run.
3. **Centralized design tokens** (one file, not duplicated across theme/plugin/thank-you).
4. **DRY nav/footer.** The homepage uses the theme’s `header.php`/`footer.php` instead of duplicating the navbar.
5. **One product-card renderer** (`ap_render_product_card()`) reused by shop, homepage, and related products.

## Install / migration steps (for the new site)

> Full context is in `nv/NEW-SITE-SETUP-GUIDE.md`. AP-specific quick path:

1. **Upload** `anchored-peptides/` → `wp-content/themes/`, and `anchored-peptides-homepage/` → `wp-content/plugins/`.
2. **Install + activate WooCommerce**, then a payment gateway (e.g. Interac e-Transfer / PayPal).
3. **Activate the theme** “Anchored Peptides”.
4. **Activate the plugin** “Anchored Peptides Homepage”. → This auto-creates categories + Home/COA/Learn pages + front page + permalinks.
5. **Add products:** create each product, fill the **Anchored Peptides Product** meta box (badge, tagline, spec pills, **Variants JSON**, COA URL…), add a featured image, then click **“⚓ Generate variations from JSON”**.
6. **Mark ~10 products as Featured** so the homepage best-sellers grid is curated (otherwise it auto-fills with top-rated).
7. **Upload the logo** under the **Anchored Peptides** admin menu (or it uses the anchor + wordmark fallback).
8. **Assign menus** (optional — header falls back to Shop / Learn / COAs & Testing / Help & Support).
9. **Customizer → Anchored Peptides — General:** marquee phrases, disclaimer, footer blurb.
10. **Integrations & keys:** payment gateway, AIOSEO, analytics, email (Omnisend) as needed.

## ⚠️ Connection points (must line up)

- **Category slugs:** the build uses the **Natty Vision scheme** (1:1 with the source catalog): `weight-loss, energy, healing, skin, brain, stacks, supplies` under parent `peptides`. The plugin activation routine creates these; the homepage "browse by goal", shop sidebar, and footer all reference these slugs. (The AP design's goal-based names were swapped out per the migration decision to keep a true 1:1 copy.)
- **Homepage products** come from *Featured* flag / ratings — no names to match. ✅
- **`_ap_coa_url`** on a product = it appears in the COA Library.
- **Variants JSON** shape: `[{"mg":10,"price":92.99,"label":"Single vial"},{"mg":100,"price":799.71,"label":"1 box · best value","note":"Save 7%"}]`. Run “Generate variations”.

## Verification done

- ✅ HTML design decoded; 2 product images extracted (hero vials + single vial).
- ✅ **`php -l` passes on all 27 PHP files** (PHP 8.5).
- ✅ CSS visually verified in a browser against the original design — hero, categories, product cards, and the full product page (incl. the real WooCommerce add-to-cart button + quantity styling) render faithfully.
- ✅ **Adversarial multi-agent code review** (5 dimensions: WooCommerce correctness, PHP bugs, escaping/security, template↔CSS↔JS consistency, migration-compat). 28 findings → 11 confirmed by a 2-verifier panel → **all 11 fixed**, including:
  - Real WooCommerce add-to-cart button/quantity now styled (was unstyled — CSS targeted `.ap-btn` which the WC button never gets).
  - SVG logo upload now **sanitized** (strips `<script>`, `on*` handlers, `javascript:`, `<foreignObject>`) — tested against a malicious SVG.
  - Add-to-cart form shows when *any* variant is purchasable (not just the default pill).
  - Homepage best-sellers grid guaranteed to fill (no longer depends on a rating-meta join); dead `wp_list_pluck` removed; negative-lookup cache fixed.
  - Variations generator handles `low`/backorder stock and honors a migrated `_nv_` SKU prefix.
  - Admin meta box pre-fills from migrated `_nv_` values.

## Known caveats / TODO at migration

- **Product imagery:** only the two design images are bundled. Each product needs its own vial photo (the original site used an “NV Bulk Image Uploader”-style tool — consider the same).
- **Fonts** load from Google Fonts CDN (the bundled woff2s were not shipped; the CDN versions are identical).
- **Newsletter + thumbnails** on the product page are presentational; wire the newsletter to Omnisend if desired.
- Run `php -l` on each PHP file on the server (or activate on staging first).
