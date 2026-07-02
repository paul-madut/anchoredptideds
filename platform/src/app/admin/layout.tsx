import Link from 'next/link';

export const dynamic = 'force-dynamic';

export default function AdminLayout({ children }: { children: React.ReactNode }) {
  return (
    <div>
      <header style={{ borderBottom: '1px solid var(--line)', padding: '16px 28px', display: 'flex', justifyContent: 'space-between', alignItems: 'center', background: 'color-mix(in srgb, var(--paper) 85%, transparent)', backdropFilter: 'blur(8px)', position: 'sticky', top: 0, zIndex: 10 }}>
        <Link href="/admin" className="serif" style={{ fontWeight: 600, fontSize: 18 }}>Site Build Queue</Link>
        <span className="eyebrow">Admin</span>
      </header>
      <div style={{ maxWidth: 1120, margin: '0 auto', padding: '32px 24px 80px' }}>{children}</div>
    </div>
  );
}
