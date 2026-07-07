import { redirect } from 'next/navigation';
import { getAdmin } from '@/lib/auth';
import { createSupabaseAdminClient } from '@/lib/supabase/admin';
import { PRESET_BY_KEY } from '@/lib/presets';
import { storagePublicUrl } from '@/lib/buildConfig';
import { sellingPointImageUrls } from '@/lib/artifacts';
import type { SiteRequest } from '@/lib/types';
import Link from 'next/link';
import { fetchProducts, sellableItems } from '@/lib/productsCsv';
import Preview from '@/components/Preview';
import DeployButton from '@/components/DeployButton';
import BuildFilesButton from '@/components/BuildFilesButton';
import GenerateButton from '@/components/GenerateButton';
import HtmlReview from '@/components/HtmlReview';
import { saveTarget, setStatus, saveSellingPointImage, clearSellingPointImage } from './actions';

export const dynamic = 'force-dynamic';

export default async function RequestDetail({ params }: { params: { id: string } }) {
  const admin = await getAdmin();
  if (!admin) redirect('/admin/login');

  const db = createSupabaseAdminClient();
  const { data } = await db.from('site_requests').select('*').eq('id', params.id).single();
  if (!data) redirect('/admin');
  const row = data as SiteRequest;

  const preset = row.preset_key ? PRESET_BY_KEY[row.preset_key] : undefined;
  const tokens = { ...(preset?.tokens ?? {}), ...(row.tokens ?? {}) };
  const fonts = { ...(preset?.fonts ?? {}), ...(row.fonts ?? {}) };
  const logoUrl = storagePublicUrl('logos', row.logo_path);
  const heroUrl = storagePublicUrl('hero-images', row.hero_image_path);
  const ready = Boolean(row.target_wp_url && row.target_wp_user && row.target_wp_app_password);

  // Product selection summary for this site (master enabled minus site exclusions).
  const catalog = sellableItems(await fetchProducts().catch(() => []));
  const excluded = new Set<string>(Array.isArray(row.excluded_products) ? row.excluded_products : []);
  const masterEnabled = catalog.filter((p) => p.enabled);
  const siteCount = masterEnabled.filter((p) => !excluded.has(p.id)).length;
  const missingImages = masterEnabled.filter((p) => !excluded.has(p.id) && !p.imageUrl).length;

  const saveTargetBound = saveTarget.bind(null, row.id);

  // Selling-point ("bento") cards: text + current image, for admin image attach.
  const sellingPoints = (Array.isArray(row.selling_points) ? row.selling_points : []).filter((p) => p?.trim());
  const spUrls = sellingPointImageUrls(row);
  const spPaths = Array.isArray(row.selling_point_image_paths) ? row.selling_point_image_paths : [];

  return (
    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 28, alignItems: 'start' }}>
      {/* Left: details + deploy controls */}
      <div style={{ display: 'grid', gap: 18 }}>
        <div>
          <h1 className="display" style={{ fontSize: 30, margin: '0 0 4px' }}>{row.business_name}</h1>
          <p className="muted" style={{ margin: 0, fontSize: 14 }}>{row.customer_name} · {row.customer_email}</p>
        </div>

        <div className="card" style={{ padding: 16 }}>
          <b style={{ fontSize: 14 }}>Stage</b>
          <div style={{ display: 'flex', gap: 8, margin: '10px 0 14px', flexWrap: 'wrap' }}>
            <form action={async () => { 'use server'; await setStatus(row.id, 'in_review'); }}>
              <button className="btn-ghost" style={{ borderRadius: 40, padding: '8px 14px', cursor: 'pointer' }}>Mark in review</button>
            </form>
          </div>
          <GenerateButton id={row.id} hasArtifacts={!!row.html_url} />
          <p className="muted" style={{ fontSize: 12, margin: '8px 0 0' }}>Approving generates the homepage HTML — your editable source of truth. Review it below before activating.</p>
        </div>

        {row.html_url && (
          <div className="card" style={{ padding: 16 }}>
            <b style={{ fontSize: 14 }}>Review &amp; edit HTML</b>
            {row.generated_at && <span className="muted" style={{ fontSize: 12, marginLeft: 8 }}>{new Date(row.generated_at).toLocaleString()}</span>}
            <div style={{ marginTop: 12 }}>
              <HtmlReview id={row.id} />
            </div>
          </div>
        )}

        {/* STEP — pick this website's catalog from the master product list. */}
        <div className="card" style={{ padding: 16 }}>
          <b style={{ fontSize: 14, display: 'block', marginBottom: 4 }}>Products on this site</b>
          <p className="muted" style={{ fontSize: 12, margin: '0 0 12px' }}>
            {catalog.length
              ? <>Selling <b style={{ color: 'var(--ink)' }}>{siteCount}</b> of {masterEnabled.length} available products.
                  {missingImages > 0 && <> {missingImages} selected product{missingImages === 1 ? ' is' : 's are'} missing a designer image.</>}</>
              : 'Master catalog is empty — run scripts/import-products.mjs.'}
          </p>
          <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
            <Link href={`/admin/${row.id}/products`} className="btn-ghost" style={{ borderRadius: 40, padding: '10px 16px', textDecoration: 'none' }}>Choose products →</Link>
            <Link href="/admin/products" className="btn-ghost" style={{ borderRadius: 40, padding: '10px 16px', textDecoration: 'none' }}>Master catalog &amp; images</Link>
          </div>
        </div>

        {/* STEP — attach an image to each selling-point (bento) card. */}
        {sellingPoints.length > 0 && (
          <div className="card" style={{ padding: 16 }}>
            <b style={{ fontSize: 14, display: 'block', marginBottom: 4 }}>Selling-point images</b>
            <p className="muted" style={{ fontSize: 12, margin: '0 0 14px' }}>
              One image per bento card. Leave a card empty to use the auto-generated art. Attaching or removing an image updates the homepage immediately once approved.
            </p>
            <div style={{ display: 'grid', gap: 14 }}>
              {sellingPoints.map((point, i) => {
                const url = spUrls[i];
                const isManual = (spPaths[i] ?? '').includes('-manual');
                const saveBound = saveSellingPointImage.bind(null, row.id, i);
                return (
                  <div key={i} style={{ display: 'flex', gap: 12, alignItems: 'flex-start' }}>
                    <div style={{ width: 64, height: 64, flex: '0 0 auto', borderRadius: 8, overflow: 'hidden', background: 'var(--ap-bg2, #eee)', border: '1px solid var(--border, #ddd)', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                      {url
                        ? <img src={url} alt="" style={{ width: '100%', height: '100%', objectFit: 'cover', display: 'block' }} />
                        : <span className="muted" style={{ fontSize: 10, textAlign: 'center', padding: 4 }}>auto art</span>}
                    </div>
                    <div style={{ flex: 1, minWidth: 0 }}>
                      <div style={{ fontSize: 13, marginBottom: 2 }}><b>Card {i + 1}</b> <span className="muted" style={{ fontSize: 11 }}>{isManual ? '· uploaded' : url ? '· generated' : '· none'}</span></div>
                      <p className="muted" style={{ fontSize: 12, margin: '0 0 8px', overflow: 'hidden', textOverflow: 'ellipsis' }}>{point}</p>
                      <form action={saveBound} style={{ display: 'flex', gap: 8, alignItems: 'center', flexWrap: 'wrap' }}>
                        <input type="file" name="image" accept="image/*" required style={{ fontSize: 12, maxWidth: 210 }} />
                        <button className="btn-ghost" style={{ borderRadius: 40, padding: '6px 14px', fontSize: 12, cursor: 'pointer' }}>{isManual ? 'Replace' : 'Attach'}</button>
                        {isManual && (
                          <button formAction={clearSellingPointImage.bind(null, row.id, i)} formNoValidate className="btn-ghost" style={{ borderRadius: 40, padding: '6px 14px', fontSize: 12, cursor: 'pointer' }}>Remove</button>
                        )}
                      </form>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        )}

        {/* FINAL STEP — the deliverable is the WordPress files themselves. */}
        <div className="card" style={{ padding: 16 }}>
          <b style={{ fontSize: 14, display: 'block', marginBottom: 4 }}>WordPress files</b>
          <p className="muted" style={{ fontSize: 12, margin: '0 0 12px' }}>
            Builds the theme + plugin bundle from the reviewed design — ready to install on any WordPress + WooCommerce site. No customer login needed.
          </p>
          {row.html_url ? (
            <>
              <BuildFilesButton id={row.id} hasBundle={!!row.bundle_url} />
              {row.bundle_url && (
                <div style={{ marginTop: 12 }}>
                  <a className="btn-ghost" href={row.bundle_url} download style={{ borderRadius: 40, padding: '10px 16px', display: 'inline-block', textDecoration: 'none' }}>Download WordPress bundle ↓</a>
                  <ul className="muted" style={{ fontSize: 12, margin: '10px 0 0', paddingLeft: 18, lineHeight: 1.7 }}>
                    <li><code>{'{site}'}-theme.zip</code> — upload via Appearance → Themes</li>
                    <li><code>{'{site}'}-plugins.zip</code> — one plugin (homepage + branding + provisioner), upload via Plugins</li>
                    <li><code>products.csv</code> — the WooCommerce catalog</li>
                    <li><code>INSTALL.md</code> — step-by-step install</li>
                  </ul>
                </div>
              )}
            </>
          ) : (
            <p className="muted" style={{ fontSize: 12, margin: 0 }}>Approve the design first — the files are built from the reviewed HTML.</p>
          )}
        </div>

        {/* OPTIONAL — instead of installing by hand, let the provisioner push it. */}
        <div className="card" style={{ padding: 16 }}>
          <span className="eyebrow" style={{ fontSize: 11 }}>Optional</span>
          <b style={{ fontSize: 14, display: 'block', margin: '4px 0' }}>Have AI install it for you</b>
          <p className="muted" style={{ fontSize: 12, margin: '0 0 12px' }}>
            Point us at the customer’s WordPress and we’ll push the same files, apply the branding, and import the catalog automatically over the REST provisioner.
          </p>
          <form action={saveTargetBound} style={{ display: 'grid', gap: 10, marginBottom: 12 }}>
            <input name="target_wp_url" defaultValue={row.target_wp_url ?? ''} placeholder="https://customer-site.com" />
            <input name="target_wp_user" defaultValue={row.target_wp_user ?? ''} placeholder="WordPress admin username" />
            <input name="target_wp_app_password" type="password" placeholder={row.target_wp_app_password ? 'Application Password (unchanged)' : 'Application Password'} />
            <button className="btn-ghost" style={{ borderRadius: 40, padding: '10px 16px', cursor: 'pointer' }}>Save target</button>
          </form>
          <DeployButton id={row.id} initialStatus={row.status} ready={ready} />
        </div>

        <div className="card" style={{ padding: 16 }}>
          <b style={{ fontSize: 14 }}>Details</b>
          <dl style={{ display: 'grid', gridTemplateColumns: 'auto 1fr', gap: '6px 14px', fontSize: 13, marginTop: 10 }}>
            <dt className="muted">Positioning</dt><dd style={{ margin: 0 }}>{row.positioning || '—'}</dd>
            <dt className="muted">Theme</dt><dd style={{ margin: 0 }}>{preset?.label ?? row.preset_key ?? '—'}</dd>
            <dt className="muted">Focus</dt><dd style={{ margin: 0 }}>{(row.emphasis_categories ?? []).join(', ') || '—'}</dd>
            <dt className="muted">Categories</dt><dd style={{ margin: 0 }}>{row.show_categories === false ? 'hidden on site (research-only framing)' : 'shown on site'}</dd>
            <dt className="muted">Selling points</dt><dd style={{ margin: 0 }}>{(row.selling_points ?? []).length ? (row.selling_points ?? []).map((p, i) => <span key={i} style={{ display: 'block' }}>{i + 1}. {p}</span>) : '—'}</dd>
            <dt className="muted">Logo</dt><dd style={{ margin: 0 }}>{logoUrl ? 'uploaded' : 'wordmark fallback'}</dd>
            <dt className="muted">Hero</dt><dd style={{ margin: 0 }}>{heroUrl ? 'selected' : 'default'}</dd>
          </dl>
        </div>
      </div>

      {/* Right: live preview of what will deploy */}
      <div style={{ position: 'sticky', top: 88 }}>
        <div className="eyebrow" style={{ marginBottom: 10 }}>Preview</div>
        <div className="card" style={{ overflow: 'hidden', padding: 8 }}>
          <div style={{ maxHeight: 640, overflow: 'auto', borderRadius: 10, background: tokens['--ap-bg'] ?? '#fff' }}>
            <Preview tokens={tokens} fonts={fonts} brandName={row.business_name ?? 'Brand'} logoUrl={logoUrl} heroImageUrl={heroUrl} copy={row.copy ?? {}} />
          </div>
        </div>
      </div>
    </div>
  );
}
