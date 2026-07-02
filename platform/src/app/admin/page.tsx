import Link from 'next/link';
import { redirect } from 'next/navigation';
import { getAdmin } from '@/lib/auth';
import { createSupabaseAdminClient } from '@/lib/supabase/admin';
import type { SiteRequest } from '@/lib/types';
import { STATUS_META } from '@/components/statusMeta';

export const dynamic = 'force-dynamic';

export default async function AdminQueue() {
  const admin = await getAdmin();
  if (!admin) redirect('/admin/login');

  const db = createSupabaseAdminClient();
  const { data } = await db
    .from('site_requests')
    .select('id, created_at, status, business_name, customer_email, preset_key, deployed_url')
    .order('created_at', { ascending: false });

  const rows = (data ?? []) as Partial<SiteRequest>[];

  return (
    <div>
      <div style={{ display: 'flex', alignItems: 'baseline', gap: 12, marginBottom: 22 }}>
        <h1 className="display" style={{ fontSize: 32, margin: 0 }}>Requests</h1>
        <span className="muted" style={{ fontSize: 14 }}>{rows.length} total</span>
      </div>
      {rows.length === 0 && (
        <div className="card" style={{ padding: 48, textAlign: 'center' }}>
          <p className="muted" style={{ margin: 0 }}>No submissions yet. They’ll appear here as customers finish the intake.</p>
        </div>
      )}
      <div style={{ display: 'grid', gap: 12 }}>
        {rows.map((r, i) => {
          const meta = STATUS_META[r.status ?? 'submitted'];
          return (
            <Link key={r.id} href={`/admin/${r.id}`} className="card q-row rise" style={{ animationDelay: `${Math.min(i * 0.04, 0.4)}s` }}>
              <div>
                <b className="serif" style={{ fontSize: 18, fontWeight: 600 }}>{r.business_name || 'Untitled'}</b>
                <div className="muted" style={{ fontSize: 13, marginTop: 2 }}>{r.customer_email} · {r.preset_key ?? 'no theme'}</div>
              </div>
              <div style={{ textAlign: 'right' }}>
                <span className="pill" style={{ background: meta.bg, color: meta.fg, borderColor: 'transparent' }}>{meta.label}</span>
                <div className="muted" style={{ fontSize: 12, marginTop: 6 }}>{new Date(r.created_at ?? '').toLocaleDateString(undefined, { month: 'short', day: 'numeric' })}</div>
              </div>
            </Link>
          );
        })}
      </div>
    </div>
  );
}
