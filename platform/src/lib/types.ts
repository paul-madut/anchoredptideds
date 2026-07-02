import type { Tokens, Fonts } from './presets';

export type SiteStatus =
  | 'submitted'
  | 'in_review'
  | 'approved'
  | 'building'
  | 'deploying'
  | 'live'
  | 'failed';

export interface SiteRequest {
  id: string;
  created_at: string;
  updated_at: string;
  status: SiteStatus;

  customer_name: string | null;
  customer_email: string | null;
  business_name: string | null;
  positioning: string | null;
  answers: Record<string, unknown>;
  emphasis_categories: string[];

  preset_key: string | null;
  tokens: Tokens;
  fonts: Partial<Fonts>;
  copy: Record<string, string>;
  logo_path: string | null;
  hero_image_path: string | null;

  target_wp_url: string | null;
  target_wp_user: string | null;
  target_wp_app_password: string | null;
  deploy_result: DeployResult | null;
  deployed_url: string | null;

  // Generated artifacts (populated on approve)
  html_url: string | null;
  html_source: string | null;
  bundle_url: string | null;
  config: unknown | null;
  generated_at: string | null;

  submitted_at: string;
  deployed_at: string | null;
}

/** Payload POSTed to the target's AP Provisioner /build endpoint. */
export interface ProvisionConfig {
  secret?: string;
  brand_name: string;
  tokens: Tokens;
  fonts_url: string;
  copy: Record<string, string>;
  logo_url?: string;
  hero_image_url?: string;
  bundle: { theme: string; homepage: string; coming_soon: string };
  products_csv_url: string;
  ensure_woocommerce: boolean;
  coming_soon: boolean;
  custom_home_html?: string;
}

export interface DeployResult {
  ok: boolean;
  status?: string;
  live_url?: string;
  warnings?: string[];
  counts?: Record<string, number>;
  error?: string;
}
