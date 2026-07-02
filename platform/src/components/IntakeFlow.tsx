'use client';

import { useEffect, useMemo, useRef, useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import type { Preset } from '@/lib/presets';
import { deriveFromPrimary, TOKEN_LABELS, isHex } from '@/lib/colorUtils';
import Preview from './Preview';

const CATEGORIES: [string, string][] = [
  ['weight-loss', 'Weight Loss'], ['energy', 'Energy'], ['healing', 'Healing'],
  ['skin', 'Skin'], ['brain', 'Brain'], ['stacks', 'Stacks'],
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

const STEPS = [
  { key: 'brand', title: 'Your brand', ai: 'brand', aiLabel: 'Name it for me', aiDesc: 'AI invents a brand name and a positioning line.' },
  { key: 'focus', title: 'Focus areas', ai: 'focus', aiLabel: 'Choose for me', aiDesc: 'AI selects the goal categories to feature.' },
  { key: 'theme', title: 'Theme', ai: 'theme', aiLabel: 'Pick for me', aiDesc: 'AI selects the theme that best fits your brand.' },
  { key: 'colors', title: 'Colors', ai: 'palette', aiLabel: 'Design a palette', aiDesc: 'AI generates a bespoke color palette.' },
  { key: 'copy', title: 'Copy', ai: 'copy', aiLabel: 'Write it for me', aiDesc: 'AI writes your homepage headline and hero copy.' },
  { key: 'logo', title: 'Logo', ai: null as string | null, aiLabel: '', aiDesc: '' },
  { key: 'hero', title: 'Hero image', ai: 'hero', aiLabel: 'Generate for me', aiDesc: 'AI creates hero artwork tuned to your theme.' },
  { key: 'finish', title: 'Finish', ai: null as string | null, aiLabel: '', aiDesc: '' },
];

export default function IntakeFlow({ presets }: { presets: Preset[] }) {
  const [step, setStep] = useState(0);
  const [dir, setDir] = useState(1);
  const [businessName, setBusinessName] = useState('');
  const [positioning, setPositioning] = useState('');
  const [emphasis, setEmphasis] = useState<string[]>([]);
  const [presetKey, setPresetKey] = useState(presets[0]?.key ?? '');
  const [overrides, setOverrides] = useState<Record<string, string>>({});
  const [advanced, setAdvanced] = useState(false);
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
  const [mobilePreview, setMobilePreview] = useState(false);

  const preset = useMemo(() => presets.find((p) => p.key === presetKey) ?? presets[0], [presets, presetKey]);
  const tokens = useMemo(() => ({ ...preset.tokens, ...overrides }), [preset, overrides]);
  const meta = STEPS[step];
  const last = STEPS.length - 1;
  const canNext = step === 0 ? businessName.trim().length > 1 : step === last ? customerEmail.includes('@') : true;

  function go(next: number) {
    setDir(next > step ? 1 : -1);
    setStep(Math.max(0, Math.min(last, next)));
    setAiNote('');
  }
  const setCopyField = (k: string, v: string) => setCopy((c) => ({ ...c, [k]: v }));
  function pickPreset(key: string) { setPresetKey(key); setOverrides({}); }

  async function generateHero(autoSelect = false): Promise<HeroOption[]> {
    setGenState('loading'); setGenError('');
    try {
      const res = await fetch('/api/hero-image', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ businessName, positioning, presetKey, count: 3 }) });
      if (res.status === 501) { setGenState('unavailable'); return []; }
      const json = await res.json();
      if (!json.ok) { setGenState('error'); setGenError(json.error ?? 'Generation failed'); return []; }
      setHeroOptions(json.images); setGenState('idle');
      if (autoSelect && json.images?.[0]) setHeroSel(json.images[0]);
      return json.images ?? [];
    } catch (e) { setGenState('error'); setGenError((e as Error).message); return []; }
  }

  async function runAssist(kind: string) {
    setAiBusy(kind); setAiNote('');
    try {
      if (kind === 'hero') { await generateHero(true); return; }
      const res = await fetch('/api/ai-assist', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ step: kind, context: { businessName, positioning, emphasis, presetKey } }) });
      if (res.status === 501) { setAiNote('AI isn’t configured yet — add OPENAI_API_KEY to .env.local.'); return; }
      const json = await res.json();
      if (!json.ok) { setAiNote(json.error ?? 'AI request failed'); return; }
      if (kind === 'brand') { if (json.business_name) setBusinessName(json.business_name); if (json.positioning) setPositioning(json.positioning); }
      else if (kind === 'focus') { if (Array.isArray(json.emphasis)) setEmphasis(json.emphasis); }
      else if (kind === 'theme') { if (json.preset_key) pickPreset(json.preset_key); }
      else if (kind === 'palette') { if (json.palette) setOverrides((o) => ({ ...o, ...deriveFromPrimary(json.palette) })); }
      else if (kind === 'copy') { if (json.copy) setCopy((c) => ({ ...c, ...json.copy })); }
    } catch (e) { setAiNote((e as Error).message); } finally { setAiBusy(null); }
  }

  async function submit() {
    setSubmitting(true); setSubmitError('');
    try {
      const res = await fetch('/api/intake', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({
        business_name: businessName, positioning, emphasis_categories: emphasis,
        preset_key: presetKey, tokens: overrides, copy,
        logo_data_url: logo?.dataUrl, logo_name: logo?.name, hero_image_path: heroSel?.path ?? null,
        customer_name: customerName, customer_email: customerEmail,
        answers: { businessName, positioning, emphasis, presetKey },
      }) });
      const json = await res.json();
      if (!json.ok) { setSubmitError(json.error ?? 'Something went wrong'); return; }
      setSubmittedId(json.id);
    } catch (e) { setSubmitError((e as Error).message); } finally { setSubmitting(false); }
  }

  const previewProps = { tokens, fonts: preset.fonts, brandName: businessName || 'Your Brand', logoUrl: logo?.dataUrl, heroImageUrl: heroSel?.url, copy };

  if (submittedId) {
    return (
      <div style={{ minHeight: '100dvh', display: 'grid', placeItems: 'center', padding: 24 }}>
        <motion.div initial={{ opacity: 0, y: 14 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5 }} style={{ textAlign: 'center', maxWidth: 440 }}>
          <p className="eyebrow">Submitted</p>
          <h1 className="display" style={{ fontSize: 40, margin: '14px 0 14px' }}>Your store design is in.</h1>
          <p className="muted" style={{ fontSize: 16, lineHeight: 1.6 }}>
            Our team will review and build it out. We’ll email <b style={{ color: 'var(--ink)' }}>{customerEmail}</b> when your preview site is ready.
          </p>
          <p className="pill" style={{ marginTop: 20 }}>Ref {submittedId.slice(0, 8)}</p>
        </motion.div>
      </div>
    );
  }

  return (
    <div className="ix-shell">
      <div className="ix-form-col">
        <div className="ix-topbar">
          <span className="serif" style={{ fontSize: 19, fontWeight: 600 }}>Peptide Site Studio</span>
          <button className="ix-ai ix-mobile-preview-btn" onClick={() => setMobilePreview(true)}>Preview →</button>
        </div>

        <div className="ix-progress">
          <div style={{ display: 'grid', gridTemplateColumns: `repeat(${STEPS.length}, 1fr)`, gap: 5 }}>
            {STEPS.map((_, i) => (
              <span key={i} className="ix-seg"><i style={{ transform: `scaleX(${i < step ? 1 : i === step ? 0.5 : 0})` }} /></span>
            ))}
          </div>
          <div style={{ display: 'flex', justifyContent: 'space-between', marginTop: 10 }}>
            <span className="eyebrow">{String(step + 1).padStart(2, '0')} — {meta.title}</span>
            <span className="eyebrow" style={{ color: 'var(--faint)' }}>{step + 1} / {STEPS.length}</span>
          </div>
          {aiNote && <p style={{ color: '#9a3b2b', fontSize: 13, margin: '10px 0 0' }}>{aiNote}</p>}
        </div>

        <div className="ix-scroll">
          <AnimatePresence mode="wait" custom={dir}>
            <motion.div key={step} custom={dir}
              initial={{ opacity: 0, x: dir * 26 }} animate={{ opacity: 1, x: 0 }} exit={{ opacity: 0, x: dir * -26 }}
              transition={{ duration: 0.32, ease: [0.22, 1, 0.36, 1] }}>

              {step === 0 && (
                <Step>
                  <StepHead title="What’s your brand called?" sub="The name your customers will see." meta={meta} aiBusy={aiBusy} runAssist={runAssist} />
                  <input value={businessName} onChange={(e) => setBusinessName(e.target.value)} placeholder="e.g. Meridian Peptides" autoFocus />
                  <label className="lbl">Positioning <span className="muted">(optional)</span></label>
                  <input value={positioning} onChange={(e) => setPositioning(e.target.value)} placeholder="e.g. clinical, premium, trusted by researchers" />
                </Step>
              )}

              {step === 1 && (
                <Step>
                  <StepHead title="What should the store lead with?" sub="Pick the goals to feature across the site." meta={meta} aiBusy={aiBusy} runAssist={runAssist} />
                  <div style={{ display: 'flex', flexWrap: 'wrap', gap: 9 }}>
                    {CATEGORIES.map(([slug, label], i) => (
                      <motion.button key={slug} type="button" className="ix-chip" data-on={emphasis.includes(slug)}
                        initial={{ opacity: 0, y: 8 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.04 * i, duration: 0.3 }}
                        onClick={() => setEmphasis((e) => (e.includes(slug) ? e.filter((x) => x !== slug) : [...e, slug]))}>
                        {label}
                      </motion.button>
                    ))}
                  </div>
                </Step>
              )}

              {step === 2 && (
                <Step>
                  <StepHead title="Choose a theme" sub="Palette and type. The preview updates instantly." meta={meta} aiBusy={aiBusy} runAssist={runAssist} />
                  <div style={{ display: 'grid', gap: 10 }}>
                    {presets.map((p, i) => (
                      <motion.button key={p.key} type="button" className="ix-theme" data-on={p.key === presetKey}
                        initial={{ opacity: 0, y: 8 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.03 * i, duration: 0.3 }}
                        onClick={() => pickPreset(p.key)}>
                        <span className="ix-swatches">
                          {['--ap-bg', '--ap-olive', '--ap-ink', '--ap-cream2'].map((t) => <i key={t} style={{ background: p.tokens[t], border: '1px solid rgba(0,0,0,.06)' }} />)}
                        </span>
                        <span>
                          <b style={{ display: 'block', fontSize: 15 }}>{p.label}</b>
                          <small className="muted" style={{ fontSize: 13 }}>{p.description}</small>
                        </span>
                      </motion.button>
                    ))}
                  </div>
                </Step>
              )}

              {step === 3 && (
                <Step>
                  <StepHead title="Fine-tune your colors" sub="Adjust the essentials, or open every color for full control." meta={meta} aiBusy={aiBusy} runAssist={runAssist} />
                  <div style={{ display: 'grid', gap: 12 }}>
                    <ColorRow label="Page background" token="--ap-bg" tokens={tokens} onChange={(hex) => setOverrides((o) => ({ ...o, ...deriveFromPrimary({ bg: hex }) }))} />
                    <ColorRow label="Text" token="--ap-ink" tokens={tokens} onChange={(hex) => setOverrides((o) => ({ ...o, ...deriveFromPrimary({ ink: hex }) }))} />
                    <ColorRow label="Accent / buttons" token="--ap-olive" tokens={tokens} onChange={(hex) => setOverrides((o) => ({ ...o, ...deriveFromPrimary({ accent: hex }) }))} />
                    <ColorRow label="Dark sections" token="--ap-dark" tokens={tokens} onChange={(hex) => setOverrides((o) => ({ ...o, ...deriveFromPrimary({ dark: hex }) }))} />
                  </div>
                  <div style={{ display: 'flex', gap: 12, alignItems: 'center', marginTop: 16 }}>
                    <button type="button" className="btn-ghost" style={{ padding: '9px 16px', fontSize: 13 }} onClick={() => setAdvanced((a) => !a)}>{advanced ? 'Hide' : 'All colors'} ({TOKEN_LABELS.length})</button>
                    {Object.keys(overrides).length > 0 && <button type="button" className="ix-ai" onClick={() => setOverrides({})}>Reset to theme</button>}
                  </div>
                  <AnimatePresence>
                    {advanced && (
                      <motion.div initial={{ opacity: 0, height: 0 }} animate={{ opacity: 1, height: 'auto' }} exit={{ opacity: 0, height: 0 }} style={{ overflow: 'hidden' }}>
                        <div style={{ display: 'grid', gap: 10, marginTop: 16, paddingTop: 16, borderTop: '1px solid var(--line)' }}>
                          {TOKEN_LABELS.map(([token, label]) => (
                            <ColorRow key={token} label={label} token={token} tokens={tokens} onChange={(hex) => setOverrides((o) => ({ ...o, [token]: hex }))} />
                          ))}
                        </div>
                      </motion.div>
                    )}
                  </AnimatePresence>
                </Step>
              )}

              {step === 4 && (
                <Step>
                  <StepHead title="Your homepage copy" sub="What visitors read first. Edit any line." meta={meta} aiBusy={aiBusy} runAssist={runAssist} />
                  <div style={{ display: 'grid', gap: 12 }}>
                    <Small label="Eyebrow"><input value={copy.hero_eyebrow} onChange={(e) => setCopyField('hero_eyebrow', e.target.value)} /></Small>
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10 }}>
                      <Small label="Headline"><input value={copy.hero_h1} onChange={(e) => setCopyField('hero_h1', e.target.value)} /></Small>
                      <Small label="Emphasis"><input value={copy.hero_h1_em} onChange={(e) => setCopyField('hero_h1_em', e.target.value)} /></Small>
                    </div>
                    <Small label="Subheadline"><textarea rows={2} value={copy.hero_sub} onChange={(e) => setCopyField('hero_sub', e.target.value)} /></Small>
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10 }}>
                      <Small label="Primary button"><input value={copy.hero_cta_primary} onChange={(e) => setCopyField('hero_cta_primary', e.target.value)} /></Small>
                      <Small label="Secondary button"><input value={copy.hero_cta_secondary} onChange={(e) => setCopyField('hero_cta_secondary', e.target.value)} /></Small>
                    </div>
                    <Small label="Tagline"><input value={copy.tagline} onChange={(e) => setCopyField('tagline', e.target.value)} /></Small>
                  </div>
                </Step>
              )}

              {step === 5 && (
                <Step>
                  <StepHead title="Add your logo" sub="Optional — skip it and we’ll use a clean wordmark." meta={meta} aiBusy={aiBusy} runAssist={runAssist} />
                  <LogoUpload logo={logo} setLogo={setLogo} />
                </Step>
              )}

              {step === 6 && (
                <Step>
                  <StepHead title="Generate a hero image" sub="AI artwork tuned to your palette. Pick your favorite." meta={meta} aiBusy={aiBusy} runAssist={runAssist} />
                  <button className="btn" onClick={() => generateHero()} disabled={genState === 'loading'}>
                    {genState === 'loading' ? 'Generating…' : heroOptions.length ? 'Regenerate options' : 'Generate options'}
                  </button>
                  {genState === 'unavailable' && <p className="muted" style={{ fontSize: 13, marginTop: 10 }}>Image generation isn’t configured — you can continue and add art during build.</p>}
                  {genState === 'error' && <p style={{ color: '#9a3b2b', fontSize: 13, marginTop: 10 }}>{genError}</p>}
                  <div style={{ display: 'flex', gap: 12, marginTop: 16, flexWrap: 'wrap' }}>
                    {heroOptions.map((h) => (
                      <button key={h.path} type="button" onClick={() => setHeroSel(h)}
                        style={{ padding: 0, border: heroSel?.path === h.path ? '2px solid var(--ink)' : '1px solid var(--line-strong)', borderRadius: 12, overflow: 'hidden', cursor: 'pointer', background: 'none', transition: 'transform .2s var(--ease)' }}>
                        <img src={h.url} alt="hero option" style={{ width: 128, height: 128, objectFit: 'cover', display: 'block' }} />
                      </button>
                    ))}
                  </div>
                </Step>
              )}

              {step === 7 && (
                <Step>
                  <StepHead title="Where should we send it?" sub="We’ll email your preview site when it’s ready." meta={meta} aiBusy={aiBusy} runAssist={runAssist} />
                  <div style={{ display: 'grid', gap: 12 }}>
                    <Small label="Your name"><input value={customerName} onChange={(e) => setCustomerName(e.target.value)} placeholder="Alex Rivera" /></Small>
                    <Small label="Email"><input type="email" value={customerEmail} onChange={(e) => setCustomerEmail(e.target.value)} placeholder="you@brand.com" /></Small>
                  </div>
                  {submitError && <p style={{ color: '#9a3b2b', fontSize: 13, marginTop: 10 }}>{submitError}</p>}
                </Step>
              )}
            </motion.div>
          </AnimatePresence>
        </div>

        <div className="ix-footer">
          <button className="btn-ghost" style={{ padding: '11px 20px' }} disabled={step === 0} onClick={() => go(step - 1)}>← Back</button>
          {step < last ? (
            <button className="btn" disabled={!canNext} onClick={() => go(step + 1)}>Continue →</button>
          ) : (
            <button className="btn" disabled={!canNext || submitting} onClick={submit}>{submitting ? 'Submitting…' : 'Submit design →'}</button>
          )}
        </div>
      </div>

      <aside className="ix-preview-pane">
        <span className="eyebrow" style={{ marginBottom: 14 }}>Live preview</span>
        <div className="ix-preview-frame"><PreviewStage {...previewProps} /></div>
      </aside>

      {mobilePreview && (
        <div className="ix-mobile-preview">
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '16px 20px' }}>
            <span className="eyebrow">Live preview</span>
            <button className="btn-ghost" style={{ padding: '8px 16px' }} onClick={() => setMobilePreview(false)}>Close</button>
          </div>
          <div style={{ flex: 1, margin: '0 16px 16px', borderRadius: 16, overflow: 'hidden', boxShadow: 'var(--shadow-lg)', background: '#fff', position: 'relative' }}>
            <PreviewStage {...previewProps} />
          </div>
        </div>
      )}
    </div>
  );
}

