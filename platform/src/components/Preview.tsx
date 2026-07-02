'use client';

import { siteInnerHtml, type SiteConfig } from '@/lib/renderSite';

export type PreviewProps = SiteConfig;

/**
 * Hybrid live preview. A thin client wrapper over the shared site renderer
 * (src/lib/renderSite.ts), so the intake/CRM preview is byte-identical to the
 * generated standalone HTML artifact and reads the same `--ap-*` tokens as the
 * deployed WordPress theme.
 */
export default function Preview(props: PreviewProps) {
  const styleVars: Record<string, string> = {};
  for (const [k, v] of Object.entries(props.tokens)) {
    if (/^--ap-[a-z0-9-]+$/.test(k)) styleVars[k] = v;
  }
  return (
    <div
      className="ap-root"
      style={styleVars as React.CSSProperties}
      dangerouslySetInnerHTML={{ __html: siteInnerHtml(props) }}
    />
  );
}
