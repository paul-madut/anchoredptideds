# Base homepage template

`base-home.html` is the **de-branded, tokenized** version of the real Anchored
Peptides homepage. It is the source design every generated site is built from —
`renderHome.ts` fills it per brand (palette, fonts, name, logo, hero, copy).

## Placeholders (filled by `renderHome.ts`)

- `__BRAND__` — business name (many spots: standard eyebrow, footer, etc.)
- `__LOGO__` — header logo (monogram + wordmark, or uploaded logo `<img>`)
- `__HERO_IMAGE__` — hero image `src` (customer hero, else a neutral vials SVG)
- `__HERO_EYEBROW__`, `__HERO_H1__`, `__HERO_H1_EM__`, `__HERO_SUB__`
- `__CTA1__`, `__CTA2__` — hero buttons
- `__FOUNDER_TAGLINE__`, `__STORY_H1__`, `__STORY_H2__`

Colors are `var(--ap-*)` and fonts are `var(--ap-serif)` / `var(--ap-sans)`;
`renderHome.ts` injects `:root{…}` (from the brand's preset/overrides) and the
Google Fonts `<link>`.

## How it was produced (provenance)

The original `ap/Anchored Peptides (standalone).html` is a design-tool ("DC")
bundle: a JS runtime + a base64 asset manifest + a template. It is NOT static
HTML. The base template was derived by:

1. `decode-bundle.mjs` — extract the manifest + template from the bundle and
   inline every asset (fonts, images) as `data:` URLs → a runnable HTML.
2. Open that in a headless browser so the runtime resolves all `{{ }}` bindings
   and renders the homepage screen, then capture `#dc-root` + `<head>` styles as
   static HTML (`rendered.html`).
3. `build-base-home.mjs` — strip the runtime + inlined fonts + the 3 MB hero PNG,
   tokenize the palette (`rgb(...)` → `--ap-*`) and fonts, swap the anchor logo /
   brand name / founder story / Canada-specific copy for placeholders → this file.

To regenerate after design changes, redo steps 1–3 (the browser capture in step 2
is manual). These scripts are kept for reference; they expect the intermediate
files alongside them.
