import { NextRequest, NextResponse } from 'next/server';
import { getAdmin } from '@/lib/auth';
import { createSupabaseAdminClient } from '@/lib/supabase/admin';

export const dynamic = 'force-dynamic';

/**
 * GET /api/preview/[id] — serve the request's stored homepage HTML as a real
 * rendered page. Supabase Storage deliberately serves HTML as text/plain
 * (anti-phishing), so the review iframe / "open full page" go through here.
 * Admin-only (same-origin cookies cover the iframe and new tabs).
 */
export async function GET(_req: NextRequest, { params }: { params: { id: string } }) {
  const admin = await getAdmin();
  if (!admin) return new NextResponse('Unauthorized', { status: 401 });

  const db = createSupabaseAdminClient();
  const { data } = await db.from('site_requests').select('html_source').eq('id', params.id).single();
  const html = data?.html_source;
  if (!html) return new NextResponse('No HTML generated yet.', { status: 404 });

  return new NextResponse(html, {
    headers: {
      'Content-Type': 'text/html; charset=utf-8',
      'Cache-Control': 'no-store',
    },
  });
}
