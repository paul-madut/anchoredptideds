// One-time import: seed the `products` master catalog from the WooCommerce CSV.
// Usage: node scripts/import-products.mjs   (reads .env.local; idempotent — upserts by wc_id)
import fs from 'node:fs';
import path from 'node:path';

const env = Object.fromEntries(
  fs.readFileSync(path.join(process.cwd(), '.env.local'), 'utf8')
    .split('\n').filter((l) => l.includes('=') && !l.startsWith('#'))
    .map((l) => [l.slice(0, l.indexOf('=')).trim(), l.slice(l.indexOf('=') + 1).trim()]),
);
const BASE = env.NEXT_PUBLIC_SUPABASE_URL;
const KEY = env.SUPABASE_SERVICE_ROLE_KEY;
const CSV = path.resolve(process.cwd(), '..', 'ap', 'migration', 'ap-products-final.csv');

/** RFC-4180 CSV parser (quoted fields, escaped quotes, embedded newlines). */
function parseCsv(text) {
  const rows = [];
  let row = [], field = '', inQ = false;
  for (let i = 0; i < text.length; i++) {
    const c = text[i];
    if (inQ) {
      if (c === '"') { if (text[i + 1] === '"') { field += '"'; i++; } else inQ = false; }
      else field += c;
    } else if (c === '"') inQ = true;
    else if (c === ',') { row.push(field); field = ''; }
    else if (c === '\n' || c === '\r') {
      if (c === '\r' && text[i + 1] === '\n') i++;
      row.push(field); field = '';
      if (row.length > 1 || row[0] !== '') rows.push(row);
      row = [];
    } else field += c;
  }
  if (field !== '' || row.length) { row.push(field); rows.push(row); }
  return rows;
}

const raw = parseCsv(fs.readFileSync(CSV, 'utf8'));
const headers = raw[0];
const records = raw.slice(1).map((r) => Object.fromEntries(headers.map((h, i) => [h, r[i] ?? ''])));
console.log(`parsed ${records.length} rows, ${headers.length} columns`);

// Display price: own price, or the min across a variable product's variations.
const minVariation = {};
for (const r of records) {
  if (r.Type !== 'variation') continue;
  const pid = r.Parent.replace(/^id:/, '');
  const p = parseFloat(r['Regular price']);
  if (!isNaN(p) && (!(pid in minVariation) || p < minVariation[pid])) minVariation[pid] = p;
}

const rows = records.map((r, i) => ({
  position: i,
  wc_id: r.ID,
  type: r.Type,
  sku: r.SKU || null,
  name: r.Name,
  parent_wc_id: r.Type === 'variation' ? r.Parent.replace(/^id:/, '') : null,
  categories: r.Categories || null,
  price: r['Regular price'] || (minVariation[r.ID] != null ? String(minVariation[r.ID]) : null),
  data: r,
  enabled: true,
}));

const res = await fetch(`${BASE}/rest/v1/products?on_conflict=wc_id`, {
  method: 'POST',
  headers: {
    apikey: KEY, Authorization: `Bearer ${KEY}`,
    'Content-Type': 'application/json',
    Prefer: 'resolution=merge-duplicates,return=minimal',
  },
  body: JSON.stringify(rows),
});
if (!res.ok) { console.error('insert failed:', res.status, (await res.text()).slice(0, 300)); process.exit(1); }

const count = await fetch(`${BASE}/rest/v1/products?select=id&limit=1`, {
  headers: { apikey: KEY, Authorization: `Bearer ${KEY}`, Prefer: 'count=exact', Range: '0-0' },
});
console.log('done. products in DB:', count.headers.get('content-range')?.split('/')[1]);
