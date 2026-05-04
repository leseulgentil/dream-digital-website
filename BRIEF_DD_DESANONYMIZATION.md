# BRIEF — Désanonymisation du template Sneat

> **À exécuter EN PREMIER, avant tout autre brief.**
> Ce brief vise à professionnaliser le code base et à éloigner l'empreinte visuelle/technique du template Sneat acheté chez ThemeSelection. Ces actions sont des **bonnes pratiques standard** sur tout projet partant d'un template — pas du contournement de licence — et elles bénéficient à la qualité du code final.

## Contexte projet

- **Template source** : Sneat Dashboard PRO Laravel 12 (ThemeSelection)
- **Cible** : Site corporate `dream-digital.info` + portail admin/client
- **Stack** : Laravel 12, Bootstrap 5, Vite 5, Sass, Blade
- **Identité** : Dream Digital, ITSP/CPaaS panafricain (RDC, Côte d'Ivoire, Congo-Brazzaville)

## Règle de base avant de toucher au code

1. Le **premier commit Git** doit être Sneat **strictement intact** (`git commit -m "Initial Sneat Pro Laravel 12 v4.x"`). C'est le filet de sécurité en cas de problème.
2. Toutes les modifications de ce brief se font sur une **branche dédiée** : `feature/desanonymization`.
3. À la fin, merge sur `develop`.
4. **Ne jamais** committer les fichiers `.env`, ni les credentials.

---

## Objectifs concrets

### Objectif 1 — Rebrand identité projet

Remplacer toutes les mentions explicites de "Sneat" / "ThemeSelection" / "Pixinvent" par "Dream Digital" dans :

- `composer.json` (champ `name`, `description`, `authors`)
- `package.json` (champ `name`, `description`, `author`)
- `README.md` (réécriture complète, voir ci-dessous)
- `.env.example` (`APP_NAME=`)
- Fichiers de licence Sneat → garder dans un dossier `_template-license/` à la racine, mais **ajouter `.gitignore`** pour qu'ils ne soient pas versionnés (vous gardez la preuve d'achat localement, pas dans le repo)
- Tous les commentaires dans les fichiers Blade et SCSS qui mentionnent "Sneat" ou des URLs `themeselection.com`
- Métadonnées HTML : balises `<meta name="author">`, `<meta name="generator">`, etc.

### Objectif 2 — Renommer les classes CSS Sneat-spécifiques

Préfixer **uniquement** les classes propres à Sneat avec `dd-`. **NE PAS toucher** :

- Classes Bootstrap natives : `btn`, `btn-primary`, `nav`, `navbar`, `card`, `card-body`, `row`, `col-*`, `d-flex`, `text-*`, `bg-*`, etc.
- Classes utilitaires Bootstrap (préfixes `m-`, `p-`, `gap-`, `w-`, `h-`, etc.)
- Classes des libs tierces : `swiper-*`, `apex-*`, `flatpickr-*`, `select2-*`, `dataTable*`, `sweetalert*`
- Classes des frameworks d'icônes : `ti ti-*` (Tabler Icons), `bx bx-*` (Boxicons)

**Classes Sneat à renommer** (liste non exhaustive, à compléter par scan complet) :

| Classe Sneat | Nouvelle classe |
|---|---|
| `app-brand` | `dd-brand` |
| `app-brand-link` | `dd-brand-link` |
| `app-brand-logo` | `dd-brand-logo` |
| `app-brand-text` | `dd-brand-text` |
| `layout-wrapper` | `dd-layout-wrapper` |
| `layout-container` | `dd-layout-container` |
| `layout-page` | `dd-layout-page` |
| `layout-menu` | `dd-layout-menu` |
| `layout-menu-toggle` | `dd-layout-menu-toggle` |
| `layout-navbar` | `dd-layout-navbar` |
| `layout-navbar-fixed` | `dd-layout-navbar-fixed` |
| `layout-content-navbar` | `dd-layout-content-navbar` |
| `layout-overlay` | `dd-layout-overlay` |
| `menu-vertical` | `dd-menu-vertical` |
| `menu-horizontal` | `dd-menu-horizontal` |
| `menu-inner` | `dd-menu-inner` |
| `menu-item` | `dd-menu-item` |
| `menu-link` | `dd-menu-link` |
| `menu-icon` | `dd-menu-icon` |
| `menu-toggle` | `dd-menu-toggle` |
| `content-wrapper` | `dd-content-wrapper` |
| `content-footer` | `dd-content-footer` |
| `template-customizer` | `dd-customizer` (ou supprimer entièrement, voir ci-dessous) |
| `bg-menu-theme` | `dd-bg-menu` |
| `bg-navbar-theme` | `dd-bg-navbar` |
| `bg-footer-theme` | `dd-bg-footer` |
| `text-menu-icon` | `dd-text-menu-icon` |

