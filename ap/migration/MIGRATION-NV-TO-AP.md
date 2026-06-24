# Copying the catalog from Natty Vision → Anchored Peptides

**You do NOT need to re-enter products by hand.** NV and AP share the exact same
catalog, so we copy it with WooCommerce's built-in Export → Import. The Anchored
Peptides theme is built to read NV's product data natively, so the move is nearly
zero-friction.

---

## TL;DR

```
NV admin → WooCommerce → Products → Export   →  nv-products.csv
python3 nv-to-ap-products.py nv-products.csv ap-products.csv
AP admin → WooCommerce → Products → Import    →  ap-products.csv
```

Images, variations, prices, SKUs, stock and descriptions all carry over
automatically. Keep the NV site online during the import so product images can
be sideloaded from it.

---

## Why it’s this easy

1. **Variations / prices / SKUs / stock / images / descriptions** — handled
   natively by WooCommerce export/import. Nothing to map.
2. **Per-product meta** (badges, taglines, spec rows, variant JSON, COA links) —
   NV stores these under `_nv_*` keys. The AP theme’s `ap_meta()` helper
   **falls back to `_nv_*` automatically**, so even a *raw* NV export renders
   correctly on AP. The transform script renames them to `_ap_*` just to keep
   the new database tidy.
3. **The COA Library** on AP queries both `_ap_coa_url` and `_nv_coa_url`, so
   migrated products show up with no extra work.

> Net effect: if you skipped the transform script entirely and imported the raw
> NV export, AP would still work. The script is about cleanliness + categories.

---

## Categories — DECIDED: keep NV's scheme (truest 1:1 clone)

The AP build has been aligned to **Natty Vision's category scheme**, so the copy
is a true 1:1: `Weight Loss, Energy, Healing, Skin, Brain, Stacks, Supplies`
(under parent `Peptides`). The AP homepage "browse by goal", shop sidebar, and
footer already reference these slugs, and the plugin's activation routine creates
them. The transform script ships with **`REMAP_CATEGORIES = False`**, so it leaves
categories untouched.

> If you ever want AP's own goal-based names instead (Metabolic, Growth Hormone,
> Healing & Recovery, …), flip `REMAP_CATEGORIES = True` in
> `nv-to-ap-products.py` (the `CATEGORY_MAP` is pre-filled) **and** tell me so I
> re-align the homepage/shop/footer slugs.

---

## Step-by-step

### 1. Export from Natty Vision
- NV admin → **WooCommerce → Products → Export**.
- ✅ Tick **“Export custom meta?”** (this includes the `_nv_*` fields — important).
- Leave columns/types at “all”. **Generate CSV** → save as `nv-products.csv`.

### 2. Transform
```bash
cd ap/migration
python3 nv-to-ap-products.py nv-products.csv ap-products.csv
```
Review the printed summary (product count, dropped columns, category remap flag).

### 3. Import into Anchored Peptides
- Make sure the **AP theme + plugin are active** and the plugin’s activation has
  created the categories (Peptides → Healing & Recovery, …).
- AP admin → **WooCommerce → Products → Import** → upload `ap-products.csv`.
- On the column-mapping screen, WooCommerce auto-maps standard columns; the
  `Meta: _ap_*` columns map to custom fields automatically.
- **Keep the NV site reachable** so `Images` URLs sideload into AP’s media library.
- Run the import.

### 4. Post-import checks
- [ ] Product count matches NV.
- [ ] A variable product (e.g. BPC-157) shows its strength **SIZE pills** with
      correct prices/stock.
- [ ] Featured products appear on the AP homepage best-sellers (mark ~10 as
      *Featured* if the grid looks thin — AP pulls best-sellers from the
      Featured flag, falling back to top-rated).
- [ ] The **COA Library** page lists products that had a COA URL.
- [ ] Category pages / shop sidebar show the expected buckets.

---

## Notes & edge cases

- **Variations:** if NV products are Variable, the export includes `variation`
  rows and the import recreates them — AP reads real WC variations first, the
  `_ap_variants`/`_nv_variants` JSON is only a fallback. No “Generate variations”
  step needed for migrated products.
- **Dropped meta:** `_nv_spec_4`, `_nv_shipping_html`, `_nv_purity_label` have no
  AP equivalent and are dropped by the script (edit `DROP_META_KEYS` to keep them).
- **Re-running:** to update an existing AP catalog later, tick “Update existing
  products” on import and match on SKU.
- **Images already in NV media:** the CSV `Images` column holds NV URLs; sideload
  pulls them once. After a successful import you can take NV offline.
