import { NextRequest, NextResponse } from 'next/server';
import { getAdmin } from '@/lib/auth';
import { createSupabaseAdminClient } from '@/lib/supabase/admin';

/** GET /api/request-status?id=... — admin-only status poll for the CRM. */
export async function GET(req: NextRequest) {
  const admin = await getAdmin();
  if (!admin) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });

  const id = req.nextUrl.searchParams.get('id');
  if (!id) return NextResponse.json({ error: 'id required' }, { status: 400 });

  const db = createSupabaseAdminClient();
  const { data, error } = await db
    .from('site_requests')
    .select('status, deployed_url, deploy_result')
    .eq('id', id)
    .single();
  if (error) return NextResponse.json({ error: error.message }, { status: 404 });
  return NextResponse.json(data);
}