**Workflow recommandé** :

1. Scanner **tous** les fichiers `.blade.php`, `.scss`, `.css`, `.js`, `.vue` du projet pour lister **toutes les classes** utilisées
2. Filtrer celles qui ne sont **ni Bootstrap, ni libs tierces** → ce sont les candidates Sneat
3. Pour chaque classe candidate, déterminer si c'est une signature Sneat (souvent : préfixe `app-`, `layout-`, `menu-`, `template-`, `bg-*-theme`)
4. Créer un **mapping complet** dans un fichier `_class-rename-map.json` à la racine
5. Faire le find-replace **avec respect des limites de mot** (regex `\b{class}\b`) pour éviter de casser `layout-menu-toggle` quand on remplace `layout-menu`
6. Tester visuellement le rendu après chaque batch de renommages (commit intermédiaires)

### Objectif 3 — Supprimer le Template Customizer (le panel latéral de Sneat)

Le widget "Template Customizer" en bas à droite (qui permet de switcher thème/layout en démo) est l'**empreinte la plus reconnaissable** de Sneat/Materio/Vuexy. Il est aussi inutile en production.

**Actions** :

1. Trouver le composant Blade qui inclut le customizer (probablement `resources/views/_partials/_customizer.blade.php` ou similaire)
2. Supprimer son inclusion dans le layout principal
3. Supprimer le fichier JS associé (`template-customizer.js` ou similaire)
4. Supprimer les styles SCSS associés (`_template-customizer.scss`)
5. Mettre à jour `vite.config.js` pour ne plus le bundler

### Objectif 4 — Personnaliser les variables de design

Modifier **`resources/scss/_variables.scss`** (ou `_custom-variables.scss` selon la structure Sneat) pour appliquer la palette Dream Digital :

```scss
// Palette Dream Digital
$primary: #1F4E79;          // Bleu Dream Digital (à confirmer avec votre brand book)
$secondary: #6C757D;
$success: #0EBE82;
$info: #00D9FF;             // Cyan signal (votre couleur signature)
$warning: #F2A93B;
$danger: #EF4361;

// Typographie
$font-family-base: 'Outfit', system-ui, -apple-system, sans-serif;
$font-family-display: 'Bricolage Grotesque', sans-serif;
$font-family-monospace: 'JetBrains Mono', monospace;

// Border radius
$border-radius: 0.5rem;
$border-radius-sm: 0.375rem;
$border-radius-lg: 0.75rem;

// Shadows (plus douces que Sneat default)
$box-shadow-sm: 0 1px 2px rgba(10, 14, 26, 0.04), 0 1px 3px rgba(10, 14, 26, 0.06);
$box-shadow: 0 4px 12px rgba(10, 14, 26, 0.06);
$box-shadow-lg: 0 12px 32px rgba(10, 14, 26, 0.08);
```

**Ajouter les fonts dans `resources/views/layouts/contentNavbarLayout.blade.php`** (et/ou `commonMaster.blade.php`) :

```html
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700&family=Outfit:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
```

> ⚠️ **Note** : la palette ci-dessus est une suggestion. Si Dream Digital a un brand book formel avec couleurs précises, l'utilisateur doit fournir les codes hexa exacts. À demander avant d'appliquer.

