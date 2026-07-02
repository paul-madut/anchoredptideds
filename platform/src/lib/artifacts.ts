import fs from 'node:fs';
import path from 'node:path';
import JSZip from 'jszip';
import { createSupabaseAdminClient } from './supabase/admin';
import { PRESET_BY_KEY } from './presets';
import { storagePublicUrl } from './buildConfig';
import { renderSiteHtml, type SiteConfig } from './renderSite';
import { generateBrandConfigPlugin } from './brandConfigPlugin';
import type { SiteRequest } from './types';

// Base WordPress artifacts live in the repo's `ap/` folder (one level up from platform/).
const AP_ROOT = path.resolve(process.cwd(), '..', 'ap');
const BUILD_ZIPS = path.join(AP_ROOT, 'build-zips');
const PRODUCTS_CSV = path.join(AP_ROOT, 'migration', 'ap-products-final.csv');
const ARTIFACTS_BUCKET = 'site-artifacts';

/** Build the SiteConfig for a request (preset tokens + row overrides + asset URLs). */
export function siteConfigFor(row: SiteRequest): SiteConfig {
  const preset = row.preset_key ? PRESET_BY_KEY[row.preset_key] : undefined;
  return {
    tokens: { ...(preset?.tokens ?? {}), ...(row.tokens ?? {}) },
    fonts: { ...(preset?.fonts ?? {}), ...(row.fonts ?? {}) },
    brandName: row.business_name ?? 'Peptides',
    logoUrl: storagePublicUrl('logos', row.logo_path),
    heroImageUrl: storagePublicUrl('hero-images', row.hero_image_path),
    copy: row.copy ?? {},
  };
}

async function loadRow(requestId: string): Promise<SiteRequest> {
  const db = createSupabaseAdminClient();
  const { data, error } = await db.from('site_requests').select('*').eq('id', requestId).single();
  if (error || !data) throw new Error(error?.message ?? 'Request not found');
  return data as SiteRequest;
}

/** Persist an HTML artifact to Storage + the row. Returns its public URL. */
async function storeHtml(id: string, html: string): Promise<string> {
  const db = createSupabaseAdminClient();
  const p = `${id}/index.html`;
  const up = await db.storage.from(ARTIFACTS_BUCKET).upload(p, Buffer.from(html), { contentType: 'text/html; charset=utf-8', upsert: true });
  if (up.error) throw new Error(`HTML upload: ${up.error.message}`);
  return db.storage.from(ARTIFACTS_BUCKET).getPublicUrl(p).data.publicUrl;
}

/**
 * APPROVE step — render the HTML from the request's design and store it as the
 * editable source of truth (`html_source`). No WordPress bundle yet.
 */
export async function generateHtml(requestId: string): Promise<{ ok: boolean; html_url?: string; error?: string }> {
  try {
    const db = createSupabaseAdminClient();
    const row = await loadRow(requestId);
    const cfg = siteConfigFor(row);
    const html = renderSiteHtml(cfg);
    const html_url = await storeHtml(row.id, html);
    await db.from('site_requests').update({
      status: 'approved', html_source: html, html_url, config: cfg, generated_at: new Date().toISOString(),
    }).eq('id', row.id);
    return { ok: true, html_url };
  } catch (e) {
    return { ok: false, error: (e as Error).message };
  }
}

/** Replace the editable HTML (from an AI edit or a dev's re-upload) + re-store. */
export async function setHtmlSource(requestId: string, html: string): Promise<{ ok: boolean; html_url?: string; error?: string }> {
  try {
    const db = createSupabaseAdminClient();
    const html_url = await storeHtml(requestId, html);
    await db.from('site_requests').update({ html_source: html, html_url }).eq('id', requestId);
    return { ok: true, html_url };
  } catch (e) {
    return { ok: false, error: (e as Error).message };
  }
}

/**
 * ACTIVATE step — assemble the self-contained WordPress bundle from the CURRENT
 * (possibly edited) HTML + config: theme + plugins + a generated brand-config
 * plugin that bakes the branding AND sets the reviewed HTML as the homepage.
 */
