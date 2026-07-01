import { NextRequest, NextResponse } from 'next/server';
import { getAdmin } from '@/lib/auth';
import { runDeploy } from '@/lib/deploy';

// Provisioning is slow (image sideload + catalog import). Give the route room.
export const maxDuration = 300;

/**
 * POST /api/deploy  { requestId }
 * Admin-only. Triggers the one-click deploy for a site request. Returns the
 * final DeployResult; the CRM also polls the row's status while this runs.
 */
export async function POST(req: NextRequest) {
  const admin = await getAdmin();
  if (!admin) return NextResponse.json({ ok: false, error: 'Unauthorized' }, { status: 401 });

  const body = await req.json().catch(() => null);
  const requestId = body?.requestId;
  if (!requestId || typeof requestId !== 'string') {
    return NextResponse.json({ ok: false, error: 'requestId required' }, { status: 400 });
  }

  const result = await runDeploy(requestId);
  return NextResponse.json(result, { status: result.ok ? 200 : 502 });
}
