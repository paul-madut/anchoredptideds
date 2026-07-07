import { redirect } from 'next/navigation';
import { getAdmin } from '@/lib/auth';
import { fetchProducts, sellableItems } from '@/lib/productsCsv';
import ProductTable from '@/components/ProductTable';

export const dynamic = 'force-dynamic';

/**
 * Master product catalog (source of truth for every generated site).
 * Upload the designer's image per product; enable/disable globally.
 * Per-website selection happens on each request's Products page.
 */
export default async function ProductsAdmin() {
  const admin = await getAdmin();
  if (!admin) redirect('/admin/login');

  const items = sellableItems(await fetchProducts());

  return (
    <div>
      <div style={{ marginBottom: 18 }}>
        <h1 className="display" style={{ fontSize: 32, margin: '0 0 6px' }}>Product catalog</h1>
        <p className="muted" style={{ margin: 0, fontSize: 14, maxWidth: 640 }}>
          The master copy every website is built from. Click a thumbnail to attach the designer’s image
          (it becomes the product photo on deployed stores). Toggling here affects <b>all</b> sites —
          per-website picks live on each request page.
        </p>
      </div>
      <ProductTable items={items} mode="master" />
    </div>
  );
}
