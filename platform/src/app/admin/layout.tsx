import Link from 'next/link';

export const dynamic = 'force-dynamic';

export default function AdminLayout({ children }: { children: React.ReactNode }) {
  return (
    <div>
      <header style={{ borderBottom: '1px solid var(--line)', padding: '14px 24px', display: 'flex', justifyContent: 'space-between', alignItems: 'center', background: '#fff' }}>
        <Link href="/admin" style={{ fontWeight: 700, textDecoration: 'none' }}>Site Build Queue</Link>
        <span className="muted" style={{ fontSize: 13 }}>Admin</span>
      </header>
      <div style={{ maxWidth: 1080, margin: '0 auto', padding: '28px 24px' }}>{children}</div>
    </div>
  );
}
