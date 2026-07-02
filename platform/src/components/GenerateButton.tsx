'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';

/**
 * "Approve & generate" — builds the standalone HTML + WordPress bundle for a
 * request, then refreshes so the Artifacts card appears. Regenerates if the
 * design changed.
 */
export default function GenerateButton({ id, hasArtifacts }: { id: string; hasArtifacts: boolean }) {
  const router = useRouter();
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');
  const [warnings, setWarnings] = useState<string[]>([]);

  async function run() {
    setBusy(true);
    setError('');
    setWarnings([]);
    try {
      const res = await fetch('/api/generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ requestId: id }),
      });
      const json = await res.json();
      if (!json.ok) { setError(json.error ?? 'Generation failed'); return; }
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
        {busy ? 'Generating HTML…' : hasArtifacts ? 'Re-approve (regenerate HTML)' : 'Approve design →'}
      </button>
      {error && <p style={{ color: '#a8503b', fontSize: 13, margin: 0 }}>{error}</p>}
      {warnings.length > 0 && <p className="muted" style={{ fontSize: 12, margin: 0 }}>Notes: {warnings.join('; ')}</p>}
    </div>
  );
}
