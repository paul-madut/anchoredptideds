import { NextRequest, NextResponse } from 'next/server';
import { getAdmin } from '@/lib/auth';
import { createSupabaseAdminClient } from '@/lib/supabase/admin';
import { hasOpenAI, chatJSON } from '@/lib/openai';
import { setHtmlSource } from '@/lib/artifacts';
import type { SiteRequest } from '@/lib/types';

export const maxDuration = 90;

/**
 * POST /api/edit-html  { requestId, prompt }
 * Admin-only. AI edits the stored homepage HTML via targeted search/replace
 * operations (NOT a full-document rewrite — the homepage is ~80KB, which would
 * blow past model output limits and risk mangling untouched sections).
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
    const out = await chatJSON(
      'You are an expert web developer editing a large self-contained HTML homepage. ' +
        'You are given the FULL document and a change request. Respond with JSON: ' +
        '{"edits":[{"find":"<exact substring from the document>","replace":"<replacement>"}], "note":"<one short sentence describing what you changed>"}. ' +
        'Rules: each "find" MUST be copied verbatim from the document (including whitespace and quoting) and long enough to be unique — include surrounding markup for context. ' +
        'To insert new content, use a "find" at the insertion point and include it unchanged inside "replace" along with the new markup. ' +
        'Keep the document a valid standalone HTML file; reuse the existing var(--ap-*) design tokens and inline-style conventions. ' +
        'Prefer a handful of surgical edits over large rewrites. Never edit inside the <style> font-face or :root blocks unless the request is about colors/fonts.',
      `Change request: ${prompt}\n\nFull document:\n\n${row.html_source}`,
      { temperature: 0.2 },
    );

    const edits = Array.isArray(out.edits) ? (out.edits as Array<{ find?: string; replace?: string }>) : [];
    if (!edits.length) return NextResponse.json({ ok: false, error: 'AI returned no edits — try rephrasing the request.' }, { status: 502 });

    let html = row.html_source;
    const failed: string[] = [];
    let applied = 0;
    for (const e of edits) {
      if (typeof e.find !== 'string' || typeof e.replace !== 'string' || !e.find) continue;
      if (!html.includes(e.find)) { failed.push(e.find.slice(0, 60)); continue; }
      html = html.split(e.find).join(e.replace);
      applied++;
    }
    if (!applied) {
      return NextResponse.json({ ok: false, error: 'AI edits did not match the document — try rephrasing the request.' }, { status: 502 });
    }
    if (!/<html|<body|<!doctype/i.test(html)) {
      return NextResponse.json({ ok: false, error: 'Edit would break the document; rejected.' }, { status: 502 });
    }

    const saved = await setHtmlSource(requestId, html);
    if (!saved.ok) return NextResponse.json(saved, { status: 500 });
    const note = typeof out.note === 'string' ? out.note : undefined;
    return NextResponse.json({
      ...saved,
      note,
      applied,
      skipped: failed.length,
      ...(failed.length ? { warning: `${failed.length} edit(s) did not match and were skipped.` } : {}),
    });
  } catch (e) {
    return NextResponse.json({ ok: false, error: (e as Error).message }, { status: 502 });
  }
}