export async function generateBundle(requestId: string): Promise<{ ok: boolean; bundle_url?: string; warnings?: string[]; error?: string }> {
  const db = createSupabaseAdminClient();
  const warnings: string[] = [];
  try {
    const row = await loadRow(requestId);
    const cfg = siteConfigFor(row);
    const html = row.html_source ?? renderSiteHtml(cfg);
    const logo = await fetchAsset(cfg.logoUrl);
    const hero = await fetchAsset(cfg.heroImageUrl);

    const slug = (row.business_name ?? 'site').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'site';
    const zip = new JSZip();
    const root = zip.folder(`${slug}-site`)!;

    await addBaseZip(root, 'anchored-peptides.zip', 'theme', warnings);
    await addBaseZip(root, 'anchored-peptides-homepage.zip', 'plugins', warnings);
    await addBaseZip(root, 'anchored-peptides-coming-soon.zip', 'plugins', warnings);
    await addBaseZip(root, 'ap-provisioner.zip', 'plugins', warnings);

    const bc = root.folder('plugins')!.folder('ap-brand-config')!;
    bc.file('ap-brand-config.php', generateBrandConfigPlugin({
      brandName: cfg.brandName, tokens: cfg.tokens, fontsUrl: cfg.fonts.url ?? '', copy: cfg.copy,
      hasLogo: !!logo, hasHero: !!hero, hasHomeHtml: true,
    }));
    bc.folder('assets')!.file('home.html', html);
    if (logo) bc.folder('assets')!.file(`logo.${logo.ext}`, logo.bytes);
    if (hero) bc.folder('assets')!.file(`hero.${hero.ext}`, hero.bytes);

    if (fs.existsSync(PRODUCTS_CSV)) root.file('products.csv', fs.readFileSync(PRODUCTS_CSV));
    else warnings.push('products CSV not found on disk');

    root.file('index.html', html);
    root.file('INSTALL.md', installReadme(slug, cfg.brandName));

    const bundleBytes = await zip.generateAsync({ type: 'nodebuffer', compression: 'DEFLATE' });
    const bundlePath = `${row.id}/${slug}-site.zip`;
    const up = await db.storage.from(ARTIFACTS_BUCKET).upload(bundlePath, bundleBytes, { contentType: 'application/zip', upsert: true });
    if (up.error) return { ok: false, error: `Bundle upload: ${up.error.message}` };

    const bundle_url = db.storage.from(ARTIFACTS_BUCKET).getPublicUrl(bundlePath).data.publicUrl;
    await db.from('site_requests').update({ bundle_url }).eq('id', row.id);
    return { ok: true, bundle_url, warnings };
  } catch (e) {
    return { ok: false, error: (e as Error).message };
  }
}

async function addBaseZip(root: JSZip, zipName: string, prefix: string, warnings: string[]) {
  const p = path.join(BUILD_ZIPS, zipName);
  if (!fs.existsSync(p)) { warnings.push(`base bundle missing: ${zipName}`); return; }
  const src = await JSZip.loadAsync(fs.readFileSync(p));
  const target = root.folder(prefix)!;
  for (const [rel, file] of Object.entries(src.files)) {
    if (file.dir) continue;
    target.file(rel, await file.async('nodebuffer'));
  }
}

async function fetchAsset(url?: string): Promise<{ bytes: Buffer; ext: string } | null> {
  if (!url) return null;
  try {
    const res = await fetch(url);
    if (!res.ok) return null;
    const ct = res.headers.get('content-type') ?? '';
    const ext = ct.includes('svg') ? 'svg' : ct.includes('webp') ? 'webp' : ct.includes('jpeg') ? 'jpg' : 'png';
    return { bytes: Buffer.from(await res.arrayBuffer()), ext };
  } catch {
    return null;
  }
}

function installReadme(slug: string, brand: string): string {
  return `# ${brand} — WordPress site bundle

Everything needed to stand up this store on a fresh WordPress + WooCommerce install.

## Contents
- \`theme/anchored-peptides/\` — the theme (upload to wp-content/themes, activate)
- \`plugins/anchored-peptides-homepage/\` — homepage template + page/category scaffolding
- \`plugins/ap-brand-config/\` — applies THIS brand's colors, fonts, copy, logo, hero,
  and sets the reviewed homepage HTML (assets/home.html) as the front page
- \`plugins/ap-provisioner/\` — optional REST endpoint for automated deploys
- \`products.csv\` — the WooCommerce catalog (WooCommerce → Products → Import)
- \`index.html\` — the approved homepage design

## Manual install
1. Ensure WooCommerce is installed + active.
2. Upload + activate the theme \`anchored-peptides\`.
3. Upload + activate \`anchored-peptides-homepage\`, then \`ap-brand-config\`.
4. WooCommerce → Products → Import → \`products.csv\`.

The homepage now matches \`index.html\`.
`;
}
