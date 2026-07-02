// One-time setup: seed presets, register admin, create login user.
// Pure REST (PostgREST + Auth admin API) so it runs on Node 20 without supabase-js.
// Usage: SB_URL=.. SB_KEY=.. ADMIN_EMAIL=.. ADMIN_PASSWORD=.. node scripts/setup.mjs
import ts from 'typescript';
import fs from 'node:fs';
import path from 'node:path';
import { pathToFileURL } from 'node:url';

const BASE = process.env.SB_URL;
const KEY = process.env.SB_KEY;
const ADMIN_EMAIL = process.env.ADMIN_EMAIL;
const ADMIN_PASSWORD = process.env.ADMIN_PASSWORD;
const H = { apikey: KEY, Authorization: `Bearer ${KEY}`, 'Content-Type': 'application/json' };

// Transpile presets.ts (type-only imports) and load PRESETS.
const src = fs.readFileSync('src/lib/presets.ts', 'utf8');
const js = ts.transpileModule(src, { compilerOptions: { module: 'ESNext', target: 'ES2021' } }).outputText;
const tmpPath = path.resolve('scripts/.presets.tmp.mjs');
fs.writeFileSync(tmpPath, js);
const { PRESETS } = await import(pathToFileURL(tmpPath).href);
fs.unlinkSync(tmpPath);

// 1) Seed presets (upsert)
const rows = PRESETS.map((p) => ({ key: p.key, label: p.label, tokens: p.tokens, fonts: p.fonts, default_copy: {}, sort: p.sort }));
let r = await fetch(`${BASE}/rest/v1/presets`, { method: 'POST', headers: { ...H, Prefer: 'resolution=merge-duplicates' }, body: JSON.stringify(rows) });
console.log('presets:', r.status, r.ok ? `seeded ${rows.length}` : await r.text());

// 2) Register admin in the allowlist table (upsert)
r = await fetch(`${BASE}/rest/v1/admins`, { method: 'POST', headers: { ...H, Prefer: 'resolution=merge-duplicates' }, body: JSON.stringify({ email: ADMIN_EMAIL }) });
console.log('admins:', r.status, r.ok ? `${ADMIN_EMAIL} registered` : await r.text());

// 3) Create the auth login user
r = await fetch(`${BASE}/auth/v1/admin/users`, { method: 'POST', headers: H, body: JSON.stringify({ email: ADMIN_EMAIL, password: ADMIN_PASSWORD, email_confirm: true }) });
const body = await r.json();
console.log('auth user:', r.status, r.ok ? `created ${ADMIN_EMAIL}` : (body.msg || body.error_description || body.error_code || JSON.stringify(body)));
