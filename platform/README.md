# Peptide Site Studio — platform

Next.js + Supabase app that turns a customer's branding choices into a live
WordPress peptide store. Three parts, one codebase:

1. **Intake** (`/intake`) — premium typeform flow with a live, token-accurate
   preview of the real theme and AI hero-image generation.
2. **Admin CRM** (`/admin`) — queue of submissions, staging, and a one-click
   **Activate** button.
3. **Deploy worker** (`/api/deploy` → `runDeploy`) — POSTs a brand config to the
   target WordPress's **AP Provisioner** plugin, which installs/activates the
   theme + plugins, applies branding, sideloads assets, and imports the catalog.

## How it fits the WordPress side

The theme was refactored to be fully data-driven (see `../ap/anchored-peptides`):
per-site palette/fonts/copy/hero are WordPress **options**, so deploying a brand
is pure data + one CSV import — no per-site code. The deploy bundle lives in
`../ap/build-zips/` and the shared catalog in `../ap/migration/ap-products-final.csv`.
The **AP Provisioner** plugin (`../ap/ap-provisioner/`) is pre-installed on each
target and exposes `POST /wp-json/ap-provision/v1/build`.

## Setup

```bash
cp .env.example .env.local   # fill in Supabase + bundle URLs + image provider
npm install
# Apply schema: paste supabase/migrations/0001_init.sql into the Supabase SQL editor
#               (or `supabase db push`).
npm run dev
```

Then, as an admin:

1. Add your admin email to both `ADMIN_EMAILS` (env) and the `admins` table.
2. Create your admin user in the Supabase dashboard (email + password).
3. Sign in at `/admin/login`, then `POST /api/seed-presets` once to load presets.

Host the three zips in `../ap/build-zips/` and the products CSV somewhere the
target WordPress can download them (Supabase Storage public bucket, S3, GitHub
raw) and point `BUNDLE_*_URL` / `PRODUCTS_CSV_URL` at them.

## End-to-end flow

```
Customer → /intake → site_requests row (status: submitted)
Admin → /admin → open row → save target URL + Application Password → Activate
  → /api/deploy → runDeploy → POST provisioner /build on the target WP
  → theme+plugins installed, branding applied, catalog imported → status: live
```

## Key files

| Concern | File |
| --- | --- |
| Design presets (source of truth) | `src/lib/presets.ts` |
| site_request → provisioner payload | `src/lib/buildConfig.ts` |
| Deploy worker | `src/lib/deploy.ts` |
| Live preview (shared intake + CRM) | `src/components/Preview.tsx` |
| Intake flow | `src/components/IntakeFlow.tsx` |
| Schema + RLS | `supabase/migrations/0001_init.sql` |
| Provisioner (WordPress) | `../ap/ap-provisioner/ap-provisioner.php` |

## Not yet wired (MVP follow-ups)

- Image provider adapter (`src/app/api/hero-image/route.ts` → `callImageProvider`).
- Rate limiting on `/api/hero-image` and `/api/intake` (public routes).
- Encrypt `target_wp_app_password` at rest (pgcrypto) — currently plaintext,
  readable only via service role.
- Move provisioning to a background job (Action Scheduler) for very large catalogs.
