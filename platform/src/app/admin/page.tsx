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
      <h1 style={{ fontSize: 24, marginTop: 0 }}>Requests</h1>
      {rows.length === 0 && <p className="muted">No submissions yet.</p>}
      <div style={{ display: 'grid', gap: 10 }}>
        {rows.map((r) => {
          const meta = STATUS_META[r.status ?? 'submitted'];
          return (
            <Link key={r.id} href={`/admin/${r.id}`} className="card" style={{ padding: 16, textDecoration: 'none', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
              <div>
                <b style={{ fontSize: 16 }}>{r.business_name || 'Untitled'}</b>
                <div className="muted" style={{ fontSize: 13 }}>{r.customer_email} · {r.preset_key ?? 'no preset'}</div>
              </div>
              <div style={{ textAlign: 'right' }}>
                <span className="pill" style={{ background: meta.bg, color: meta.fg, borderColor: 'transparent' }}>{meta.label}</span>
                <div className="muted" style={{ fontSize: 12, marginTop: 4 }}>{new Date(r.created_at ?? '').toLocaleDateString()}</div>
              </div>
            </Link>
          );
        })}
      </div>
    </div>
  );
}