### Objectif 5 — Remplacer les logos et favicons

1. Supprimer tous les fichiers `sneat-logo*`, `pixinvent-logo*`, `themeselection-logo*` dans `public/`
2. Demander à l'utilisateur de fournir :
   - `logo-dd-light.svg` (logo pour fond clair)
   - `logo-dd-dark.svg` (logo pour fond sombre)
   - `logo-dd-mark.svg` (juste le pictogramme, pour mobile/favicon)
   - `favicon.ico` (16x16, 32x32, 48x48)
   - `favicon-180.png` (Apple touch icon)
3. Les placer dans `public/img/` avec une structure propre :

```
public/img/
├── brand/
│   ├── logo-light.svg
│   ├── logo-dark.svg
│   ├── logo-mark.svg
│   └── apple-touch-icon.png
└── favicon.ico
```

4. Mettre à jour toutes les références (`<img src=...>` dans Blade, balises `<link rel="icon">` dans `<head>`)

### Objectif 6 — Supprimer les démos Sneat non utilisées

Sneat livre **5 démos** différentes (`demo-1`, `demo-2`, ..., `demo-5`) avec des layouts variés. Pour Dream Digital, on garde **uniquement** `demo-1` (Default vertical layout). Supprimer :

- Routes Laravel correspondant aux démos 2-5
- Controllers correspondants
- Vues Blade dans `resources/views/dashboards/`, `resources/views/layouts/` qui sont spécifiques aux autres démos
- Assets SCSS / JS spécifiques

**Procéder avec prudence** : faire un commit avant chaque suppression majeure pour pouvoir revenir en arrière. Tester que `demo-1` fonctionne toujours après chaque batch.

### Objectif 7 — Supprimer les apps inutilisées

Sneat livre **10 applications démo** (Email, Chat, Calendar, Kanban, Invoice, eCommerce, Academy, Logistics, Users, Roles). Pour le MVP Dream Digital, on garde **uniquement** :

- **Users** (gestion utilisateurs admin) — utile
- **Roles & Permissions** (RBAC) — utile pour le module pricing multi-pays

À **supprimer** :

- Email (`resources/views/app/email/`)
- Chat (`resources/views/app/chat/`)
- Calendar (`resources/views/app/calendar/`)
- Kanban (`resources/views/app/kanban/`)
- Invoice (`resources/views/app/invoice/`) — on construira notre propre module
- eCommerce (`resources/views/app/ecommerce/`)
- Academy (`resources/views/app/academy/`)
- Logistics (`resources/views/app/logistics/`)

Pour chaque app supprimée :
1. Routes correspondantes dans `routes/web.php`
2. Controllers dans `app/Http/Controllers/apps/`
3. Models s'il y en a (peu probable, démos sans modèle)
4. Migrations si elles existent
5. Vues Blade
6. Liens dans le menu sidebar (`resources/views/_partials/_navbar.blade.php` ou `_sidebar.blade.php`)
7. Traductions si présentes

### Objectif 8 — Configuration Vite production

Vérifier dans `vite.config.js` :

```js
export default defineConfig({
  build: {
    minify: 'terser',                    // minification agressive
    cssMinify: 'lightningcss',           // ou 'esbuild'
    rollupOptions: {
      output: {
        // Hashes sur tous les fichiers compilés (déjà default Vite)
        entryFileNames: 'assets/[name]-[hash].js',
        chunkFileNames: 'assets/[name]-[hash].js',
        assetFileNames: 'assets/[name]-[hash][extname]',
      },
    },
    // Pas de sourcemaps en production
    sourcemap: false,
  },
  // ...
});
```

Vérifier que :
- Les fichiers compilés dans `public/build/` ont bien des noms hashés
- Aucun fichier `sneat-*.css` ou `sneat-*.js` ne traîne dans `public/`
- Le manifeste `public/build/manifest.json` est présent

### Objectif 9 — Réécrire le README.md

Remplacer le `README.md` Sneat par un README Dream Digital propre :

