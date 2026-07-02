import { redirect } from 'next/navigation';
import { getAdmin } from '@/lib/auth';
import { createSupabaseAdminClient } from '@/lib/supabase/admin';
import { PRESET_BY_KEY } from '@/lib/presets';
import { storagePublicUrl } from '@/lib/buildConfig';
import type { SiteRequest } from '@/lib/types';
import Preview from '@/components/Preview';
import DeployButton from '@/components/DeployButton';
import GenerateButton from '@/components/GenerateButton';
import HtmlReview from '@/components/HtmlReview';
import { saveTarget, setStatus } from './actions';

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

  const saveTargetBound = saveTarget.bind(null, row.id);

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
              <HtmlReview id={row.id} htmlUrl={row.html_url} />
            </div>
          </div>
        )}

        <div className="card" style={{ padding: 16 }}>
          <b style={{ fontSize: 14 }}>Deploy target</b>
          <form action={saveTargetBound} style={{ display: 'grid', gap: 10, marginTop: 10 }}>
            <input name="target_wp_url" defaultValue={row.target_wp_url ?? ''} placeholder="https://customer-site.com" />
            <input name="target_wp_user" defaultValue={row.target_wp_user ?? ''} placeholder="WordPress admin username" />
            <input name="target_wp_app_password" type="password" placeholder={row.target_wp_app_password ? 'Application Password (unchanged)' : 'Application Password'} />
            <button className="btn-ghost" style={{ borderRadius: 40, padding: '10px 16px', cursor: 'pointer' }}>Save target</button>
          </form>
        </div>

        <div className="card" style={{ padding: 16 }}>
          <b style={{ fontSize: 14, display: 'block', marginBottom: 4 }}>Activate site</b>
          <p className="muted" style={{ fontSize: 12, margin: '0 0 12px' }}>Turns the reviewed HTML into the WordPress theme + plugins and deploys to the target with the customer’s login.</p>
          <DeployButton id={row.id} initialStatus={row.status} ready={ready} />
          {row.bundle_url && (
            <p style={{ fontSize: 12, margin: '10px 0 0' }}>
              <a href={row.bundle_url} download>Download WordPress bundle ↓</a> <span className="muted">(the generated theme + plugins)</span>
            </p>
          )}
        </div>

        <div className="card" style={{ padding: 16 }}>
          <b style={{ fontSize: 14 }}>Details</b>
          <dl style={{ display: 'grid', gridTemplateColumns: 'auto 1fr', gap: '6px 14px', fontSize: 13, marginTop: 10 }}>
            <dt className="muted">Positioning</dt><dd style={{ margin: 0 }}>{row.positioning || '—'}</dd>
            <dt className="muted">Theme</dt><dd style={{ margin: 0 }}>{preset?.label ?? row.preset_key ?? '—'}</dd>
            <dt className="muted">Focus</dt><dd style={{ margin: 0 }}>{(row.emphasis_categories ?? []).join(', ') || '—'}</dd>
            <dt className="muted">Logo</dt><dd style={{ margin: 0 }}>{logoUrl ? 'uploaded' : 'wordmark fallback'}</dd>
            <dt className="muted">Hero</dt><dd style={{ margin: 0 }}>{heroUrl ? 'selected' : 'default'}</dd>
          </dl>
        </div>
      </div>

      {/* Right: live preview of what will deploy */}
      <div style={{ position: 'sticky', top: 88 }}>
        <div className="eyebrow" style={{ marginBottom: 10 }}>Preview</div>
        <div className="card" style={{ overflow: 'hidden', padding: 8 }}>
          <div style={{ maxHeight: 640, overflow: 'auto', borderRadius: 10 }}>
            <div style={{ transform: 'scale(0.96)', transformOrigin: 'top center' }}>
              <Preview tokens={tokens} fonts={fonts} brandName={row.business_name ?? 'Brand'} logoUrl={logoUrl} heroImageUrl={heroUrl} copy={row.copy ?? {}} />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
