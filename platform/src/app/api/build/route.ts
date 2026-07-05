import { NextRequest, NextResponse } from 'next/server';
import { getAdmin } from '@/lib/auth';
import { generateBundle } from '@/lib/artifacts';

// Bundle assembly fetches the logo/hero assets and zips the theme + plugins.
export const maxDuration = 180;

/**
 * POST /api/build  { requestId }  — the FINAL step (files-first).
 * Admin-only. Assembles the self-contained WordPress bundle (theme + plugins +
 * brand-config + catalog) from the reviewed HTML and returns its download URL.
 * No target credentials required — this is the manual-install deliverable. The
 * optional automated push (/api/deploy) rebuilds the same bundle before sending.
 */
export async function POST(req: NextRequest) {
  const admin = await getAdmin();
  if (!admin) return NextResponse.json({ ok: false, error: 'Unauthorized' }, { status: 401 });

  const body = await req.json().catch(() => null);
  const requestId = body?.requestId;
  if (!requestId || typeof requestId !== 'string') {
    return NextResponse.json({ ok: false, error: 'requestId required' }, { status: 400 });
  }

  const result = await generateBundle(requestId);
  return NextResponse.json(result, { status: result.ok ? 200 : 500 });
}
