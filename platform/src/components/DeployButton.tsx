'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import type { SiteStatus, DeployResult } from '@/lib/types';
import { STATUS_META } from './statusMeta';

/**
 * One-click Activate. Fires POST /api/deploy, then polls request-status so the
 * pill reflects building -> deploying -> live | failed even though the deploy
 * request is long-running.
 */
export default function DeployButton({ id, initialStatus, ready }: { id: string; initialStatus: SiteStatus; ready: boolean }) {
  const router = useRouter();
  const [status, setStatus] = useState<SiteStatus>(initialStatus);
  const [running, setRunning] = useState(false);
  const [result, setResult] = useState<DeployResult | null>(null);

  const active = running || status === 'building' || status === 'deploying';

  async function poll() {
    for (let i = 0; i < 120; i++) {
      await new Promise((r) => setTimeout(r, 3000));
      try {
        const res = await fetch(`/api/request-status?id=${id}`, { cache: 'no-store' });
        if (!res.ok) continue;
        const json = await res.json();
        setStatus(json.status);
        if (json.status === 'live' || json.status === 'failed') {
          setResult(json.deploy_result ?? null);
          router.refresh();
          return;
        }
      } catch { /* keep polling */ }
    }
  }

  async function activate() {
    setRunning(true);
    setResult(null);
    setStatus('building');
    const pollPromise = poll();
    try {
      const res = await fetch('/api/deploy', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ requestId: id }),
      });
      const json = (await res.json()) as DeployResult;
      setResult(json);
      setStatus(json.ok ? 'live' : 'failed');
    } catch (e) {
      setResult({ ok: false, error: (e as Error).message });
      setStatus('failed');
    } finally {
      setRunning(false);
      await pollPromise;
      router.refresh();
    }
  }

  const meta = STATUS_META[status];

  return (
    <div style={{ display: 'grid', gap: 10 }}>
      <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
        <button className="btn" onClick={activate} disabled={!ready || active}>
          {active ? 'Activating…' : status === 'live' ? 'Re-activate' : 'Activate site'}
        </button>
        <span className="pill" style={{ background: meta.bg, color: meta.fg, borderColor: 'transparent' }}>{meta.label}</span>
      </div>
      {!ready && <p className="muted" style={{ fontSize: 13, margin: 0 }}>Add the target WordPress URL + Application Password below to enable deploy.</p>}
      {result?.ok && (
        <p style={{ fontSize: 13, margin: 0 }}>
          ✅ Live at <a href={result.live_url} target="_blank" rel="noreferrer">{result.live_url}</a>
          {result.counts && <> · imported {result.counts.imported ?? 0}, updated {result.counts.updated ?? 0}</>}
          {result.warnings?.length ? <><br /><span className="muted">Warnings: {result.warnings.join('; ')}</span></> : null}
        </p>
      )}
      {result && !result.ok && <p style={{ color: '#a8503b', fontSize: 13, margin: 0 }}>❌ {result.error}</p>}
    </div>
  );
}
