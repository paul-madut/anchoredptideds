import Link from 'next/link';
import { redirect } from 'next/navigation';
import { getAdmin } from '@/lib/auth';
import { createSupabaseAdminClient } from '@/lib/supabase/admin';
import { fetchProducts, sellableItems } from '@/lib/productsCsv';
import ProductTable from '@/components/ProductTable';
import type { SiteRequest } from '@/lib/types';

export const dynamic = 'force-dynamic';

/** Per-website product selection — decides what lands in THIS site's catalog. */
export default async function SiteProducts({ params }: { params: { id: string } }) {
  const admin = await getAdmin();
  if (!admin) redirect('/admin/login');

  const db = createSupabaseAdminClient();
  const { data } = await db.from('site_requests').select('id, business_name, excluded_products').eq('id', params.id).single();
  if (!data) redirect('/admin');
  const row = data as Pick<SiteRequest, 'id' | 'business_name' | 'excluded_products'>;

  const excluded = new Set<string>(Array.isArray(row.excluded_products) ? row.excluded_products : []);
  const items = sellableItems(await fetchProducts()).map((i) => ({ ...i, included: !excluded.has(i.id) }));

  return (
    <div>
      <div style={{ marginBottom: 18 }}>
        <Link href={`/admin/${row.id}`} className="muted" style={{ fontSize: 13 }}>← {row.business_name}</Link>
        <h1 className="display" style={{ fontSize: 32, margin: '6px 0' }}>Products for {row.business_name}</h1>
        <p className="muted" style={{ margin: 0, fontSize: 14, maxWidth: 640 }}>
          Choose what this website sells. Products disabled in the <Link href="/admin/products">master catalog</Link> are
          locked here. The WordPress bundle and deploy import exactly this selection.
        </p>
      </div>
      <ProductTable items={items} mode="site" requestId={row.id} />
    </div>
  );
}
