'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { createSupabaseBrowserClient } from '@/lib/supabase/client';

export default function AdminLogin() {
  const router = useRouter();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  async function signIn(e: React.FormEvent) {
    e.preventDefault();
    setBusy(true);
    setError('');
    const supabase = createSupabaseBrowserClient();
    const { error } = await supabase.auth.signInWithPassword({ email, password });
    setBusy(false);
    if (error) { setError(error.message); return; }
    router.replace('/admin');
    router.refresh();
  }

  return (
    <main style={{ minHeight: '100dvh', display: 'grid', placeItems: 'center', padding: 24 }}>
      <div className="rise" style={{ width: '100%', maxWidth: 360 }}>
        <p className="eyebrow">Peptide Site Studio</p>
        <h1 className="display" style={{ fontSize: 34, margin: '10px 0 4px' }}>Admin sign in</h1>
        <p className="muted" style={{ fontSize: 14, marginTop: 0 }}>Site build queue.</p>
        <form onSubmit={signIn} style={{ display: 'grid', gap: 12, marginTop: 24 }}>
          <input type="email" placeholder="Email" value={email} onChange={(e) => setEmail(e.target.value)} required autoFocus />
          <input type="password" placeholder="Password" value={password} onChange={(e) => setPassword(e.target.value)} required />
          {error && <p style={{ color: '#9a3b2b', fontSize: 13, margin: 0 }}>{error}</p>}
          <button className="btn" disabled={busy}>{busy ? 'Signing in…' : 'Sign in →'}</button>
        </form>
      </div>
    </main>
  );
}
