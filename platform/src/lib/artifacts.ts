import fs from 'node:fs';
import path from 'node:path';
import JSZip from 'jszip';
import { createSupabaseAdminClient } from './supabase/admin';
import { PRESET_BY_KEY } from './presets';
import { storagePublicUrl } from './buildConfig';
import { type SiteConfig } from './renderSite';
import { renderHomeHtml } from './renderHome';
import { hasOpenAI, generateImageBytes } from './openai';
import { buildProductsCsv } from './productsCsv';
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
    showCategories: row.show_categories !== false,
    sellingPoints: Array.isArray(row.selling_points) ? row.selling_points : [],
    sellingPointImages: sellingPointImageUrls(row),
  };
}

/** Public URL (or undefined) for each selling-point card's stored image path. */
export function sellingPointImageUrls(row: SiteRequest): (string | undefined)[] {
  const paths = Array.isArray(row.selling_point_image_paths) ? row.selling_point_image_paths : [];
  return [0, 1, 2].map((i) => storagePublicUrl(ARTIFACTS_BUCKET, paths[i] ?? null));
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
  // cacheControl 0: the file is upserted in place on every edit, so the CDN must not serve stale copies.
  const up = await db.storage.from(ARTIFACTS_BUCKET).upload(p, Buffer.from(html), { contentType: 'text/html; charset=utf-8', upsert: true, cacheControl: '0' });
  if (up.error) throw new Error(`HTML upload: ${up.error.message}`);
  return db.storage.from(ARTIFACTS_BUCKET).getPublicUrl(p).data.publicUrl;
}

/**
 * Ensure each of the three bento cards has an image, returning the effective
 * Storage path per slot. Slots that already have a path (an admin-attached
 * image, or one generated on a prior approve) are LEFT UNTOUCHED — only empty
 * slots are filled by the designer (gpt-image-1). Best-effort: a slot that
 * can't be generated stays null and falls back to decorative art at render.
 */
