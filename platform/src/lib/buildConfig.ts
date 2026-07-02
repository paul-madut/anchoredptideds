import type { SiteRequest, ProvisionConfig } from './types';
import { PRESET_BY_KEY } from './presets';

/** Public URL for a Storage object in a public bucket. */
export function storagePublicUrl(bucket: string, path: string | null): string | undefined {
  if (!path) return undefined;
  const base = process.env.NEXT_PUBLIC_SUPABASE_URL!;
  return `${base}/storage/v1/object/public/${bucket}/${encodeURI(path)}`;
}

/**
 * Turn a CRM site_request row into the exact JSON the target's AP Provisioner
 * expects. Tokens/fonts come from the chosen preset, with any per-row overrides
 * layered on top. Assets resolve to their public Storage URLs so the target can
 * download them during provisioning.
 */
export function buildProvisionConfig(row: SiteRequest): ProvisionConfig {
  const preset = row.preset_key ? PRESET_BY_KEY[row.preset_key] : undefined;

  const tokens = { ...(preset?.tokens ?? {}), ...(row.tokens ?? {}) };
  const fontsUrl = row.fonts?.url ?? preset?.fonts.url ?? '';

  const env = (k: string) => {
    const v = process.env[k];
    if (!v) throw new Error(`Missing required env: ${k}`);
    return v;
  };

  return {
    secret: process.env.PROVISION_SECRET || undefined,
    brand_name: row.business_name ?? 'Peptides',
    tokens,
    fonts_url: fontsUrl,
    copy: row.copy ?? {},
    logo_url: storagePublicUrl('logos', row.logo_path),
    hero_image_url: storagePublicUrl('hero-images', row.hero_image_path),
    bundle: {
      theme: env('BUNDLE_THEME_URL'),
      homepage: env('BUNDLE_HOMEPAGE_URL'),
      coming_soon: env('BUNDLE_COMING_SOON_URL'),
    },
    products_csv_url: env('PRODUCTS_CSV_URL'),
    ensure_woocommerce: true,
    coming_soon: false,
    // The reviewed/edited homepage HTML becomes the deployed front page.
    custom_home_html: row.html_source ?? undefined,
  };
}
