import Link from 'next/link';

export default function Home() {
  return (
    <main style={{ minHeight: '100dvh', display: 'grid', placeItems: 'center', padding: '24px' }}>
      <div style={{ maxWidth: 640, textAlign: 'center' }}>
        <p className="eyebrow rise" style={{ animationDelay: '0.05s' }}>Peptide Site Studio</p>
        <h1 className="display rise" style={{ fontSize: 'clamp(38px, 7vw, 62px)', margin: '18px 0 18px', animationDelay: '0.12s' }}>
          Design your premium<br />peptide storefront.
        </h1>
        <p className="muted rise" style={{ fontSize: 18, lineHeight: 1.6, maxWidth: 500, margin: '0 auto 34px', animationDelay: '0.2s' }}>
          Answer a few questions about your brand — name, palette, type, and feel — and watch a real
          store take shape. About ten minutes.
        </p>
        <div className="rise" style={{ animationDelay: '0.28s' }}>
          <Link href="/intake" className="btn" style={{ display: 'inline-block', fontSize: 16, padding: '15px 30px' }}>
            Start designing →
          </Link>
        </div>
      </div>
    </main>
  );
}