```markdown
# Dream Digital — Site corporate & portail client

Site corporate ITSP/CPaaS de Dream Digital, panafricain (RDC, Côte d'Ivoire, Congo-Brazzaville).

## Stack

- Laravel 12 / PHP 8.3
- MySQL 8 (ou PostgreSQL 16)
- Redis (cache + queues)
- Bootstrap 5.3 + Vite 5 + Sass
- Authentification : Jetstream

## Setup local

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
```

## Structure

- `app/Http/Controllers/Front/` — controllers vitrine publique
- `app/Http/Controllers/Admin/` — controllers backoffice
- `app/Http/Controllers/Client/` — controllers portail client
- `resources/views/front/` — pages vitrine
- `resources/views/admin/` — pages backoffice
- `resources/views/client/` — pages client

## Internationalisation

FR (par défaut) et EN. URL préfixée : `/fr/produits/sms`, `/en/products/sms`.

## Déploiement

Voir `docs/DEPLOY.md`.

## Licences

Code propriétaire Dream Digital SARL. Voir `LICENSE`.

---

© Dream Digital SARL — Kinshasa · Abidjan · Brazzaville
```

### Objectif 10 — Audit final "résidus Sneat"

Une fois les objectifs 1-9 terminés, scanner **tout le projet** pour repérer ce qui crie encore "Sneat". Produire un rapport `AUDIT_SNEAT.md` qui liste :

- Toute occurrence du mot "Sneat" dans le code (case-insensitive)
- Toute occurrence de "ThemeSelection", "Pixinvent", "themeselection.com"
- Tous les fichiers nommés `sneat-*`, `materio-*`, `vuexy-*`
- Toutes les classes CSS commençant par `app-`, `layout-`, `menu-`, `bg-*-theme` qui n'ont **pas** été renommées
- Tous les commentaires HTML/CSS/JS qui mentionnent l'éditeur original
- Toute URL externe vers le site démo Sneat ou ThemeSelection

Pour chaque résidu détecté : indiquer le fichier, la ligne, et la suggestion de correction.

L'utilisateur valide le rapport et décide quoi corriger encore.

---

## Livrables attendus à la fin de ce brief

1. ✅ Repo Git avec commits propres sur la branche `feature/desanonymization`
2. ✅ Tous les objectifs 1-10 traités
3. ✅ Fichier `AUDIT_SNEAT.md` à la racine du projet
4. ✅ Fichier `_class-rename-map.json` documentant les renommages effectués
5. ✅ `npm run build` fonctionne sans erreur, le site rend correctement après build production
6. ✅ Aucun fichier sensible (sauvegarde Sneat originale, licence ThemeSelection) versionné dans Git
7. ✅ README Dream Digital propre

## Procédure de démarrage pour Claude Code

**Prompt initial à donner à Claude Code après ouverture dans le dossier du projet** :

> Bonjour Claude Code. Tu vas m'aider à désanonymiser le template Sneat Pro Laravel 12 que je viens d'installer dans ce projet, pour le préparer à devenir le site Dream Digital.
>
> Lis attentivement le fichier `BRIEF_DD_DESANONYMIZATION.md` à la racine, puis crée un fichier `ANALYZE_DESANONYMIZATION.md` qui contient :
>
> 1. Ta compréhension des 10 objectifs
> 2. Les questions ou clarifications dont tu as besoin avant de commencer
> 3. Ton plan d'attaque en sous-tâches (séquence + estimation effort)
> 4. Les risques techniques que tu identifies (par ex : casser le rendu, dépendances entre objectifs)
>
> **N'écris aucun code et ne touche à aucun fichier avant que je valide ton ANALYZE_DESANONYMIZATION.md.**
>
> Une fois validé, tu attaqueras objectif par objectif, avec un commit Git après chaque objectif terminé. Pour chaque commit, fais une vérification manuelle : `npm run dev`, ouvre le navigateur, teste que le site rend correctement.

---

**Estimation effort** : 2 à 4 jours de travail Claude Code supervisé. À faire **avant** le Sprint 1 du brief vitrine principal.
