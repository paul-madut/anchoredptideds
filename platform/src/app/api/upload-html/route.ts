import { NextRequest, NextResponse } from 'next/server';
import { getAdmin } from '@/lib/auth';
import { setHtmlSource } from '@/lib/artifacts';

export const maxDuration = 30;

/**
 * POST /api/upload-html  { requestId, html }
 * Admin-only. A dev re-uploads a hand-edited HTML file; it becomes the new
 * source of truth for this site.
 */
export async function POST(req: NextRequest) {
  const admin = await getAdmin();
  if (!admin) return NextResponse.json({ ok: false, error: 'Unauthorized' }, { status: 401 });

  const body = await req.json().catch(() => null);
  const requestId = body?.requestId;
  const html = body?.html;
  if (!requestId || typeof html !== 'string' || !/<html|<body|<!doctype/i.test(html)) {
    return NextResponse.json({ ok: false, error: 'requestId and a valid HTML document are required' }, { status: 400 });
  }
  if (html.length > 800_000) return NextResponse.json({ ok: false, error: 'HTML too large (max ~800KB).' }, { status: 400 });

  const saved = await setHtmlSource(requestId, html);
  return NextResponse.json(saved, { status: saved.ok ? 200 : 500 });
}