/* ---------- Preview box (fluid + responsive, lightly scaled) ---------- */
function PreviewStage(props: React.ComponentProps<typeof Preview>) {
  return (
    <div style={{ position: 'absolute', inset: 0, overflow: 'auto' }}>
      <div style={{ transform: 'scale(0.94)', transformOrigin: 'top center' }}>
        <Preview {...props} />
      </div>
    </div>
  );
}

/* ---------- Small building blocks ---------- */
function Step({ children }: { children: React.ReactNode }) {
  return <div style={{ display: 'grid', gap: 8, maxWidth: 560 }}>{children}</div>;
}

function StepHead({ title, sub, meta, aiBusy, runAssist }: {
  title: string; sub: string; meta: (typeof STEPS)[number]; aiBusy: string | null; runAssist: (k: string) => void;
}) {
  const busy = aiBusy === meta.ai;
  return (
    <div style={{ marginBottom: 8 }}>
      <div style={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', gap: 16 }}>
        <h1 className="display" style={{ fontSize: 'clamp(26px, 4vw, 34px)', margin: 0 }}>{title}</h1>
        {meta.ai && (
          <button className="ix-ai" onClick={() => runAssist(meta.ai!)} disabled={!!aiBusy} title={meta.aiDesc}>
            {busy ? <span className="ix-spin" /> : <span className="spark">✦</span>}
            {busy ? 'Thinking…' : meta.aiLabel}
          </button>
        )}
      </div>
      <p className="muted" style={{ fontSize: 15, margin: '8px 0 18px', lineHeight: 1.5 }}>
        {sub}{meta.ai && <span style={{ display: 'block', fontSize: 13, marginTop: 4, color: 'var(--faint)' }}>✦ {meta.aiDesc}</span>}
      </p>
    </div>
  );
}

function Small({ label, children }: { label: string; children: React.ReactNode }) {
  return <label style={{ display: 'grid', gap: 5, fontSize: 13, color: 'var(--muted)', fontWeight: 500 }}>{label}{children}</label>;
}

function ColorRow({ label, token, tokens, onChange }: { label: string; token: string; tokens: Record<string, string>; onChange: (hex: string) => void }) {
  const value = tokens[token] ?? '#000000';
  const [text, setText] = useState(value);
  useEffect(() => setText(value), [value]);
  return (
    <div className="ix-colorrow">
      <label>{label}</label>
      <input className="ix-hex" value={text} onChange={(e) => { setText(e.target.value); if (isHex(e.target.value)) onChange(e.target.value); }} />
      <input type="color" value={/^#([0-9a-f]{6})$/i.test(value) ? value : '#000000'} onChange={(e) => onChange(e.target.value.toUpperCase())} />
    </div>
  );
}

/* ---------- Logo uploader (dropzone + drag-drop + thumbnail) ---------- */
function LogoUpload({ logo, setLogo }: { logo: { dataUrl: string; name: string } | null; setLogo: (l: { dataUrl: string; name: string } | null) => void }) {
  const inputRef = useRef<HTMLInputElement>(null);
  const [err, setErr] = useState('');
  const [drag, setDrag] = useState(false);

  function handleFile(f?: File | null) {
    setErr('');
    if (!f) return;
    if (!/^image\/(png|jpe?g|svg\+xml|webp)$/.test(f.type)) { setErr('Use a PNG, JPG, SVG, or WebP image.'); return; }
    if (f.size > 3 * 1024 * 1024) { setErr('That file is over 3 MB — please pick a smaller one.'); return; }
    const reader = new FileReader();
    reader.onload = () => setLogo({ dataUrl: String(reader.result), name: f.name });
    reader.onerror = () => setErr('Could not read that file. Try another.');
    reader.readAsDataURL(f);
  }

  return (
    <div>
      <input ref={inputRef} type="file" accept="image/png,image/jpeg,image/svg+xml,image/webp" style={{ display: 'none' }} onChange={(e) => handleFile(e.target.files?.[0])} />
      {logo ? (
        <div style={{ display: 'flex', alignItems: 'center', gap: 14, padding: 14, border: '1px solid var(--line-strong)', borderRadius: 14, background: 'var(--card)' }}>
          <img src={logo.dataUrl} alt="logo preview" style={{ height: 46, width: 46, objectFit: 'contain', borderRadius: 9, background: 'var(--paper)', flex: '0 0 auto' }} />
          <div style={{ flex: 1, minWidth: 0 }}>
            <div style={{ fontSize: 14, fontWeight: 600, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{logo.name}</div>
            <button type="button" style={{ padding: 0, marginTop: 3, fontSize: 12, color: 'var(--muted)', background: 'none', border: 'none', cursor: 'pointer', textDecoration: 'underline' }} onClick={() => { setLogo(null); if (inputRef.current) inputRef.current.value = ''; }}>Remove</button>
          </div>
          <button type="button" className="btn-ghost" style={{ padding: '8px 16px' }} onClick={() => inputRef.current?.click()}>Replace</button>
        </div>
      ) : (
        <button type="button" onClick={() => inputRef.current?.click()}
          onDragOver={(e) => { e.preventDefault(); setDrag(true); }} onDragLeave={() => setDrag(false)}
          onDrop={(e) => { e.preventDefault(); setDrag(false); handleFile(e.dataTransfer.files?.[0]); }}
          style={{ width: '100%', padding: '30px', border: `1.5px dashed ${drag ? 'var(--ink)' : 'var(--line-strong)'}`, borderRadius: 14, background: drag ? 'rgba(23,21,15,.03)' : 'var(--card)', cursor: 'pointer', color: 'var(--muted)', fontSize: 14, textAlign: 'center', lineHeight: 1.6, transition: 'all .2s var(--ease)' }}>
          <b style={{ color: 'var(--ink)' }}>Click to upload</b> or drag an image here
          <br /><span style={{ fontSize: 12, color: 'var(--faint)' }}>PNG, JPG, SVG, or WebP · max 3 MB</span>
        </button>
      )}
      {err && <p style={{ color: '#9a3b2b', fontSize: 13, marginTop: 8 }}>{err}</p>}
    </div>
  );
}
