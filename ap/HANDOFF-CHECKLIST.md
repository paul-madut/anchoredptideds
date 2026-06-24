# Anchored Peptides — Client Handoff Checklist

Gap analysis of **anchoredpeptides.com** vs. the Natty Vision reference store, as of the migration. Grouped by priority so your client (who has set up WooCommerce before) can work straight down the list.

---

## ✅ Already done (no action needed)
- **Theme + Homepage plugin** installed & active (full storefront design)
- **42 products + 20 variations** imported with all custom meta (badges, taglines, specs, variant pricing, COA data)
- **Product categories** in the NV scheme (Weight Loss, Energy, Healing, Skin, Brain, Stacks, Supplies)
- **COA Library** auto-populated (7 products), **homepage best-sellers**, **navbar**
- **WooCommerce core pages** assigned: Cart (11), Checkout (12), My account (13) ✓
- **Currency**: USD ✓ (matches NV)
- Active commerce plugins: WooCommerce, **WooPayments**, **Omnisend**, MailPoet, Jetpack, Akismet

---

## 🔴 Tier 1 — Required before the store can take a real order

### 1. Payments — **yes, this needs a bank account** 💳
- **WooPayments is installed but onboarding is NOT finished.** To go live it needs business details + identity verification + a **bank account for payouts** (it's Stripe-backed). Go to **Payments → Finish setup**.
- **Decision for parity with NV:** Natty Vision does *not* use WooPayments — it uses **Interac e-Transfer + PayPal** (Canadian). To match NV exactly, instead install the **"Interac e-Transfer / EMT Gateway for WooCommerce"** plugin + enable **PayPal**, and skip WooPayments. Interac links to a bank *email*, not a card processor.
- Either way: **the client must connect a bank/payment account.** Pick one path (WooPayments **or** Interac+PayPal) — don't run both.

### 2. Store address + base country 🏠
- Currently **empty / United States – California**. NV is **Canada – Ontario**.
- Set **WooCommerce → Settings → General**: real Canadian fulfillment address + base country. This drives tax + shipping rates.

### 3. Shipping 📦
- **No shipping zones/methods are configured** (only an empty "Rest of the world").
- Add at least a **Canada** zone with a method (Flat rate and/or **Free shipping over $X** — NV uses free shipping thresholds). Add an international zone if shipping abroad.

### 4. Tax 🧾
- Not configured. Decide: charge tax (set up tax rates / enable automated tax) **or** treat prices as tax-included / tax-exempt. Match however NV handles it.

### 5. Order emails ✉️
- Set the **"from" name & email** (WooCommerce → Settings → Emails) and send a test order.
- NV uses **WP Mail SMTP** for reliable delivery — recommend installing it (or configuring the host's SMTP) so order/receipt emails don't land in spam.

---

## 🟠 Tier 2 — Content & legal (needed for a credible, compliant store)
- **Legal pages**: Terms & Conditions (none assigned in WooCommerce yet), Refund/Returns Policy, Privacy Policy, Shipping Policy. NV has these.
- **Logo**: upload the brand logo under the **Anchored Peptides** admin menu (currently using the built-in anchor SVG fallback).
- **Product images**: 23 products imported without a photo (they had none on NV). Add images, or NV's **"NV Bulk Image Uploader"**-style tool.
- **COA links**: the 7 COA URLs currently point to **NV's Kovera Labs batches**. If the client ships their own batches, swap these for their lab links (per product → `_ap_coa_url`).
- **Announcement marquee / disclaimer / footer blurb**: tweak under **Appearance → Customize → Anchored Peptides — General**.

---

## 🟡 Tier 3 — NV feature parity (optional — client chooses what they want)
These are features the NV store has that AP does **not** yet. None are required for a working store.

| NV feature | Plugin(s) on NV | On AP? | Paid? |
|---|---|---|---|
| Affiliate program | AffiliateWP suite + custom "NV Affiliate Coupon Sync" | ❌ | 💲 |
| Quiz popup (10% off) | "NV Quiz Popup" + Omnisend | Omnisend ✓, quiz ❌ | — |
| Subscriptions | YITH WooCommerce Subscription | ❌ | 💲 |
| Wishlist / Compare | YITH Wishlist / Compare | ❌ | mixed |
| Advanced coupons / store credit | Advanced Coupons, Smart Coupons | ❌ | 💲 |
| SEO (titles, schema, sitemap) | All in One SEO | ❌ (Jetpack only) | free/Pro |
| PDF invoices / packing slips | PDF Invoices & Packing Slips | ❌ | free |
| Analytics | MonsterInsights (Google Analytics) | ❌ | free/Pro |
| Email marketing | Omnisend | ✅ (also MailPoet) | free tier |
| Custom SEO/revenue/landing pages | NV Brand SEO, Payment Method Revenue, influencer landing plugins | ❌ (bespoke) | — |

> If the client wants these, install the same plugins and re-create the config. The bespoke NV plugins were exported to `nv/exported-plugins/` for reference.

---

## Suggested handoff order
1. Hand over admin access + this checklist.
2. Client completes **Tier 1** (payments/bank, address, shipping, tax, emails) → store can transact.
3. Add **Tier 2** content/legal → store is credible & compliant.
4. Pick **Tier 3** features as the business needs them.

Everything in "Already done" is live now; the old homepage-only plugin and Assembler theme remain installed but inactive (safe to delete once the client is happy).
