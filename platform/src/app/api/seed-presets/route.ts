import { NextResponse } from 'next/server';
import { getAdmin } from '@/lib/auth';
import { createSupabaseAdminClient } from '@/lib/supabase/admin';
import { PRESETS } from '@/lib/presets';

/**
 * POST /api/seed-presets — admin-only. Upserts the built-in presets into the DB
 * so site_requests.preset_key FKs resolve. Run once after migrating; safe to
 * re-run (idempotent upsert) to sync preset edits.
 */
export async function POST() {
  const admin = await getAdmin();
  if (!admin) return NextResponse.json({ ok: false, error: 'Unauthorized' }, { status: 401 });

  const db = createSupabaseAdminClient();
  const rows = PRESETS.map((p) => ({
    key: p.key,
    label: p.label,
    tokens: p.tokens,
    fonts: p.fonts,
    default_copy: {},
    sort: p.sort,
  }));

  const { error } = await db.from('presets').upsert(rows, { onConflict: 'key' });
  if (error) return NextResponse.json({ ok: false, error: error.message }, { status: 500 });
  return NextResponse.json({ ok: true, seeded: rows.length });
}
