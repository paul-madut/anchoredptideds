import { createSupabaseAdminClient } from './supabase/admin';

/**
 * Master product catalog lives in the `products` table (imported from the
 * original WooCommerce CSV; `data` holds the verbatim row). This rebuilds a
 * WooCommerce-importable CSV for one site: master-enabled products, minus the
 * site's exclusions, with designer images (product-images bucket) overriding
 * the Images column. Variations always follow their variable parent.
 */

export interface ProductRow {
  id: string;
  position: number;
  wc_id: string;
  type: string;
  sku: string | null;
  name: string;
  parent_wc_id: string | null;
  categories: string | null;
  price: string | null;
  data: Record<string, string>;
  enabled: boolean;
  image_path: string | null;
}

/** Canonical column order of the source CSV (jsonb does not preserve key order). */
const HEADERS = [
  'ID', 'Type', 'SKU', 'GTIN, UPC, EAN, or ISBN', 'Name', 'Published', 'Is featured?',
  'Visibility in catalog', 'Short description', 'Description', 'Date sale price starts',
  'Date sale price ends', 'Tax status', 'Tax class', 'In stock?', 'Stock', 'Low stock amount',
  'Backorders allowed?', 'Sold individually?', 'Weight (kg)', 'Length (cm)', 'Width (cm)',
  'Height (cm)', 'Allow customer reviews?', 'Purchase note', 'Sale price', 'Regular price',
  'Categories', 'Tags', 'Shipping class', 'Images', 'Download limit', 'Download expiry days',
  'Parent', 'Grouped products', 'Upsells', 'Cross-sells', 'External URL', 'Button text',
  'Position', 'Brands', 'Attribute 1 name', 'Attribute 1 value(s)', 'Attribute 1 visible',
  'Attribute 1 global', 'Attribute 1 default', 'Meta: _ap_badge', 'Meta: _ap_coa_lot',
  'Meta: _ap_coa_purity', 'Meta: _ap_coa_tested', 'Meta: _ap_coa_url', 'Meta: _ap_disclaimer',
  'Meta: _ap_eyebrow', 'Meta: _ap_price_suffix', 'Meta: _ap_sku_prefix', 'Meta: _ap_spec_1',
  'Meta: _ap_spec_2', 'Meta: _ap_spec_3', 'Meta: _ap_specs_html', 'Meta: _ap_storage_html',
  'Meta: _ap_tagline', 'Meta: _ap_title_em', 'Meta: _ap_unit', 'Meta: _ap_variants',
];

const csvField = (v: string): string => (/[",\n\r]/.test(v) ? `"${v.replace(/"/g, '""')}"` : v);

export async function fetchProducts(): Promise<ProductRow[]> {
  const db = createSupabaseAdminClient();
  const { data, error } = await db.from('products').select('*').order('position');
  if (error) throw new Error(`products fetch: ${error.message}`);
  return (data ?? []) as ProductRow[];
}

export function productImageUrl(p: Pick<ProductRow, 'image_path'>): string | undefined {
  if (!p.image_path) return undefined;
  const db = createSupabaseAdminClient();
  return db.storage.from('product-images').getPublicUrl(p.image_path).data.publicUrl;
}

/** Which products a given site includes (sellable-level; variations follow parents). */
export function includedFor(products: ProductRow[], excludedIds: string[]): ProductRow[] {
  const excluded = new Set(excludedIds);
  const keptParents = new Set(
    products.filter((p) => p.type !== 'variation' && p.enabled && !excluded.has(p.id)).map((p) => p.wc_id),
  );
  return products.filter((p) =>
    p.type === 'variation' ? keptParents.has(p.parent_wc_id ?? '') : keptParents.has(p.wc_id),
  );
}

/** Display category: the child term of "Peptides > X", else "Other". */
export function displayCategory(categories: string | null): string {
  for (const part of (categories ?? '').split(',')) {
    const i = part.indexOf('>');
    if (i >= 0) return part.slice(i + 1).trim();
  }
  return 'Other';
}

/** Shape the sellable products (variable + simple) for the admin tables. */
export function sellableItems(products: ProductRow[]) {
  const variationCount: Record<string, number> = {};
  for (const p of products) {
    if (p.type === 'variation' && p.parent_wc_id) {
      variationCount[p.parent_wc_id] = (variationCount[p.parent_wc_id] ?? 0) + 1;
    }
  }
  return products
    .filter((p) => p.type !== 'variation')
    .map((p) => ({
      id: p.id,
      name: p.name,
      sku: p.sku,
      type: p.type,
      category: displayCategory(p.categories),
      price: p.price,
      variations: variationCount[p.wc_id] ?? 0,
      enabled: p.enabled,
      imageUrl: productImageUrl(p),
    }));
}

/** Build the WooCommerce CSV for one site. Returns null when the catalog is empty. */
export async function buildProductsCsv(excludedIds: string[]): Promise<string | null> {
  const products = await fetchProducts();
  if (!products.length) return null;
  const rows = includedFor(products, excludedIds);
  const lines = [HEADERS.map(csvField).join(',')];
  for (const p of rows) {
    const rec = { ...p.data };
    const img = productImageUrl(p);
    if (img && p.type !== 'variation') rec.Images = img;
    lines.push(HEADERS.map((h) => csvField(rec[h] ?? '')).join(','));
  }
  return lines.join('\n') + '\n';
}
