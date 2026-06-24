#!/usr/bin/env python3
"""
merge-nv-catalog.py
Build the final Anchored Peptides import CSV from:
  1. nv-base-export.csv      (WooCommerce product export — products+variations, no protected meta)
  2. nv-meta-1/2/3.json      (harvested _nv_* protected meta, keyed by product ID)

Injects the harvested meta as `Meta: _ap_*` columns (renaming _nv_ -> _ap_, dropping
keys with no AP equivalent), keeps NV categories, and writes ap-products-final.csv.
"""
import json, csv, io, sys, glob

DROP = {'_nv_spec_4', '_nv_shipping_html', '_nv_purity_label'}  # no AP equivalent

def load_json_loose(path):
    raw = open(path).read()
    v = json.loads(raw)
    if isinstance(v, str):          # filename-save sometimes double-encodes
        v = json.loads(v)
    return v

# --- load harvested meta ---
meta = {}
for f in sorted(glob.glob('nv-meta-*.json')):
    d = load_json_loose(f)
    for pid, rec in d.items():
        clean = {k: v for k, v in rec.items() if not k.startswith('__') and v not in ('', None)}
        if clean:
            meta[str(pid)] = clean

# --- determine the _ap_ meta columns to add ---
nv_keys = set()
for rec in meta.values():
    nv_keys |= set(rec.keys())
nv_keys -= DROP
ap_cols = sorted('_ap_' + k[len('_nv_'):] for k in nv_keys)   # _nv_badge -> _ap_badge

# --- load base CSV ---
base = open('nv-base-export.csv').read()
rows = list(csv.reader(io.StringIO(base)))
hdr = rows[0]

# WooCommerce export omitted the "Attribute N ..." headers even though variable-
# product rows carry those 5 values per attribute. Normalize: extend the header
# with the standard attribute column names and pad every row to equal width.
maxw = max(len(r) for r in rows)
extra = maxw - len(hdr)
if extra > 0:
    attr_names, g = [], 1
    while len(attr_names) < extra:
        attr_names += [f'Attribute {g} name', f'Attribute {g} value(s)',
                       f'Attribute {g} visible', f'Attribute {g} global', f'Attribute {g} default']
        g += 1
    hdr = hdr + attr_names[:extra]
rows = [r + [''] * (maxw - len(r)) for r in rows]   # pad header + all rows to maxw
rows[0] = hdr
id_idx = next((i for i, c in enumerate(hdr) if c.strip().lower() == 'id'), 0)

# --- build new header + rows ---
new_hdr = hdr + ['Meta: ' + c for c in ap_cols]
out = [new_hdr]
filled = 0
for r in rows[1:]:
    if not any(c.strip() for c in r):
        continue
    pid = r[id_idx].strip() if len(r) > id_idx else ''
    extra = []
    rec = meta.get(pid, {})
    if rec:
        filled += 1
    for c in ap_cols:                       # c like _ap_badge
        nv_key = '_nv_' + c[len('_ap_'):]
        extra.append(rec.get(nv_key, ''))
    out.append(r + extra)

with open('ap-products-final.csv', 'w', newline='') as f:
    csv.writer(f).writerows(out)

print(f"✅ ap-products-final.csv written")
print(f"   data rows: {len(out)-1}")
print(f"   products with harvested meta: {filled}")
print(f"   _ap_ meta columns added ({len(ap_cols)}): " + ', '.join(c[4:] for c in ap_cols))
# quick COA / badge coverage report
coa = sum(1 for rec in meta.values() if rec.get('_nv_coa_url'))
badge = sum(1 for rec in meta.values() if rec.get('_nv_badge'))
variants = sum(1 for rec in meta.values() if rec.get('_nv_variants'))
print(f"   coverage -> coa_url:{coa}  badge:{badge}  variants_json:{variants}  (of {len(meta)} products)")
