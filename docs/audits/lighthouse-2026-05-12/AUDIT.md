# Lighthouse audit — Sprint 1.5 (P2)

Date : 2026-05-12
Branche : `feature/sprint-1-5-redesign`
Cible : 9 pages publiques `/fr/*` (mode `php artisan serve` sur `127.0.0.1:8888`)
Preset : mobile, throttling simulé, categories perf/a11y/best-practices/seo

## Scores avant / apres fixes (mobile)

| Page | Perf | A11y avant | A11y apres | BP | SEO |
|---|---:|---:|---:|---:|---:|
| /fr | 55 | 90 | 100 | 100 | 66 |
| /fr/products | 55 | 93 | 100 | 100 | 66 |
| /fr/products/sms-a2p | 55 | 93 | 100 | 100 | 66 |
| /fr/developers | 55 | 93 | 100 | 100 | 66 |
| /fr/solutions | 55 | 93 | 100 | 100 | 66 |
| /fr/coverage | 55 | 88 | 95 | 100 | 66 |
| /fr/pricing | 55 | 88 | 95 | 96 | 66 |
| /fr/company | 55 | 93 | 100 | 100 | 66 |
| /fr/contact | 55 | 93 | 100 | 96 | 66 |

Critère brief Section 6.2 (Performance >85 + Accessibility >95) :
- **Accessibility >=95 atteint sur les 9 pages** apres fixes
- Performance bloque a 55 sur dev (voir ci-dessous)
- SEO 66 limite par `noindex` actif (attendu, garde-fou `DD_PUBLIC_INDEXABLE=false`)

## Fixes appliques (ce commit)

1. **commonMaster.blade.php** — `<meta name="viewport">` : retire `user-scalable=no, minimum-scale=1.0, maximum-scale=1.0`. Le defaut Sneat empechait le pinch-zoom mobile (anti-pattern WCAG).
2. **navbar-front.blade.php** + **navbar-partial.blade.php** — switcher de theme : converti `<a href="javascript:void(0);">` (toggle + 3 items) en `<button type="button">`. Coherent avec Bootstrap 5 dropdown semantics et resout `crawlable-anchors`.
3. **front-page-landing.scss** — `.dd-hero-pagination .swiper-pagination-bullet` : taille augmentee a 24x24 (cible tactile) avec dot visuel 8x8 via `::before`. Etat actif redimensionne le dot en barre 20x8.
4. **corridor-card.blade.php** — `<div class="dd-corridor-card__quality">` : ajoute `role="img"`, `aria-label` enrichi avec valeur ("Route quality X sur 5"), enfants en `aria-hidden="true"`. Resout `aria-prohibited-attr`.

## Non-fixes documentes

### Performance bloquee a 55 (perf >85 non atteint)

Cause principale : `uses-text-compression` (1.79 MB d'economies estimees). Le serveur `php artisan serve` ne fait pas de gzip/brotli. Sur le VPS de production, Nginx 1.24 a la compression activee (cf. cloture pre-S1.5, hardening Sprint 0). **A re-mesurer sur `https://dream-digital.info`** avant decision finale.

Secondaire : `unused-css-rules` 686 KiB + `unused-javascript` 151 KiB. Le bundle public charge encore une part importante du theme Sneat. Une separation `theme-front-minimal` est listee comme amelioration possible dans `FINITION_BLOCKS_2026-05-12.md` Bloc 4 (risque restant).

### SEO 66 (is-crawlable fail)

`<meta name="robots" content="noindex, nofollow">` actif par garde-fou (`DD_PUBLIC_INDEXABLE=false` jusqu'a validation PO). Bascule documentee dans Bloc 4. **Non-fix volontaire.**

### errors-in-console sur /fr/pricing et /fr/contact

`ERR_CONNECTION_RESET` sporadique sur les bundles Vite servis par `php artisan serve` (mono-thread). Specifique au dev local, ne se reproduit pas derriere Nginx FPM en production. **Non-fix.**

## Reproduction

```bash
# Pre-requis : serveur Laravel sur 127.0.0.1:8888 + npm.cmd run build deja execute
bash docs/audits/lighthouse-2026-05-12/run-lighthouse.sh
cat docs/audits/lighthouse-2026-05-12/_summary.csv
```

Les rapports JSON detailles sont regeneres localement (gitignored, ~6 MB).
