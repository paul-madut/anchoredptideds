'use client';

import { useRef, useState } from 'react';
import { useRouter } from 'next/navigation';

/**
 * Review & edit the generated homepage HTML before activation:
 *  - live iframe of the stored HTML
 *  - edit with an AI prompt
 *  - or download / hand-edit / re-upload the .html file
 * The HTML is the source of truth the WordPress site is built from.
 */
export default function HtmlReview({ id, htmlUrl }: { id: string; htmlUrl: string }) {
  const router = useRouter();
  const fileRef = useRef<HTMLInputElement>(null);
  const [bust, setBust] = useState(0);
  const [prompt, setPrompt] = useState('');
  const [busy, setBusy] = useState<'ai' | 'upload' | null>(null);
  const [error, setError] = useState('');

  const src = `${htmlUrl}${htmlUrl.includes('?') ? '&' : '?'}v=${bust}`;

  async function applyAi() {
    if (!prompt.trim()) return;
    setBusy('ai'); setError('');
    try {
      const res = await fetch('/api/edit-html', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ requestId: id, prompt }) });
      const json = await res.json();
      if (!json.ok) { setError(json.error ?? 'Edit failed'); return; }
      setPrompt(''); setBust((b) => b + 1); router.refresh();
    } catch (e) { setError((e as Error).message); } finally { setBusy(null); }
  }

  async function downloadHtml() {
    setError('');
    try {
      // `download` is ignored for cross-origin URLs, so fetch → blob → save.
      const res = await fetch(src, { cache: 'no-store' });
      const blob = await res.blob();
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'index.html';
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    } catch (e) { setError((e as Error).message); }
  }

  async function upload(file?: File | null) {
    if (!file) return;
    setBusy('upload'); setError('');
    try {
      const html = await file.text();
      const res = await fetch('/api/upload-html', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ requestId: id, html }) });
      const json = await res.json();
      if (!json.ok) { setError(json.error ?? 'Upload failed'); return; }
      setBust((b) => b + 1); router.refresh();
    } catch (e) { setError((e as Error).message); } finally { setBusy(null); if (fileRef.current) fileRef.current.value = ''; }
  }

  return (
    <div style={{ display: 'grid', gap: 12 }}>
      <div style={{ border: '1px solid var(--line)', borderRadius: 12, overflow: 'hidden', background: '#fff' }}>
        <iframe key={bust} src={src} title="HTML preview" style={{ width: '100%', height: 320, border: 'none', display: 'block' }} />
      </div>

      <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
        <a className="btn-ghost" href={src} target="_blank" rel="noreferrer" style={{ padding: '9px 15px', fontSize: 13 }}>Open full page ↗</a>
        <button className="btn-ghost" style={{ padding: '9px 15px', fontSize: 13 }} onClick={downloadHtml}>Download HTML ↓</button>
        <button className="btn-ghost" style={{ padding: '9px 15px', fontSize: 13 }} disabled={busy === 'upload'} onClick={() => fileRef.current?.click()}>
          {busy === 'upload' ? 'Uploading…' : 'Upload edited HTML ↑'}
        </button>
        <input ref={fileRef} type="file" accept="text/html,.html" style={{ display: 'none' }} onChange={(e) => upload(e.target.files?.[0])} />
      </div>

      <div style={{ display: 'grid', gap: 8 }}>
        <label style={{ fontSize: 13, color: 'var(--muted)', fontWeight: 500 }}>Edit with a prompt</label>
        <textarea rows={2} value={prompt} onChange={(e) => setPrompt(e.target.value)} placeholder="e.g. Make the hero headline larger and add a testimonials strip below the hero." />
        <div>
          <button className="btn" style={{ padding: '10px 18px', fontSize: 14 }} disabled={busy === 'ai' || !prompt.trim()} onClick={applyAi}>
            {busy === 'ai' ? 'Applying…' : '✦ Apply with AI'}
          </button>
        </div>
      </div>
      {error && <p style={{ color: '#9a3b2b', fontSize: 13, margin: 0 }}>{error}</p>}
    </div>
  );
}
