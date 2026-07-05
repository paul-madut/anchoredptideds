'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';

/**
 * "Build WordPress files" — the files-first final step. Assembles the theme +
 * plugin bundle from the reviewed design (no customer login needed) and
 * refreshes so the download link appears. Rebuilds on demand if the design or
 * HTML changed after a prior build.
 */
export default function BuildFilesButton({ id, hasBundle }: { id: string; hasBundle: boolean }) {
  const router = useRouter();
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');
  const [warnings, setWarnings] = useState<string[]>([]);

  async function run() {
    setBusy(true);
    setError('');
    setWarnings([]);
    try {
      const res = await fetch('/api/build', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ requestId: id }),
      });
      const json = await res.json();
      if (!json.ok) { setError(json.error ?? 'Build failed'); return; }
      setWarnings(json.warnings ?? []);
      router.refresh();
    } catch (e) {
      setError((e as Error).message);
    } finally {
      setBusy(false);
    }
  }

  return (
    <div style={{ display: 'grid', gap: 8 }}>
      <button className="btn" onClick={run} disabled={busy}>
        {busy ? 'Building files…' : hasBundle ? 'Rebuild files' : 'Build WordPress files →'}
      </button>
      {error && <p style={{ color: '#a8503b', fontSize: 13, margin: 0 }}>{error}</p>}
      {warnings.length > 0 && <p className="muted" style={{ fontSize: 12, margin: 0 }}>Notes: {warnings.join('; ')}</p>}
    </div>
  );
}
