import { NextRequest, NextResponse } from 'next/server';
import { getAdmin } from '@/lib/auth';
import { createSupabaseAdminClient } from '@/lib/supabase/admin';

/** POST { id, enabled } — master-catalog enable/disable (applies to every site). */
export async function POST(req: NextRequest) {
  const admin = await getAdmin();
  if (!admin) return NextResponse.json({ ok: false, error: 'Unauthorized' }, { status: 401 });

  const body = await req.json().catch(() => null);
  if (!body?.id || typeof body.enabled !== 'boolean') {
    return NextResponse.json({ ok: false, error: 'id and enabled required' }, { status: 400 });
  }

  const db = createSupabaseAdminClient();
  const { error } = await db.from('products')
    .update({ enabled: body.enabled, updated_at: new Date().toISOString() })
    .eq('id', body.id);
  if (error) return NextResponse.json({ ok: false, error: error.message }, { status: 500 });
  return NextResponse.json({ ok: true });
}
