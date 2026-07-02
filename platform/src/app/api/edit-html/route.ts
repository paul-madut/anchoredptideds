import { NextRequest, NextResponse } from 'next/server';
import { getAdmin } from '@/lib/auth';
import { createSupabaseAdminClient } from '@/lib/supabase/admin';
import { hasOpenAI, chatText } from '@/lib/openai';
import { setHtmlSource } from '@/lib/artifacts';
import type { SiteRequest } from '@/lib/types';

export const maxDuration = 90;

/**
 * POST /api/edit-html  { requestId, prompt }
 * Admin-only. Sends the current homepage HTML + an instruction to the model and
 * stores the edited HTML as the new source of truth.
 */
export async function POST(req: NextRequest) {
  const admin = await getAdmin();
  if (!admin) return NextResponse.json({ ok: false, error: 'Unauthorized' }, { status: 401 });
  if (!hasOpenAI()) return NextResponse.json({ ok: false, error: 'AI not configured.' }, { status: 501 });

  const body = await req.json().catch(() => null);
  const requestId = body?.requestId;
  const prompt = (body?.prompt ?? '').trim();
  if (!requestId || !prompt) return NextResponse.json({ ok: false, error: 'requestId and prompt required' }, { status: 400 });

  const db = createSupabaseAdminClient();
  const { data } = await db.from('site_requests').select('*').eq('id', requestId).single();
  const row = data as SiteRequest | null;
  if (!row?.html_source) return NextResponse.json({ ok: false, error: 'No HTML to edit yet — approve first.' }, { status: 400 });

  try {
    const out = await chatText(
      'You are an expert web designer editing a single self-contained HTML document. ' +
        'Apply the user’s change and return the COMPLETE updated HTML document only — no markdown, no code fences, no commentary. ' +
        'Preserve the existing <style> and structure unless the change requires otherwise. Keep it a valid standalone HTML file.',
      `Current HTML:\n\n${row.html_source}\n\n---\nChange to make: ${prompt}`,
    );
    const html = stripFences(out);
    if (!/<html|<body|<!doctype/i.test(html)) return NextResponse.json({ ok: false, error: 'Model did not return valid HTML.' }, { status: 502 });

    const saved = await setHtmlSource(requestId, html);
    return NextResponse.json(saved, { status: saved.ok ? 200 : 500 });
  } catch (e) {
    return NextResponse.json({ ok: false, error: (e as Error).message }, { status: 502 });
  }
}

function stripFences(s: string): string {
  return s.replace(/^\s*```(?:html)?\s*/i, '').replace(/\s*```\s*$/i, '').trim();
}
