import Link from 'next/link';

export default function Home() {
  return (
    <main style={{ maxWidth: 720, margin: '0 auto', padding: '96px 24px', textAlign: 'center' }}>
      <p className="pill">Peptide Site Studio</p>
      <h1 style={{ fontSize: 44, lineHeight: 1.1, margin: '20px 0 14px' }}>
        Design your premium peptide storefront.
      </h1>
      <p className="muted" style={{ fontSize: 18, maxWidth: 520, margin: '0 auto 32px' }}>
        Answer a few questions about your brand — name, palette, type, and feel — and preview a
        real store built to your look. Takes about 10 minutes.
      </p>
      <Link href="/intake" className="btn" style={{ display: 'inline-block', textDecoration: 'none' }}>
        Start designing →
      </Link>
    </main>
  );
}
