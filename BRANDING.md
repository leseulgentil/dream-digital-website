# BRANDING — Dream Digital (developer cheatsheet)

> Quick reference for developers working on the Dream Digital codebase. The **canonical source of truth** is [BRAND_KIT_DREAM_DIGITAL.md](./BRAND_KIT_DREAM_DIGITAL.md) v1.2 (validated 2026-05-05). When in doubt, defer to that document.

---

## Identity in code

```
Brand name  : Dream Digital SARL
Tagline EN  : Voice. SMS. eSIM. And More.
Tagline FR  : Voix. SMS. eSIM. Et bien plus.
Domain      : https://dream-digital.info
Positioning : global CPaaS/ITSP operator (NEVER "panafricain", NEVER "Afrique" as a geographic limitation)
```

These values are wired into `config/variables.php` and consumed by Blade layouts (`commonMaster.blade.php`, `layoutFront.blade.php`).

## Design tokens — where they live

- **Brand Kit (specification)** : `BRAND_KIT_DREAM_DIGITAL.md` at the repository root
- **SCSS source (ready-to-use)** : `_dream-digital-tokens.scss` at the repository root
- **In-Sneat copy (integrated in S3)** : `resources/assets/vendor/scss/_custom-variables/_dream-digital.scss`, imported at the top of `_bootstrap-extended.scss`

All Sass variables are prefixed `$dd-*`.

## Color cheatsheet (4-color hierarchy)

| Token | Hex | Role | Surface ratio |
|---|---|---|---|
| `$dd-primary-500` | `#335F5F` | Branding, sidebars, brand titles, icons | ~30% |
| `$dd-secondary` (`$dd-action-black`) | `#0E121C` | Primary CTAs, strong text | ~15% |
| `$dd-tertiary-500` | `#14B8A6` | **SPOT only — max 5% surface, max 3-4 occurrences per page** | ≤5% |
| `$dd-surface` / `$dd-ink-*` | `#FFFFFF` + neutrals | Surfaces, body text, borders | ~50% |

**Cyan (`$dd-tertiary-500`) hard rules** :
- ✅ allowed : editorial text links, active menu underline, NEW badges, live indicators, code highlights, focus rings, wizard step-completed checks
- ❌ forbidden : primary CTAs, card backgrounds, headings, body text, container borders (except focus state)

**Status colors** (`$dd-success`, `$dd-warning`, `$dd-danger`, `$dd-info`) are reserved for **status indicators only** — never for branding or decoration.

## Typography cheatsheet

```scss
$dd-font-family-base    : 'Inter', system-ui, …       // body 400/500/600, headings 600/700/800
$dd-font-family-display : 'Inter', system-ui, …       // same family as body
$dd-font-family-mono    : 'JetBrains Mono', …         // 400/500/600
```

Google Fonts are loaded in `<head>` of `commonMaster.blade.php` and `layoutFront.blade.php` (integrated in S3) :

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
```

## Code conventions

- **CSS classes** : Sneat-specific classes are renamed with the `dd-` prefix in S7 (mapping in `_class-rename-map.json`). Bootstrap, libs (swiper, datatables, …) and icon classes (`ti ti-*`, `bx bx-*`) are **never** renamed.
- **Border radius** : signature is `$dd-radius-md` (8px). Stay between 4 and 12px ; reserve 16-24px for premium hero blocks only.
- **Shadows** : default is **no shadow + `border: 1px solid $dd-ink-100`**. Shadows only for hover states or floating elements (use `$dd-shadow-*` tokens).
- **Focus rings** : `box-shadow: $dd-shadow-focus;` (cyan focus ring at 25% opacity).
- **Transitions** : prefer `$dd-ease-out` (90% of cases) with `$dd-duration-fast` or `$dd-duration-normal`.

## Logos

The four official PNG logos live in `public/img/brand/` (integrated in S5) :

| File | Use |
|---|---|
| `logo-dd-horizontal.png` | Default navbar logo (PRINCIPAL) |
| `logo-dd-vertical.png` | Vertical layouts |
| `logo-dd-icon.png` | Favicon, sidebar collapsed, very small contexts |
| `logo-dd-wordmark.png` | Wordmark only (no symbol) |

Roadmap : PNG → SVG conversion in 3-6 months ; pro logo redesign in 6-12 months.

## Tone of voice (quick rules)

- **Direct and precise** — no marketing fluff, no "solution"
- **Carrier-grade confidence** — telecom operator vocabulary
- **Action verbs** — "Connect", "Program", "Activate" / "Connectez", "Programmez", "Activez"
- **Bilingual native** — every customer-facing message exists in both FR and EN
- **No emojis** in corporate copy
- **Concrete numbers** over abstractions (latency, volumes, pricing)

## Assets pending

- **Pango the pangolin** — secondary mascot (provisional SVG, used for 404, empty states, docs, social — never on hero or B2B product pages)
- Open Graph image for `config('variables.ogImage')` — to be produced
- Social media official URLs to fill in `config/variables.php`

## Brand questions / arbitration

Lukoo Mapendo Gentil — Founder & CEO Dream Digital — `gentil@dream-digital.info`
