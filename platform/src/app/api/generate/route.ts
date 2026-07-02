import { NextRequest, NextResponse } from 'next/server';
import { getAdmin } from '@/lib/auth';
import { generateHtml } from '@/lib/artifacts';

export const maxDuration = 60;

/**
 * POST /api/generate  { requestId }  — the APPROVE step.
 * Admin-only. Renders the HTML design and stores it as the editable source
 * (`html_source`), marking the request approved. The WordPress bundle is built
 * later, at Activate.
 */
export async function POST(req: NextRequest) {
  const admin = await getAdmin();
  if (!admin) return NextResponse.json({ ok: false, error: 'Unauthorized' }, { status: 401 });

  const body = await req.json().catch(() => null);
  const requestId = body?.requestId;
  if (!requestId || typeof requestId !== 'string') {
    return NextResponse.json({ ok: false, error: 'requestId required' }, { status: 400 });
  }

  const result = await generateHtml(requestId);
  return NextResponse.json(result, { status: result.ok ? 200 : 500 });
}