async function ensureSellingPointImages(row: SiteRequest, cfg: SiteConfig): Promise<(string | null)[]> {
  const existing = Array.isArray(row.selling_point_image_paths) ? row.selling_point_image_paths : [];
  const paths: (string | null)[] = [0, 1, 2].map((i) => existing[i] ?? null);
  const points = (cfg.sellingPoints ?? []).map((p) => p.trim()).filter(Boolean).slice(0, 3);
  if (points.length < 3 || !hasOpenAI()) return paths;

  const db = createSupabaseAdminClient();
  const accent = cfg.tokens['--ap-olive'] ?? '#3E412E';
  const bg = cfg.tokens['--ap-bg'] ?? '#ECE7DA';
  await Promise.all(
    points.map(async (point, i) => {
      if (paths[i]) return; // keep an admin upload or a previously-generated image
      try {
        const prompt =
          `Premium editorial still-life photograph for a research-peptide brand, illustrating: "${point}". ` +
          `Laboratory glass vials, scientific equipment, or abstract macro detail — tasteful and clinical. ` +
          `Muted palette anchored on ${accent} and ${bg}, soft studio light, shallow depth of field. ` +
          `Strictly no people, no faces, no hands, no text, no logos.`;
        const bytes = await generateImageBytes(prompt);
        const p = `${row.id}/selling-point-${i + 1}.png`;
        const up = await db.storage.from(ARTIFACTS_BUCKET).upload(p, bytes, { contentType: 'image/png', upsert: true, cacheControl: '0' });
        if (!up.error) paths[i] = p;
      } catch {
        /* leave slot null → decorative fallback */
      }
    }),
  );
  return paths;
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
    // Fill any empty bento slots (keeps admin uploads + prior generations), then
    // render + persist the effective paths so re-renders stay lossless.
    const spPaths = await ensureSellingPointImages(row, cfg);
    cfg.sellingPointImages = spPaths.map((p) => storagePublicUrl(ARTIFACTS_BUCKET, p));
    const html = renderHomeHtml(cfg);
    const html_url = await storeHtml(row.id, html);
    await db.from('site_requests').update({
      status: 'approved', html_source: html, html_url, config: cfg,
      selling_point_image_paths: spPaths, generated_at: new Date().toISOString(),
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
    const html = row.html_source ?? renderHomeHtml(cfg);
    const logo = await fetchAsset(cfg.logoUrl);
    const hero = await fetchAsset(cfg.heroImageUrl);

    const slug = (row.business_name ?? 'site').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'site';

    // WordPress admin installs ONE zip at a time: the theme via Appearance →
    // Upload Theme, a plugin via Plugins → Upload Plugin. A zip with multiple
    // plugin folders does NOT install (WP nests them and none activate). So we
    // ship exactly two uploadable zips: the theme, and ONE consolidated plugin.

    // 1) Theme zip — the base theme is already a single-folder, installable zip.
    const themeZipPath = path.join(BUILD_ZIPS, 'anchored-peptides.zip');
    const themeBytes = fs.existsSync(themeZipPath) ? fs.readFileSync(themeZipPath) : null;
    if (!themeBytes) warnings.push('theme base zip missing: anchored-peptides.zip');

    // 2) Consolidated plugin zip — one plugin folder `ap-site` whose loader
    //    requires the homepage, brand-config, and provisioner as modules.
    const pluginZip = new JSZip();
    const site = pluginZip.folder('ap-site')!;
    site.file('ap-site.php', siteLoaderPhp(cfg.brandName));
    const mods = site.folder('modules')!;
    await extractZipInto(mods, 'anchored-peptides-homepage.zip', warnings);
    await extractZipInto(mods, 'ap-provisioner.zip', warnings);

    const bc = mods.folder('ap-brand-config')!;
    bc.file('ap-brand-config.php', generateBrandConfigPlugin({
      brandName: cfg.brandName, tokens: cfg.tokens, fontsUrl: cfg.fonts.url ?? '', copy: cfg.copy,
      hasLogo: !!logo, hasHero: !!hero, hasHomeHtml: true,
    }));
    bc.folder('assets')!.file('home.html', html);
    if (logo) bc.folder('assets')!.file(`logo.${logo.ext}`, logo.bytes);
    if (hero) bc.folder('assets')!.file(`hero.${hero.ext}`, hero.bytes);
    const pluginBytes = await pluginZip.generateAsync({ type: 'nodebuffer', compression: 'DEFLATE' });

    // 3) Outer bundle: the two uploadable zips + catalog + reference + install guide.
    const zip = new JSZip();
    const root = zip.folder(`${slug}-site`)!;
    if (themeBytes) root.file(`${slug}-theme.zip`, themeBytes);
    root.file(`${slug}-plugins.zip`, pluginBytes);

    // Per-site catalog: master `products` table minus this site's exclusions,
    // with designer image URLs in the Images column (WooCommerce sideloads them).
    // Falls back to the repo CSV if the DB catalog is empty.
    let csv = await buildProductsCsv(
      Array.isArray(row.excluded_products) ? row.excluded_products : [],
    ).catch(() => null);
    if (!csv && fs.existsSync(PRODUCTS_CSV)) csv = fs.readFileSync(PRODUCTS_CSV, 'utf8');
    if (csv) {
      root.file('products.csv', csv);
      // Publish it too, so the provisioner deploy imports this exact selection.
      const csvUp = await db.storage.from(ARTIFACTS_BUCKET).upload(`${row.id}/products.csv`, Buffer.from(csv), {
        contentType: 'text/csv; charset=utf-8', upsert: true, cacheControl: '0',
      });
      if (csvUp.error) warnings.push(`products.csv upload: ${csvUp.error.message}`);
    } else {
      warnings.push('no products available (DB catalog empty and repo CSV missing)');
    }

    root.file('index.html', html);
    root.file('INSTALL.md', installReadme(slug, cfg.brandName));

    const bundleBytes = await zip.generateAsync({ type: 'nodebuffer', compression: 'DEFLATE' });
    const bundlePath = `${row.id}/${slug}-site.zip`;
    const up = await db.storage.from(ARTIFACTS_BUCKET).upload(bundlePath, bundleBytes, { contentType: 'application/zip', upsert: true, cacheControl: '0' });
    if (up.error) return { ok: false, error: `Bundle upload: ${up.error.message}` };

    const bundle_url = db.storage.from(ARTIFACTS_BUCKET).getPublicUrl(bundlePath).data.publicUrl;
    await db.from('site_requests').update({ bundle_url }).eq('id', row.id);
    return { ok: true, bundle_url, warnings };
  } catch (e) {
    return { ok: false, error: (e as Error).message };
  }
}

/** Extract a base build-zip into `target`, preserving its single top-level folder. */
async function extractZipInto(target: JSZip, zipName: string, warnings: string[]) {
  const p = path.join(BUILD_ZIPS, zipName);
  if (!fs.existsSync(p)) { warnings.push(`base bundle missing: ${zipName}`); return; }
  const src = await JSZip.loadAsync(fs.readFileSync(p));
  for (const [rel, file] of Object.entries(src.files)) {
    if (file.dir) continue;
    target.file(rel, await file.async('nodebuffer'));
  }
}

/**
 * The single wrapper plugin (`ap-site/ap-site.php`) that turns several plugins
 * into ONE installable plugin. It `require`s each module — their add_action /
 * add_filter hooks run at include time — but each module's own
 * register_activation_hook(__FILE__, …) never fires when bundled (its __FILE__
 * isn't the activated plugin), so we invoke their setup from THIS plugin's
 * activation hook instead.
 */
function siteLoaderPhp(brandName: string): string {
  const name = (brandName || 'Peptides').replace(/[\r\n*/]/g, ' ').trim();
  return `<?php
/**
 * Plugin Name: ${name} — Site
 * Description: One-install bundle — homepage, branding, and the REST provisioner for ${name}.
 * Version: 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Load each bundled module from ap-site/modules/. Their hooks register on include;
// __FILE__-relative asset paths (APH_URL, plugin_dir_path, …) still resolve since
// each module keeps its own folder.
$ap_site_modules = __DIR__ . '/modules';
foreach ( array(
    '/anchored-peptides-homepage/anchored-peptides-homepage.php',
    '/ap-brand-config/ap-brand-config.php',
    '/ap-provisioner/ap-provisioner.php',
) as $ap_site_rel ) {
    $ap_site_file = $ap_site_modules . $ap_site_rel;
    if ( file_exists( $ap_site_file ) ) { require_once $ap_site_file; }
}

// Run the modules' one-time setup from this plugin's activation.
register_activation_hook( __FILE__, 'ap_site_activate' );
function ap_site_activate() {
    if ( function_exists( 'aph_activate' ) ) { aph_activate(); }
    if ( function_exists( 'ap_brand_config_apply' ) ) { ap_brand_config_apply(); }
    flush_rewrite_rules();
}
`;
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
  return `# ${brand} — WordPress install

Two uploadable zips + the product catalog. Each zip installs through the normal
WordPress admin uploader (no FTP, no unzipping into wp-content).

## Contents
- \`${slug}-theme.zip\` — the theme
- \`${slug}-plugins.zip\` — ONE plugin ("${brand} — Site") that bundles the homepage,
  this brand's config (colors, fonts, copy, logo, hero + the reviewed homepage HTML),
  and the REST provisioner
- \`products.csv\` — the WooCommerce catalog
- \`index.html\` — the approved homepage design (reference only)

## Install
1. Ensure WooCommerce is installed + active.
2. Appearance → Themes → Add New → Upload Theme → \`${slug}-theme.zip\` → Activate.
3. Plugins → Add New → Upload Plugin → \`${slug}-plugins.zip\` → Activate.
   Branding, fonts, logo, hero, and the homepage all apply on activation.
4. WooCommerce → Products → Import → \`products.csv\`.

The homepage now matches \`index.html\`.
`;
}
