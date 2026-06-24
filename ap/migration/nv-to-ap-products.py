#!/usr/bin/env python3
"""
nv-to-ap-products.py
====================
Transform a WooCommerce product-export CSV from the Natty Vision site into one
ready to import into the Anchored Peptides site.

WHY THIS EXISTS
---------------
NV and AP share the exact same catalog, so you should NOT re-enter products by
hand. Instead:

    1. On the NV site:  WooCommerce → Products → Export → (tick "Export custom
       meta?") → Generate CSV.  Save it as  nv-products.csv
    2. Run this script:  python3 nv-to-ap-products.py nv-products.csv ap-products.csv
    3. On the AP site:   WooCommerce → Products → Import → upload ap-products.csv
       (leave "Update existing" off for a first import; image URLs are
       sideloaded automatically from the NV site, so keep NV online until done).

WHAT IT DOES
------------
* Renames custom-meta columns  `Meta: _nv_X`  →  `Meta: _ap_X`  so the AP
  database is clean.  (The AP theme ALSO reads `_nv_` keys as a fallback, so
  this rename is optional — but it keeps the new site tidy.)
* Optionally remaps product CATEGORIES from NV's scheme to AP's scheme
  (see CATEGORY_MAP + REMAP_CATEGORIES below). This is the one real decision:
    - REMAP_CATEGORIES = True  → AP storefront uses its own goal-based
      categories (Healing & Recovery, Growth Hormone, …).  [AP design intent]
    - REMAP_CATEGORIES = False → AP keeps NV's categories verbatim
      (Weight Loss, Energy, Healing, …).  [truest 1:1 clone]
* Leaves everything else (variations, prices, SKUs, stock, images, descriptions)
  untouched — WooCommerce export/import handles those natively.

This script has no dependencies beyond the Python standard library.
"""

import csv
import sys

# ──────────────────────────────────────────────────────────────────────────
# CONFIG — edit these two blocks to taste.
# ──────────────────────────────────────────────────────────────────────────

# Keep NV's categories on the AP site (truest 1:1 clone) — the chosen setup.
# Flip to True (and edit CATEGORY_MAP) if you later want AP's own scheme.
REMAP_CATEGORIES = False

# NV leaf category  →  AP leaf category.  Only used when REMAP_CATEGORIES=True.
# (Left side = the category NAME as it appears on the NV export.)
CATEGORY_MAP = {
    "Weight Loss": "Metabolic",
    "Energy":      "Growth Hormone",
    "Healing":     "Healing & Recovery",
    "Skin":        "Cosmetic",
    "Brain":       "Cognitive",
    "Stacks":      "Blends",
    "Supplies":    "Accessories",
    # "Longevity", "Nasal Sprays", "Capsules" are AP-only — no NV source.
}

# AP parent category that all leaves nest under (matches the AP activation routine).
AP_PARENT = "Peptides"

# Also rename the `Meta: _nv_*` columns to `Meta: _ap_*`?  (Recommended: True.)
RENAME_META = True

# NV meta keys that have NO Anchored Peptides equivalent — drop them so they
# don't clutter the AP database.  (_nv_spec_4 = NV's 4th "feature" spec row;
# AP shows only 3 spec pills.  _nv_shipping_html = NV had a Shipping tab; AP
# folds shipping into its FAQ accordion.  _nv_purity_label = unused in AP.)
DROP_META_KEYS = {"_nv_spec_4", "_nv_shipping_html", "_nv_purity_label"}

# ──────────────────────────────────────────────────────────────────────────


def remap_category_cell(cell):
    """WooCommerce 'Categories' cells look like:
        'Peptides > Weight Loss, Peptides > Energy'
    Remap each leaf via CATEGORY_MAP and re-nest under AP_PARENT.
    """
    if not cell:
        return cell
    out = []
    for entry in cell.split(","):
        entry = entry.strip()
        if not entry:
            continue
        leaf = entry.split(">")[-1].strip()
        if leaf == AP_PARENT or leaf.lower() == "peptides":
            out.append(AP_PARENT)
            continue
        mapped = CATEGORY_MAP.get(leaf, leaf)
        out.append(f"{AP_PARENT} > {mapped}")
    # de-dupe preserving order
    seen, deduped = set(), []
    for c in out:
        if c not in seen:
            seen.add(c); deduped.append(c)
    return ", ".join(deduped)


def transform_header(col):
    if RENAME_META and col.startswith("Meta: _nv_"):
        return "Meta: _ap_" + col[len("Meta: _nv_"):]
    return col


def main():
    if len(sys.argv) < 3:
        print("usage: python3 nv-to-ap-products.py <nv-products.csv> <ap-products.csv>")
        sys.exit(1)
    src, dst = sys.argv[1], sys.argv[2]

    with open(src, newline="", encoding="utf-8-sig") as f:
        reader = csv.reader(f)
        rows = list(reader)
    if not rows:
        print("Empty CSV."); sys.exit(1)

    header = rows[0]
    # Identify columns to drop (unmapped NV meta) and the Categories column.
    drop_idx = set()
    cat_idx = None
    for i, col in enumerate(header):
        if col.startswith("Meta: ") and col[len("Meta: "):] in DROP_META_KEYS:
            drop_idx.add(i)
        if col.strip().lower() in ("categories", "category"):
            cat_idx = i

    new_header = [transform_header(c) for i, c in enumerate(header) if i not in drop_idx]

    out_rows = [new_header]
    for row in rows[1:]:
        if not any(cell.strip() for cell in row):
            continue
        if REMAP_CATEGORIES and cat_idx is not None and cat_idx < len(row):
            row = list(row)
            row[cat_idx] = remap_category_cell(row[cat_idx])
        out_rows.append([c for i, c in enumerate(row) if i not in drop_idx])

    with open(dst, "w", newline="", encoding="utf-8") as f:
        csv.writer(f).writerows(out_rows)

    print(f"✅ Wrote {dst}")
    print(f"   products: {len(out_rows) - 1}")
    print(f"   meta columns renamed _nv_→_ap_: {RENAME_META}")
    print(f"   categories remapped: {REMAP_CATEGORIES}")
    if drop_idx:
        print(f"   dropped {len(drop_idx)} unmapped meta column(s): "
              + ", ".join(sorted(header[i] for i in drop_idx)))


if __name__ == "__main__":
    main()
