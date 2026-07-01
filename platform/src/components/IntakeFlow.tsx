'use client';

import { useMemo, useState } from 'react';
import type { Preset } from '@/lib/presets';
import Preview from './Preview';

const CATEGORIES: [string, string][] = [
  ['weight-loss', 'Weight Loss'],
  ['energy', 'Energy'],
  ['healing', 'Healing'],
  ['skin', 'Skin'],
  ['brain', 'Brain'],
  ['stacks', 'Stacks'],
];

type Copy = Record<string, string>;

const DEFAULT_COPY: Copy = {
  hero_eyebrow: 'Research-grade quality',
  hero_h1: 'Peptides That',
  hero_h1_em: 'Stay Grounded',
  hero_sub: 'Third-party HPLC-tested peptides for serious researchers. Purity you can trust.',
  hero_cta_primary: 'Browse Catalog',
  hero_cta_secondary: 'Learn More',
  tagline: 'Stay true, stay anchored.',
};

interface HeroOption { path: string; url: string; }

export default function IntakeFlow({ presets }: { presets: Preset[] }) {
  const [step, setStep] = useState(0);
  const [businessName, setBusinessName] = useState('');
  const [positioning, setPositioning] = useState('');
  const [emphasis, setEmphasis] = useState<string[]>([]);
  const [presetKey, setPresetKey] = useState(presets[0]?.key ?? '');
  const [copy, setCopy] = useState<Copy>(DEFAULT_COPY);
  const [logo, setLogo] = useState<{ dataUrl: string; name: string } | null>(null);
  const [heroOptions, setHeroOptions] = useState<HeroOption[]>([]);
  const [heroSel, setHeroSel] = useState<HeroOption | null>(null);
  const [genState, setGenState] = useState<'idle' | 'loading' | 'error' | 'unavailable'>('idle');
  const [genError, setGenError] = useState('');
  const [customerName, setCustomerName] = useState('');
  const [customerEmail, setCustomerEmail] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [submittedId, setSubmittedId] = useState<string | null>(null);
  const [submitError, setSubmitError] = useState('');
  const [aiBusy, setAiBusy] = useState<string | null>(null);
  const [aiNote, setAiNote] = useState('');

  const preset = useMemo(() => presets.find((p) => p.key === presetKey) ?? presets[0], [presets, presetKey]);

  const setCopyField = (k: string, v: string) => setCopy((c) => ({ ...c, [k]: v }));

  const steps = ['Brand', 'Focus', 'Theme', 'Copy', 'Logo', 'Hero image', 'Finish'];
  const last = steps.length - 1;
  const canNext = step === 0 ? businessName.trim().length > 1 : step === last ? customerEmail.includes('@') : true;

  async function generateHero(autoSelect = false): Promise<HeroOption[]> {
    setGenState('loading');
    setGenError('');
    try {
      const res = await fetch('/api/hero-image', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ businessName, positioning, presetKey, count: 3 }),
      });
      if (res.status === 501) { setGenState('unavailable'); return []; }
      const json = await res.json();
      if (!json.ok) { setGenState('error'); setGenError(json.error ?? 'Generation failed'); return []; }
      setHeroOptions(json.images);
      setGenState('idle');
      if (autoSelect && json.images?.[0]) setHeroSel(json.images[0]);
      return json.images ?? [];
    } catch (e) {
      setGenState('error');
      setGenError((e as Error).message);
      return [];
    }
  }

  /** "Let AI choose" — fills the current step from OpenAI. */
  async function runAssist(assistStep: string) {
    setAiBusy(assistStep);
    setAiNote('');
    try {
      if (assistStep === 'hero') { await generateHero(true); return; }
      const res = await fetch('/api/ai-assist', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ step: assistStep, context: { businessName, positioning, emphasis, presetKey } }),
      });
      if (res.status === 501) { setAiNote('AI isn’t configured yet — add OPENAI_API_KEY to .env.local.'); return; }
      const json = await res.json();
      if (!json.ok) { setAiNote(json.error ?? 'AI request failed'); return; }
      if (assistStep === 'brand') {
        if (json.business_name) setBusinessName(json.business_name);
        if (json.positioning) setPositioning(json.positioning);
      } else if (assistStep === 'focus') {
        if (Array.isArray(json.emphasis)) setEmphasis(json.emphasis);
      } else if (assistStep === 'theme') {
        if (json.preset_key) setPresetKey(json.preset_key);
      } else if (assistStep === 'copy') {
        if (json.copy) setCopy((c) => ({ ...c, ...json.copy }));
      }
    } catch (e) {
      setAiNote((e as Error).message);
    } finally {
      setAiBusy(null);
    }
  }

  async function submit() {
    setSubmitting(true);
    setSubmitError('');
    try {
      const res = await fetch('/api/intake', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          business_name: businessName,
          positioning,
          emphasis_categories: emphasis,
          preset_key: presetKey,
          copy,
          logo_data_url: logo?.dataUrl,
          logo_name: logo?.name,
          hero_image_path: heroSel?.path ?? null,
          customer_name: customerName,
          customer_email: customerEmail,
          answers: { businessName, positioning, emphasis, presetKey },
        }),
      });
      const json = await res.json();
      if (!json.ok) { setSubmitError(json.error ?? 'Something went wrong'); return; }
      setSubmittedId(json.id);
    } catch (e) {
      setSubmitError((e as Error).message);
    } finally {
      setSubmitting(false);
    }
  }

  if (submittedId) {
    return (
      <Shell preset={preset} businessName={businessName} copy={copy} logo={logo} hero={heroSel}>
        <div style={{ textAlign: 'center', paddingTop: 40 }}>
          <p className="pill">Submitted</p>
          <h2 style={{ fontSize: 30, margin: '16px 0 10px' }}>Your store design is in.</h2>
          <p className="muted" style={{ maxWidth: 380 }}>
            Our team will review and build it out. We&apos;ll email <b>{customerEmail}</b> when your
            preview site is ready. Reference: <code>{submittedId.slice(0, 8)}</code>
          </p>
        </div>
      </Shell>
    );
  }

  return (
    <Shell preset={preset} businessName={businessName} copy={copy} logo={logo} hero={heroSel}>
      <div style={{ marginBottom: 22 }}>
        <div style={{ display: 'flex', gap: 6, marginBottom: 8 }}>
          {steps.map((_, i) => (
            <div key={i} style={{ height: 4, flex: 1, borderRadius: 4, background: i <= step ? 'var(--accent)' : 'var(--line)' }} />
          ))}
        </div>
        <p className="muted" style={{ fontSize: 13, margin: 0 }}>Step {step + 1} of {steps.length} · {steps[step]}</p>
        {aiNote && <p style={{ color: '#a8503b', fontSize: 13, margin: '8px 0 0' }}>{aiNote}</p>}
      </div>

      {step === 0 && (
        <Field>
          <StepHead label="What's your brand name?" assist="brand" aiBusy={aiBusy} runAssist={runAssist} />
          <input value={businessName} onChange={(e) => setBusinessName(e.target.value)} placeholder="e.g. Meridian Peptides" autoFocus />
          <label style={{ marginTop: 18 }}>One line on how you want to be seen (optional)</label>
          <input value={positioning} onChange={(e) => setPositioning(e.target.value)} placeholder="e.g. clinical, premium, trusted by researchers" />
        </Field>
      )}

      {step === 1 && (
        <Field>
          <StepHead label="Which goals should your store lead with?" assist="focus" aiBusy={aiBusy} runAssist={runAssist} />
          <p className="muted" style={{ fontSize: 13, marginTop: 0 }}>Pick any that fit — this shapes your category emphasis.</p>
          <div style={{ display: 'flex', flexWrap: 'wrap', gap: 8 }}>
            {CATEGORIES.map(([slug, label]) => {
              const on = emphasis.includes(slug);
              return (
                <button key={slug} type="button" onClick={() => setEmphasis((e) => (on ? e.filter((x) => x !== slug) : [...e, slug]))}
                  className="pill" style={{ cursor: 'pointer', background: on ? 'var(--accent)' : '#fff', color: on ? '#f4f0e6' : 'var(--ink)', borderColor: on ? 'var(--accent)' : 'var(--line)' }}>
                  {label}
                </button>
              );
            })}
          </div>
        </Field>
      )}

      {step === 2 && (
        <Field>
          <StepHead label="Choose your theme" assist="theme" aiBusy={aiBusy} runAssist={runAssist} />
          <p className="muted" style={{ fontSize: 13, marginTop: 0 }}>Palette + type. The preview updates live →</p>
          <div style={{ display: 'grid', gap: 10 }}>
            {presets.map((p) => (
              <button key={p.key} type="button" onClick={() => setPresetKey(p.key)} className="card"
                style={{ textAlign: 'left', padding: 14, cursor: 'pointer', display: 'flex', alignItems: 'center', gap: 14, outline: p.key === presetKey ? '2px solid var(--accent)' : 'none' }}>
                <span style={{ display: 'flex', gap: 4 }}>
                  {['--ap-bg', '--ap-olive', '--ap-ink', '--ap-cream2'].map((t) => (
                    <span key={t} style={{ width: 22, height: 22, borderRadius: 6, background: p.tokens[t], border: '1px solid rgba(0,0,0,.08)' }} />
                  ))}
                </span>
                <span>
                  <b style={{ display: 'block' }}>{p.label}</b>
                  <small className="muted">{p.description}</small>
                </span>
              </button>
            ))}
          </div>
        </Field>
      )}

      {step === 3 && (
        <Field>
          <StepHead label="Headline & hero copy" assist="copy" aiBusy={aiBusy} runAssist={runAssist} />
          <div style={{ display: 'grid', gap: 10 }}>
            <Small label="Eyebrow"><input value={copy.hero_eyebrow} onChange={(e) => setCopyField('hero_eyebrow', e.target.value)} /></Small>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10 }}>
              <Small label="Headline"><input value={copy.hero_h1} onChange={(e) => setCopyField('hero_h1', e.target.value)} /></Small>
              <Small label="Headline emphasis"><input value={copy.hero_h1_em} onChange={(e) => setCopyField('hero_h1_em', e.target.value)} /></Small>
            </div>
            <Small label="Subheadline"><textarea rows={2} value={copy.hero_sub} onChange={(e) => setCopyField('hero_sub', e.target.value)} /></Small>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10 }}>
              <Small label="Primary button"><input value={copy.hero_cta_primary} onChange={(e) => setCopyField('hero_cta_primary', e.target.value)} /></Small>
              <Small label="Secondary button"><input value={copy.hero_cta_secondary} onChange={(e) => setCopyField('hero_cta_secondary', e.target.value)} /></Small>
            </div>
            <Small label="Tagline"><input value={copy.tagline} onChange={(e) => setCopyField('tagline', e.target.value)} /></Small>
          </div>
        </Field>
      )}

      {step === 4 && (
        <Field>
          <label>Upload your logo (optional)</label>
          <p className="muted" style={{ fontSize: 13, marginTop: 0 }}>PNG, SVG, or WebP. Skip it and we&apos;ll use a clean wordmark.</p>
          <input type="file" accept="image/png,image/jpeg,image/svg+xml,image/webp" onChange={(e) => {
            const f = e.target.files?.[0];
            if (!f) return;
            const reader = new FileReader();
            reader.onload = () => setLogo({ dataUrl: String(reader.result), name: f.name });
            reader.readAsDataURL(f);
          }} />
          {logo && <p className="muted" style={{ fontSize: 13 }}>Selected: {logo.name} — <button className="btn-ghost" style={{ padding: '2px 8px', borderRadius: 8 }} onClick={() => setLogo(null)}>remove</button></p>}
        </Field>
      )}

      {step === 5 && (
        <Field>
          <StepHead label="Generate a hero image" assist="hero" aiBusy={aiBusy} runAssist={runAssist} />
          <p className="muted" style={{ fontSize: 13, marginTop: 0 }}>AI-generated art tuned to your theme. Pick your favorite, or let AI choose.</p>
          <button className="btn" onClick={() => generateHero()} disabled={genState === 'loading'}>
            {genState === 'loading' ? 'Generating…' : heroOptions.length ? 'Regenerate' : 'Generate options'}
          </button>
          {genState === 'unavailable' && <p className="muted" style={{ fontSize: 13 }}>Image generation isn&apos;t configured yet — you can continue and add art during build.</p>}
          {genState === 'error' && <p style={{ color: '#a8503b', fontSize: 13 }}>{genError}</p>}
          <div style={{ display: 'flex', gap: 10, marginTop: 12, flexWrap: 'wrap' }}>
            {heroOptions.map((h) => (
              <button key={h.path} type="button" onClick={() => setHeroSel(h)} style={{ padding: 0, border: heroSel?.path === h.path ? '2px solid var(--accent)' : '1px solid var(--line)', borderRadius: 10, overflow: 'hidden', cursor: 'pointer', background: 'none' }}>
                <img src={h.url} alt="hero option" style={{ width: 120, height: 120, objectFit: 'cover', display: 'block' }} />
              </button>
            ))}
          </div>
        </Field>
      )}

      {step === 6 && (
        <Field>
          <label>Where should we send your preview?</label>
          <div style={{ display: 'grid', gap: 10 }}>
            <Small label="Your name"><input value={customerName} onChange={(e) => setCustomerName(e.target.value)} /></Small>
            <Small label="Email"><input type="email" value={customerEmail} onChange={(e) => setCustomerEmail(e.target.value)} placeholder="you@brand.com" /></Small>
          </div>
          {submitError && <p style={{ color: '#a8503b', fontSize: 13 }}>{submitError}</p>}
        </Field>
      )}

      <div style={{ display: 'flex', justifyContent: 'space-between', marginTop: 28 }}>
        <button className="btn-ghost" style={{ borderRadius: 40, padding: '12px 20px', cursor: 'pointer' }} disabled={step === 0} onClick={() => setStep((s) => Math.max(0, s - 1))}>← Back</button>
        {step < last ? (
          <button className="btn" disabled={!canNext} onClick={() => setStep((s) => s + 1)}>Continue →</button>
        ) : (
          <button className="btn" disabled={!canNext || submitting} onClick={submit}>{submitting ? 'Submitting…' : 'Submit design'}</button>
        )}
      </div>
    </Shell>
  );
}

