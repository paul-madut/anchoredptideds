import { NextRequest, NextResponse } from 'next/server';
import { getAdmin } from '@/lib/auth';
import { createSupabaseAdminClient } from '@/lib/supabase/admin';

/**
 * POST { requestId, productId, included } — per-website product selection.
 * Maintains site_requests.excluded_products (master catalog stays untouched).
 */
export async function POST(req: NextRequest) {
  const admin = await getAdmin();
  if (!admin) return NextResponse.json({ ok: false, error: 'Unauthorized' }, { status: 401 });

  const body = await req.json().catch(() => null);
  if (!body?.requestId || !body?.productId || typeof body.included !== 'boolean') {
    return NextResponse.json({ ok: false, error: 'requestId, productId, included required' }, { status: 400 });
  }

  const db = createSupabaseAdminClient();
  const { data } = await db.from('site_requests').select('excluded_products').eq('id', body.requestId).single();
  if (!data) return NextResponse.json({ ok: false, error: 'Request not found' }, { status: 404 });

  const excluded = new Set<string>(Array.isArray(data.excluded_products) ? data.excluded_products : []);
  if (body.included) excluded.delete(body.productId);
  else excluded.add(body.productId);

  const { error } = await db.from('site_requests')
    .update({ excluded_products: [...excluded] })
    .eq('id', body.requestId);
  if (error) return NextResponse.json({ ok: false, error: error.message }, { status: 500 });
  return NextResponse.json({ ok: true, excludedCount: excluded.size });
}
