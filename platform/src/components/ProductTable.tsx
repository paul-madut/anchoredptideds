'use client';

import { useMemo, useRef, useState } from 'react';

export interface ProductItem {
  id: string;
  name: string;
  sku: string | null;
  type: string;
  category: string;
  price: string | null;
  variations: number;
  enabled: boolean;      // master flag
  imageUrl?: string;
  included?: boolean;    // per-site mode only
}

type Mode = 'master' | 'site';

/**
 * Catalog table used two ways:
 *  - master  (/admin/products): toggle = products.enabled + designer image upload
 *  - site    (/admin/[id]/products): toggle = include/exclude for THIS website;
 *            master-disabled products are shown locked.
 */
export default function ProductTable({ items: initial, mode, requestId }: { items: ProductItem[]; mode: Mode; requestId?: string }) {
  const [items, setItems] = useState(initial);
  const [q, setQ] = useState('');
  const [cat, setCat] = useState('all');
  const [busy, setBusy] = useState<string | null>(null);
  const [error, setError] = useState('');

  const cats = useMemo(() => ['all', ...[...new Set(initial.map((i) => i.category))].sort()], [initial]);
  const shown = useMemo(() => items.filter((i) =>
    (cat === 'all' || i.category === cat) &&
    (!q.trim() || (i.name + ' ' + (i.sku ?? '')).toLowerCase().includes(q.toLowerCase()))
  ), [items, q, cat]);

  const onCount = mode === 'master' ? items.filter((i) => i.enabled).length : items.filter((i) => i.enabled && i.included).length;

  async function toggle(item: ProductItem) {
    if (mode === 'site' && !item.enabled) return; // locked: disabled in master
    const next = mode === 'master' ? !item.enabled : !item.included;
    setBusy(item.id); setError('');
    try {
      const res = mode === 'master'
        ? await fetch('/api/products/toggle', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id: item.id, enabled: next }) })
        : await fetch('/api/site-products', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ requestId, productId: item.id, included: next }) });
      const json = await res.json();
      if (!json.ok) { setError(json.error ?? 'Update failed'); return; }
      setItems((xs) => xs.map((x) => (x.id === item.id ? { ...x, [mode === 'master' ? 'enabled' : 'included']: next } : x)));
    } catch (e) { setError((e as Error).message); } finally { setBusy(null); }
  }

  return (
    <div style={{ display: 'grid', gap: 14 }}>
      <div style={{ display: 'flex', gap: 8, alignItems: 'center', flexWrap: 'wrap' }}>
        <input value={q} onChange={(e) => setQ(e.target.value)} placeholder="Search products…" style={{ flex: 1, minWidth: 200 }} />
        <select value={cat} onChange={(e) => setCat(e.target.value)} style={{ padding: '10px 12px', borderRadius: 10, border: '1px solid var(--line-strong)', background: 'var(--card)', fontSize: 14 }}>
          {cats.map((c) => <option key={c} value={c}>{c === 'all' ? 'All categories' : c}</option>)}
        </select>
        <span className="pill">{onCount} / {items.length} {mode === 'master' ? 'enabled' : 'on this site'}</span>
      </div>
      {error && <p style={{ color: '#9a3b2b', fontSize: 13, margin: 0 }}>{error}</p>}

      <div style={{ display: 'grid', gap: 8 }}>
        {shown.map((item) => {
          const on = mode === 'master' ? item.enabled : (item.enabled && !!item.included);
          const locked = mode === 'site' && !item.enabled;
          return (
            <div key={item.id} className="card" style={{ display: 'flex', alignItems: 'center', gap: 14, padding: '10px 14px', opacity: locked ? 0.45 : on ? 1 : 0.55 }}>
              <Thumb item={item} canUpload={mode === 'master'} setItems={setItems} setError={setError} />
              <div style={{ flex: 1, minWidth: 0 }}>
                <b style={{ fontSize: 14.5, display: 'block', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{item.name}</b>
                <span className="muted" style={{ fontSize: 12 }}>
                  {item.category}{item.sku ? ` · ${item.sku}` : ''}{item.variations ? ` · ${item.variations} sizes` : ''}{item.price ? ` · from $${item.price}` : ''}
                  {locked ? ' · disabled in master catalog' : ''}
                </span>
              </div>
              <button type="button" onClick={() => toggle(item)} disabled={busy === item.id || locked} aria-label={on ? 'Disable' : 'Enable'}
                style={{ width: 42, height: 24, borderRadius: 20, border: 'none', cursor: locked ? 'not-allowed' : 'pointer', position: 'relative', flex: '0 0 auto', background: on ? 'var(--ink)' : 'var(--line-strong)', transition: 'background .2s' }}>
                <span style={{ position: 'absolute', top: 3, left: on ? 21 : 3, width: 18, height: 18, borderRadius: '50%', background: '#fff', transition: 'left .2s', boxShadow: '0 1px 3px rgba(0,0,0,.25)' }} />
              </button>
            </div>
          );
        })}
        {!shown.length && <p className="muted" style={{ textAlign: 'center', padding: 24 }}>No products match.</p>}
      </div>
    </div>
  );
}

function Thumb({ item, canUpload, setItems, setError }: {
  item: ProductItem; canUpload: boolean;
  setItems: React.Dispatch<React.SetStateAction<ProductItem[]>>;
  setError: (s: string) => void;
}) {
  const ref = useRef<HTMLInputElement>(null);
  const [busy, setBusy] = useState(false);

  async function upload(f?: File | null) {
    if (!f) return;
    if (!/^image\/(png|jpe?g|webp)$/.test(f.type)) { setError('Use a PNG, JPG, or WebP image.'); return; }
    if (f.size > 5 * 1024 * 1024) { setError('Image over 5 MB.'); return; }
    setBusy(true); setError('');
    try {
      const dataUrl = await new Promise<string>((res, rej) => {
        const r = new FileReader(); r.onload = () => res(String(r.result)); r.onerror = rej; r.readAsDataURL(f);
      });
      const resp = await fetch('/api/products/image', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id: item.id, image_data_url: dataUrl }) });
      const json = await resp.json();
      if (!json.ok) { setError(json.error ?? 'Upload failed'); return; }
      setItems((xs) => xs.map((x) => (x.id === item.id ? { ...x, imageUrl: json.url } : x)));
    } catch (e) { setError((e as Error).message); } finally { setBusy(false); if (ref.current) ref.current.value = ''; }
  }

  const box: React.CSSProperties = { width: 52, height: 52, borderRadius: 10, flex: '0 0 auto', border: '1px solid var(--line)', background: 'var(--paper)', display: 'flex', alignItems: 'center', justifyContent: 'center', overflow: 'hidden', cursor: canUpload ? 'pointer' : 'default', position: 'relative' };
  return (
    <div style={box} title={canUpload ? (item.imageUrl ? 'Replace image' : 'Upload image') : item.name} onClick={() => canUpload && ref.current?.click()}>
      {canUpload && <input ref={ref} type="file" accept="image/png,image/jpeg,image/webp" style={{ display: 'none' }} onChange={(e) => upload(e.target.files?.[0])} />}
      {busy ? <span className="muted" style={{ fontSize: 10 }}>…</span>
        : item.imageUrl ? <img src={item.imageUrl} alt={item.name} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
        : <span className="muted" style={{ fontSize: canUpload ? 18 : 11, lineHeight: 1 }}>{canUpload ? '+' : '—'}</span>}
    </div>
  );
}