/** Two-pane layout: form on the left, live preview on the right. */
function Shell({ preset, businessName, copy, logo, hero, children }: {
  preset: Preset; businessName: string; copy: Copy; logo: { dataUrl: string } | null; hero: HeroOption | null; children: React.ReactNode;
}) {
  return (
    <main style={{ display: 'grid', gridTemplateColumns: 'minmax(360px, 460px) 1fr', minHeight: '100vh' }}>
      <div style={{ padding: '48px 40px', maxWidth: 460 }}>
        <div style={{ marginBottom: 28, fontWeight: 700 }}>Peptide Site Studio</div>
        {children}
      </div>
      <div style={{ background: '#e9e5db', borderLeft: '1px solid var(--line)', padding: 24, overflow: 'hidden' }}>
        <div style={{ fontSize: 12, textTransform: 'uppercase', letterSpacing: '.12em', color: 'var(--muted)', marginBottom: 12 }}>Live preview</div>
        <div style={{ background: '#fff', borderRadius: 12, overflow: 'hidden', boxShadow: '0 10px 40px rgba(0,0,0,.1)', transform: 'scale(0.92)', transformOrigin: 'top center' }}>
          <Preview
            tokens={preset.tokens}
            fonts={preset.fonts}
            brandName={businessName || 'Your Brand'}
            logoUrl={logo?.dataUrl}
            heroImageUrl={hero?.url}
            copy={copy}
          />
        </div>
      </div>
    </main>
  );
}

function Field({ children }: { children: React.ReactNode }) {
  return <div style={{ display: 'grid', gap: 6 }}>{children}</div>;
}

/** Step header with a "Let AI choose" button on the right. */
function StepHead({ label, assist, aiBusy, runAssist }: {
  label: string; assist: string; aiBusy: string | null; runAssist: (s: string) => void;
}) {
  const busy = aiBusy === assist;
  return (
    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: 12 }}>
      <label style={{ margin: 0 }}>{label}</label>
      <button type="button" onClick={() => runAssist(assist)} disabled={!!aiBusy}
        title="Let AI choose for you"
        style={{ flex: '0 0 auto', display: 'inline-flex', alignItems: 'center', gap: 6, cursor: aiBusy ? 'default' : 'pointer', background: 'transparent', color: 'var(--accent)', border: '1px solid var(--line)', borderRadius: 40, padding: '5px 12px', fontSize: 12, fontWeight: 600, opacity: aiBusy && !busy ? 0.4 : 1 }}>
        {busy ? '✨ Thinking…' : '✨ Let AI choose'}
      </button>
    </div>
  );
}
function Small({ label, children }: { label: string; children: React.ReactNode }) {
  return (
    <label style={{ display: 'grid', gap: 4, fontSize: 13, color: 'var(--muted)' }}>
      {label}
      {children}
    </label>
  );
}
