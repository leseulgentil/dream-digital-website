# SPRINT_STATE — Sprint 0 (Désanonymisation Sneat → Dream Digital)

> **🏁 SPRINT 0 OFFICIELLEMENT CLOS** — 2026-05-06
> Branche active : `feature/desanonymization` — prête à merger vers `master`
> 36 commits · 127 fichiers modifiés · +30249 / −5144 lignes · 8 jurisprudences (Q14-Q21)
> Identité Dream Digital complète : palette teal Petrol + typographie Inter + logos officiels + dark mode

---

## 1. Progression globale (S0 → S10)

| ID | Statut | Description courte | Commit principal |
|---|---|---|---|
| **Pré-S0** | ✅ Done | Squash + purge `.env` & `dream_digital` de l'historique via `git filter-repo`, rotation `APP_KEY`, création `SECURITY.md` | `c2e7884` |
| **S0** | ✅ Done | Scaffold `_template-license/` + README + `.gitignore` interne | `d00831c` |
| **S1** | ✅ Done | Rebrand identité technique (composer, package, .env.example, variables.php, README, BRANDING) | `96e60e9` |
| **S2** | ✅ Done | Désactivation Customizer via flags `hasCustomizer=false`, `displayCustomizer=false` dans `config/custom.php` | `32ddf6c` |
| **S6** | ✅ Done | Suppression physique complète du Customizer (54 fichiers : 4 paths supprimés + 50 fichiers édités) | `4480851` |
| **S7** | ✅ **Done** | Renommage classes Sneat génériques (`app-*`, `layout-*`, `menu-*`, `bg-*-theme`) avec préfixe `dd-` — **1068 occurrences sur 62 fichiers, 66 classes uniques, 6 lots atomiques** | L1=`a5fbdd0` L2=`021f8cf` L3=`277dcff` L4=`591e02b` L5=`452bf8e` L6=`b7bd972` |
| **S8** | ✅ **Done** | Vite production config : `target: 'es2020'`, `minify: 'esbuild'`, `sourcemap: false`, `chunkSizeWarningLimit: 1500`, `rollupOptions.output` hashes explicites. Bonus : `npm run preview` script + iconify gitignore. Build verified : 872 assets, 17.1 MB total, 873 manifest entries, 0 sourcemap. | `ecc1482` |
| **S9** | ✅ **Done** | Cleanup résidus textuels Sneat/ThemeSelection + suppression dead code `templateCustomizer` (38 refs / 4 JS) + réinjection dark mode switcher Q13 (Light/Dark/System, localStorage `dd-theme`, anti-FOUC inline, purge legacy keys) + fix wiring `@vite()` post-validation PO + Q20 documenté | C1=`ee173a3` C2=`9c67dd0` C3=`e26ddbb` fix=`921e249` docs=`05f0138` |
| **S3** | ✅ **Done** | Palette teal Petrol `#335F5F` (override Bootstrap via point d'injection Sneat-natif `_custom-variables/_bootstrap-extended.scss`) + Inter (300-800) + JetBrains Mono (400-600) Google Fonts dans `commonMaster` `<head>` + Q21 SCSS override architecture documenté. Preuve runtime : `--bs-primary: #335F5F` confirmé via DevTools Computed Styles. | fix=`6813439` docs=`d419f33` |
| **S5** | ✅ **Done** | Logos officiels Dream Digital intégrés (Phase 1 SVG inline, Pattern A.1) : SVG icon `logo-dd-icon.svg` (1.4 KB, 5 paths vectoriels purs, viewBox 1247×1370, couleurs teal `#336666` + blancs `#FEFEFE` calibrées sur Brand Kit) inliné dans `_partials/macros.blade.php` → 22 emplacements impactés automatiquement. Favicon SVG primary + PNG fallback. 6 logos travail dans `public/img/brand/` (icon, full, horizontal, vertical, wordmark — SVG + PNG). 18 originaux préservés en local dans `public/img/brand/originals/` (gitignored). Brand-text span Inter conservé (cohérence S3 typo). | `f818f03` |
| **S10** | ✅ **Done** | Validation finale : audit complet sprint (36 commits, working tree clean), 0 résidu textuel Sneat dans le code production, classes Sneat 100% préfixées `dd-` dans le scope production (sidebar admin, navbar, footer, auth, invoice), `npm run build` succeed (874 entries manifest, 19 MB assets, 873 fichiers compilés), validation visuelle PO confirmée S3+S5+S9. Reste hors scope production (à traiter Sprint 1.5) : pages `/layouts-example/*` + `/front-pages/landing` contiennent du contenu marketing Sneat et un script `ui-app-brand.js` ciblant des `#layout-menu1/2/3/4` de démonstration — pas critique car non utilisé en production. | `8e7c148` |

### S7 — Synthèse (close 2026-05-05)

| Lot | Description | Occ. | Files | Commit |
|---|---|---:|---:|---|
| L1 | Classes CSS dans SCSS sources | 633 | 13 | `a5fbdd0` |
| L2 | Classes & IDs dans Blade templates | 160 | 37 | `021f8cf` |
| L3 | String literals & classList ops dans JS | 185 | 9 | `277dcff` |
| L4 | Classes générées dans `Helpers.php` | 6 | 1 | `591e02b` |
| L5 | Classes dans config menu JSON | 84 | 2 | `452bf8e` |
| L6 | `_class-rename-map.json` documentation | — | 1 (new) | `b7bd972` |
| **Total** | | **1068** | **62** | **66 classes** |

**Commits documentation associés** : Q14 `f729c85` · Q15+Q16 `1e0b3fb` · Q17+Q18 `8914832` · Q19 `f25f927`

Détail exhaustif par classe : voir `_class-rename-map.json` à la racine.

**Note** : objectif 6 du brief original (suppression demos demo-2 à demo-5) est **N/A** — la version Laravel n'a qu'une seule démo, pas de S4 dans le plan v1.2.

### 🏁 Synthèse de clôture Sprint 0 (2026-05-06)

**Sous-tâches accomplies** (10/10 dont 1 N/A) :
1. ✅ **Pré-S0** — Sécurité : purge `.env` historique + rotation `APP_KEY` + exclusion SQLite + SECURITY.md
2. ✅ **S0** — Scaffold `_template-license/` pour préservation licence vendor Sneat
3. ✅ **S1** — Rebrand identité technique (composer, package, .env.example, variables.php, README, BRANDING.md)
4. ✅ **S2** — Template Customizer désactivé via flags config
5. ⊘ **S4** — N/A (single demo Laravel build, voir Q8)
6. ✅ **S6** — Template Customizer supprimé physiquement (54 fichiers)
7. ✅ **S7** — Renommage Sneat → `dd-*` (1068 occurrences sur 62 fichiers, 66 classes uniques, 6 lots atomiques)
8. ✅ **S8** — Vite configuration production (target es2020, minify esbuild, hashes explicites)
9. ✅ **S9** — Cleanup résidus textuels + dead code `templateCustomizer` + dark mode switcher Q13 (Light/Dark/System) + fix wiring `@vite()` + Q20 méthodologie
10. ✅ **S3** — Design tokens Dream Digital (palette teal Petrol + Inter/JetBrains Mono) + Q21 SCSS override architecture
11. ✅ **S5** — Logos officiels Dream Digital (SVG inline Pattern A.1, favicon SVG+PNG)
12. ✅ **S10** — Validation finale + clôture sprint

**Jurisprudences méthodologiques (Q14-Q21)** :

| ID | Sujet | Pattern d'erreur protégé |
|---|---|---|
| Q14 | Convention rename `dd-<classe>` (Option A prepend) | Drift inconsistant entre fichiers |
| Q15 | IDs partagés HTML/JS/CSS synchronisés en S7 | Casse silencieuse JS sélecteurs |
| Q16 | 2 classes brief absentes du Sneat installé | Scope inflation sans valeur |
| Q17 | Renommage commentaires JS contenant références techniques | Drift code/commentaire |
| Q18 | Règle générale commentaires (HTML rename, Blade non, JS technique oui) | Décisions ad-hoc commentaires |
| Q19 | Audit étendu sources interpolées (JSON config, PHP fragments HTML, JS templating) | **Classes orphelines** (CSS sans HTML rendant la classe) |
| Q20 | Validation runtime navigateur des modules JS (Network 200 + Storage + Console + Comportement) | **Modules orphelins** (JS compilé sans HTML chargeant le module) |
| Q21 | Architecture overrides SCSS Sneat (3 fichiers homonymes, point d'injection canonical) | **Variables orphelines** (override SCSS sans effet sur Bootstrap) |

**Documentation produite** :

| Fichier | Rôle | Lignes |
|---|---|---|
| `BRAND_KIT_DREAM_DIGITAL.md` v1.2 | Source de vérité visuelle (15 sections) | ~1500 |
| `ANALYZE_DESANONYMIZATION.md` | Jurisprudence Q1-Q21 | ~720 |
| `_class-rename-map.json` | 1068 renames S7 documentés | — |
| `_dream-digital-tokens.scss` (racine + copie `_custom-variables/`) | Design tokens SCSS | 282 |
| `BRANDING.md` | Cheatsheet dev pointant vers BRAND_KIT | court |
| `SECURITY.md` | Procédure secrets + incident | court |
| `README.md` | Présentation projet, stack, setup | court |

**Statistiques finales** :
- Commits : **36** (initial Sneat exclus)
- Fichiers modifiés/créés : **127**
- Insertions : **+30 249** lignes
- Suppressions : **−5 144** lignes
- Build production : **874 entrées manifest**, **19 MB d'assets**, **873 fichiers compilés**

**Limites connues — scope Sprint 1.5** :
- Pages `/layouts-example/*` (8 vues de démonstration des layouts Sneat) contiennent encore des images PNG `layouts/layout-*.png` et des classes non préfixées dans le script `ui-app-brand.js` ciblant `#layout-menu1/2/3/4` de démo
- Page `/front-pages/landing` contient du contenu marketing Sneat (gradient violet hero, mockup PNG dashboard Sneat, copy "Production-ready Admin Template")
- Page `/apps/ecommerce-referrals` contient un placeholder `pixinvent.com/?ref=6479`
- Démo `extended-ui-sweetalert2.js` contient un lien `pixinvent.com`
- → Tous ces résidus sont du **contenu marketing/démo**, pas des classes/variables de production. Refactor planifié en Sprint 1.5 (redesign vitrine ITSP avec contenu Dream Digital authentique).

**État de la branche** : `feature/desanonymization` prête à merger vers `master`. Aucun blocker fonctionnel détecté. Validation visuelle PO confirmée sur dashboard back-office (Email app), sidebar, navbar, login, mode dark.

**Prochaine étape** : **Sprint 1.5** — redesign vitrine ITSP avec contenu Dream Digital authentique (landing page, 6 product pages Voice/SMS/eSIM/+, about, contact). Décision stratégique de mise en ligne en cours côté PO (Scenario B planifié : landing temporaire → site complet).

---

## 2. Décisions arbitrées (historique Q&A)

| ID | Date | Sujet | Décision |
|---|---|---|---|
| **Q1** | 2026-05-04 | Palette initiale | ⚠️ **Supersedée par Q11** (Brand Kit v1.2) |
| **Q2** | 2026-05-04 | Typographie initiale | ⚠️ **Supersedée par Q11** (Brand Kit v1.2) |
| Q3 | 2026-05-04 | Customizer | Désactiver d'abord (S2) puis supprimer (S6) — **fait** |
| Q4 | 2026-05-04 | Branche d'intégration | Garder `master` (pas `main`, pas `develop`) |
| Q5 | 2026-05-04 | Logos provisoires | Skip — vrais PNG officiels disponibles, voir S5 |
| Q6 | 2026-05-04 | composer.json à rebrander | Oui : `dream-digital/website` — **fait** |
| Q7 | 2026-05-04 | Licence Sneat | Dossier `_template-license/` créé avec README + `.gitignore` interne — **fait** |
| Q8 | 2026-05-04 | Objectif 6 N/A | Confirmé : version Laravel = 1 seule démo |
| Q9 | 2026-05-04 | Squash 3 commits doublons | Validé option **B** (filter-repo) — **fait** |
| **Q10** | 2026-05-05 | `.env` dans historique Git | Option **B** : purge filter-repo + rotation `APP_KEY` + ignore SQLite db — **fait** |
| **Q11** | 2026-05-05 | Brand Kit v1.2 reçu | Palette 4 couleurs + Inter/JetBrains Mono + nouvelle séquence S0→S1→S2→S6→S7→S8→S9→S3→S5→S10 |
| **Q12** | 2026-05-05 | Scope S7 | Option **B** : préfixer classes génériques (`app-*`, `layout-*`, `menu-*`, `bg-menu-theme`, `bg-navbar-theme`) avec `dd-`. **`data-*` HORS scope.** 5 résidus textuels "sneat" → S9. ⚠️ Table de mappings **superseded by Q14**. |
| **Q13** | 2026-05-05 | Dark mode switcher | À **réinjecter en S9** (pas maintenant). Specs : 6 points listés en section 5 ci-dessous. |
| **Q14** | 2026-05-05 | Convention renommage S7 | Option **A** retenue : prepend simple `dd-<classe>` pour TOUTES les classes (uniforme, traçable, réversible). Supersedes table Q12. Scope L1 figé : **633 occurrences / 13 fichiers SCSS / 58 classes**, +24 découvertes incluses, −5 anomalies à vérifier en L2/L3. ⚠️ 3 classes JS-dynamiques (`layout-transitioning`, `layout-no-transition`, `menu-no-animation`) → sync L1↔L3 strict. |
| **Q15** | 2026-05-05 | IDs partagés en L2 | Option **A** retenue : renommer aussi `id="layout-menu"` et `id="layout-navbar"` (4 occurrences) pour cohérence DOM totale. ⚠️ Contrainte L3 : tous `querySelector('#layout-menu')`, `getElementById('layout-navbar')`, etc. doivent être renommés en sync sinon menu casse silencieusement. |
| **Q16** | 2026-05-05 | Classes brief absentes du codebase | `menu-content` et `text-menu-icon` retirées définitivement du scope total S7 (0 occurrence SCSS + 0 occurrence Blade après scan exhaustif L1+L2). Mentionnées dans BRIEF/Q12 mais absentes du Sneat Pro Laravel 12 v4.0.0 installé. Scope final S7 = **66 classes** (58 L1 + 3 anomalies HTML confirmées + 5 discoveries L2). |
| **Q17** | 2026-05-05 | Commentaire JS main.js:112 | Renommé pour cohérence documentaire (drift code/comment inacceptable si code utilise `dd-*` mais commentaire référence l'ancien nom). Précédent L2 (commentaire HTML) fait jurisprudence. |
| **Q18** | 2026-05-05 | Règle commentaires clarifiée (S7+) | Blade `{{-- --}}` ❌ pas touché. HTML `<!-- -->` ✅ rename. JS `//` ou `/* */` **avec référence technique** ✅ rename. JS contextuel sans référence technique ❌ pas touché. Couvre rétroactivement L2 (1 commentaire HTML) + L3 (1 commentaire JS). S'applique à L4/L5/L6 et tout sprint futur. |
| **Q19** | 2026-05-05 | Règle audit étendue (S7+) | Tout audit de rename HTML doit étendre son scope au-delà des `.blade.php`/`.html` pour inclure : (a) **JSON config** sous `resources/menu/` et `resources/config/`, (b) **PHP** retournant des fragments HTML, (c) **JS** injectant du HTML via templating. Lesson learned du bug visuel L4/L5 : `menu-icon` était émis par `verticalMenu.json` interpolé dans Blade, classe invisible au scan L2 limité aux `.blade.php`. Pas de correction rétroactive (L5 fixe), documentation pour futurs sprints (1.5 redesign, S1 i18n, S2). |

Détail complet de chaque arbitrage dans `ANALYZE_DESANONYMIZATION.md`.

---

## 3. État Git

- **Branche active** : `feature/desanonymization`
- **Branche filet de sécurité** : `master` à `d604ee8` (Sneat 100% intact, **JAMAIS modifier**)
- **Aucun remote** configuré
- **Backup pre-filterrepo** : `../dream-digital-website-backup-pre-filterrepo/` (clone safe pré-réécriture historique, à garder jusqu'à validation finale du sprint)
- **Backup binaire SQLite** : `../dream-digital-website-backup-pre-filterrepo-dreamdb` (le fichier `dream_digital` original)

### Historique attendu (après création de SPRINT_STATE.md)

```
[NEW] docs: SPRINT_STATE checkpoint before PO machine reboot ...     ← checkpoint
e0409dc docs: add ANALYZE Q12 (S7 scope divergence) and Q13 ...
4480851 refactor(ui): remove Template Customizer files, partials ... ← S6
32ddf6c feat(ui): deactivate Template Customizer panel via config flags ← S2
88e0528 chore: temporarily ignore LOGO DREAM DIGITAL OR ...
da8d4fb docs: add Dream Digital Brand Kit v1.2 reference materials ...
96e60e9 feat: rebrand technical identity to Dream Digital ...        ← S1
0d02746 docs: mark Q10 resolved + add Q11 Brand Kit v1.2 ...
d00831c chore: scaffold _template-license/ ...                       ← S0
c2e7884 chore(security): remove .env, rotate APP_KEY, exclude SQLite ← Pré-S0
ab9ef22 docs: add ANALYZE with Q10 ...
f6219ec docs: add project briefs ...
551e7e3 feat: Sneat Pro Laravel 12 v4.0.0 installed ...
d604ee8 Initial Sneat Pro Laravel 12 v4.0.0 - vendor template intact ← master
```

---

## 4. Points d'attention CRITIQUES pour S7

### 4.1 Scope confirmé (Q12 option B)

**À renommer (préfixe `dd-` ajouté)** :
- `app-brand`, `app-brand-link`, `app-brand-logo`, `app-brand-text`
- `layout-wrapper`, `layout-container`, `layout-page`, `layout-menu`, `layout-menu-toggle`, `layout-menu-fixed`, `layout-menu-collapsed`, `layout-navbar`, `layout-navbar-fixed`, `layout-navbar-hidden`, `layout-content-navbar`, `layout-overlay`, `layout-without-menu`, `layout-footer-fixed`, `layout-compact`, `layout-wide`, `layout-horizontal`
- `menu-vertical`, `menu-horizontal`, `menu-inner`, `menu-item`, `menu-link`, `menu-icon`, `menu-toggle`, `menu-content`, `menu-sub`, etc. (à scanner exhaustivement)
- `content-wrapper`, `content-footer`, `content-backdrop`
- `bg-menu-theme`, `bg-navbar-theme`, `bg-footer-theme`
- `text-menu-icon` (et autres `text-*-theme` si trouvés)
- `template-customizer-*` résiduels dans helpers.js / main.js (dead code à supprimer en même temps)

**À NE PAS toucher** :
- Toutes classes Bootstrap (`btn`, `card`, `nav`, `navbar`, `dropdown`, `row`, `col-*`, `d-flex`, `m-*`, `p-*`, `gap-*`, `w-*`, `h-*`, `text-*`, `bg-*` utilitaires, etc.)
- Libs tierces (`swiper-*`, `apex-*`, `flatpickr-*`, `select2-*`, `dataTable*`, `sweetalert*`, `pickr-*`, `notyf-*`, `tippy-*`, etc.)
- Icons (`ti ti-*` Tabler, `bx bx-*` Boxicons, `fa fa-*` FontAwesome)
- **Attributs `data-*`** (ex `data-template`, `data-bs-theme`, `data-skin`) — conventions JS internes, hors scope
- Fichiers vendor/, libs/, node_modules/

### 4.2 Risques techniques (rappel section E de ANALYZE)

1. **`Helpers.php` génère classes dynamiquement** : `'layout-menu-fixed'`, `'layout-navbar-fixed'`, `'layout-footer-fixed'`, `'layout-menu-collapsed'`, `'layout-navbar-hidden'`. → Doivent être renommés DANS LE MÊME COMMIT que les classes CSS associées, sinon le layout casse à chaque rechargement de page.
2. **`menu.js` (~1000 lignes)** : sélecteurs en dur `document.querySelector('#layout-menu')`, `.layout-menu-toggle`, `.menu-inner`, etc. → renommer en sync avec HTML/SCSS.
3. **`main.js` / `front-main.js` / `helpers.js`** : idem sélecteurs en dur.
4. **Word-boundary strict OBLIGATOIRE** : remplacer naïvement `layout-menu` casserait `layout-menu-toggle`, `layout-menu-fixed`, `layout-menu-collapsed`. → Toujours utiliser `\b{class}\b` regex + ordre par longueur **décroissante** (renommer d'abord les classes les plus longues).
5. **Cookies `LayoutCollapsed`, `customize_skin`, `customize_semi_dark`, `admin-mode`, `direction`** : continuent d'exister (gérés par Helpers.php). À nettoyer du navigateur entre tests sinon résultats incohérents.
6. **RTL et thème dark** : à tester après chaque lot.
7. **Vite cache + assets compilés** : si `npm run build` est lancé pendant la transition, vider `public/build/` avant le build final.

### 4.3 Procédure 6-lots (Q12 option B)

| Lot | Périmètre | Commit suggéré |
|---|---|---|
| **L1** | Classes CSS dans les SCSS (resources/assets/vendor/scss/) | `refactor(naming): rename Sneat-prefix CSS classes to dd-* in SCSS` |
| **L2** | Classes CSS dans les Blade (resources/views/) | `refactor(naming): rename Sneat-prefix CSS classes to dd-* in Blade templates` |
| **L3** | Sélecteurs CSS dans les JS (resources/assets/js/, vendor/js/) | `refactor(naming): rename Sneat-prefix selectors to dd-* in JS` |
| **L4** | Classes générées dans `Helpers.php` + cohérence avec L1-L3 | `refactor(naming): rename Sneat-prefix classes generated by Helpers.php` |
| **L5** | Fichiers config/menu JSON (`resources/menu/*.json`) si présents | `refactor(naming): rename Sneat-prefix references in config/menu JSON` |
| **L6** | Création de `_class-rename-map.json` à la racine documentant tous les renommages | `docs: add _class-rename-map.json documenting S7 renames` |

**Validation visuelle** + `npm run dev` sans erreur Sass + console F12 propre **après chaque lot**.

---

## 5. Spécifications S9 — Restauration dark mode switcher (Q13)

À implémenter **dans le scope de S9** (audit final), comme contrôle UX standalone sans dépendance au customizer disparu.

1. **Lieu de réinjection** :
   - `resources/views/layouts/sections/navbar/navbar.blade.php` (ou `navbar-partial.blade.php` selon la structure définitive après S7)
   - `resources/views/layouts/sections/navbar/navbar-front.blade.php`
2. **Stockage `localStorage`** :
   - Clé : `dd-theme` (indépendante du customizer)
   - Valeurs : `light` / `dark` / `system`
   - Défaut : `system`
3. **Détection initiale** : `window.matchMedia('(prefers-color-scheme: dark)')` quand `system` est sélectionné
4. **Application** : attribut `data-bs-theme` sur `<html>` (Bootstrap 5.3+ supporte nativement `data-bs-theme="dark"`)
5. **Pas de référence** à `window.templateCustomizer` (qui aura été retiré des 4 fichiers JS dead-safe en S9)
6. **Icônes** : Tabler Icons `ti-sun`, `ti-moon`, `ti-device-desktop`

---

## 6. Séquence d'exécution restante

```
S7 (renommage classes) → S8 (Vite prod) → S9 (audit + dark mode + cleanup JS)
                       ↓
              S3 (design tokens + Inter)
                       ↓
              S5 (logos PNG officiels)
                       ↓
              S10 (validation finale)
```

**Stops obligatoires avec validation visuelle PO** : après S7, après S3, après S5.

---

## 7. Environnement local (à relancer après reboot)

| Composant | Commande / valeur |
|---|---|
| Working dir | `c:\wamp64_new\www\dream-digital-website` |
| Branche | `feature/desanonymization` |
| PHP | `C:\wamp64_new\bin\php\php8.4.0\php.exe` (alias `function php` en PowerShell global) |
| Composer | `C:\composer\composer.phar` ou alias |
| Node / npm | 22.15.0 / 10.9.2 |
| Laravel dev server | `php artisan serve --port=8888` → http://127.0.0.1:8888 |
| Vite dev server | `npm run dev` → http://127.0.0.1:5174 (5173 occupé) |
| DB dev | SQLite — fichier `dream_digital` à la racine (gitignored) |

---

## 8. Documents de référence à lire à chaque session

| Fichier | Rôle |
|---|---|
| `BRAND_KIT_DREAM_DIGITAL.md` | **Source de vérité visuelle officielle** (v1.2, 2026-05-05). 15 sections : palette 4 couleurs, typo Inter, iconography, espacements, radius, shadows, tone of voice, mascotte Pango, anti-patterns. |
| `_dream-digital-tokens.scss` | Tokens SCSS prêts à l'emploi (sera copié vers `resources/assets/vendor/scss/_custom-variables/_dream-digital.scss` en S3). |
| `BRANDING.md` | Cheatsheet dev (court, pointe vers BRAND_KIT). |
| `BRIEF_DD_DESANONYMIZATION.md` | Brief original sprint 0 (10 objectifs). |
| `BRIEF_SPRINT_1_5_REDESIGN.md` | Brief sprint 1.5 (à venir, post-désanonymisation). |
| `BRIEF_SPRINT_1.md` | Brief sprint 1 fondations multi-pays + i18n FR/EN. |
| `ANALYZE_DESANONYMIZATION.md` | Analyse complète du sprint 0 + Q&A (Q1 → Q13). |
| `SECURITY.md` | Gestion des secrets + procédure incident. |
| `README.md` | Présentation projet, stack, setup PowerShell. |
| `LOGO DREAM DIGITAL OR/` | Logos PNG officiels (gitignored — sera réorganisé en S5 vers `public/img/brand/originals/`). |

### Mémoire Claude Code (si Claude Code détecte la mémoire projet)

Localisation : `C:\Users\genti\.claude\projects\c--wamp64-new-www-dream-digital-website\memory\`
- `MEMORY.md` (index)
- `user_profile.md`
- `feedback_workflow.md`
- `project_dream_digital.md` (positionnement, palette, typo, branches)
- `reference_briefs.md`

---

## 9. REPRISE POST-REBOOT — INSTRUCTIONS POUR LA NOUVELLE SESSION CLAUDE CODE

> Quand le PO ouvrira une nouvelle session Claude Code après son reboot, voici comment reprendre :

1. **Lire ce fichier `SPRINT_STATE.md` en intégralité** — il est la source de vérité pour l'état du sprint
2. **Lire `ANALYZE_DESANONYMIZATION.md`** — Q1 à Q13 et tout l'historique des décisions
3. **Lire `BRAND_KIT_DREAM_DIGITAL.md`** — référence visuelle (toutes les questions de couleur/typo/styles s'y résolvent)
4. **Vérifier l'état du repo** :
   ```powershell
   git log --oneline -15
   git status
   git branch --show-current   # doit afficher feature/desanonymization
   ```
5. **Relancer l'environnement** (deux terminaux PowerShell séparés) :
   ```powershell
   # Terminal 1
   php artisan serve --port=8888

   # Terminal 2
   npm run dev
   ```
6. **Vérifier que le site répond** : ouvrir http://127.0.0.1:8888 dans le navigateur
7. **Reprendre directement à S7** avec l'option B confirmée (Q12) :
   - Scope : préfixer classes génériques (`app-*`, `layout-*`, `menu-*`, `bg-menu-theme`, `bg-navbar-theme`) avec `dd-`
   - Procédure : 6 lots (L1 → L6) avec commit atomique après chaque lot
   - Validation visuelle PO + `npm run dev` propre + console F12 propre **après chaque lot**
   - Détails dans la section 4 ci-dessus
8. **Stop après S7** pour validation visuelle PO sur http://127.0.0.1:8888

### Règles à NE PAS oublier

- **Ne JAMAIS modifier `master`** (filet de sécurité Sneat intact)
- **Ne JAMAIS commit `.env`** (gitignored maintenant, mais double-check `git status` avant chaque commit)
- **Ne JAMAIS recommencer l'audit** ni redemander d'arbitrage — toutes les décisions sont prises et documentées ici (Q10/Q11/Q12/Q13)
- **Workflow strict** : pour chaque sous-tâche → travail + commit conventional-commits + verify Laravel boot + report 3-5 lignes + wait for `VALIDÉ S{n}, suite`
- **Si situation imprévue / non couverte** : NE PAS décider seul → ajouter `Q14` (puis Q15...) à `ANALYZE_DESANONYMIZATION.md`, commit le doc, et attendre

### Si quelque chose semble incohérent au reboot

Vérifier dans cet ordre :
1. `git log --oneline -15` correspond bien à la section 3 ci-dessus (à un commit près)
2. Le working tree est clean (`git status` vide)
3. Master est toujours à `d604ee8` (`git log master --oneline` doit afficher uniquement ce commit)
4. `php artisan --version` affiche `Laravel Framework 12.58.0`
5. `php artisan config:show custom` ne contient AUCUNE clé `hasCustomizer` / `displayCustomizer` / `customizerControls`

Si l'un de ces 5 checks échoue, **STOP immédiat** et alerter le PO.

---

## Pré-S1.5 — Mise en ligne dream-digital.info (CLÔT 2026-05-08)

**Branche** : master (commits directs, sprint d'infrastructure)
**Durée réelle** : ~3h30 (jeudi 7 mai 2026 après-midi)
**PO** : MAPENDO Gentil

### Contexte
Avant Sprint 1.5 (redesign vitrine ITSP/CPaaS), le PO a décidé de
mettre en ligne le code Dream Digital actuel sur un VPS dédié
fraîchement provisionné chez OVH. Décision assumée par le PO :
déployer le dashboard Sneat rebrandé tel quel ("pour mettre la
machine en route"), avec protection Basic Auth pour limiter
l'exposition publique pendant que Sprint 1.5 prépare le contenu
final.

### Livrables techniques
- VPS dédié OVH 79.137.64.128 (Ubuntu 24.04 LTS, 16 GB RAM,
  8 cores Xeon E3-1230 v6, 3.6 TB SSD)
- Stack Laravel : Nginx 1.24 + PHP 8.4 FPM + PostgreSQL 16 +
  Composer 2.9.7 + Node 22.22.2 + Certbot 2.9.0 + Git 2.43.0
- Hardening : UFW (deny incoming, allow 22/80/443) + fail2ban
  (sshd jail, bantime 1h)
- Pool PHP-FPM dédié : `dreamdigital` (user/group), socket
  `/run/php/php8.4-fpm-dreamdigital.sock`
- Base PostgreSQL : `dreamdigital_db` / `dreamdigital_user`
- Repo GitHub privé : `github.com/leseulgentil/dream-digital-website`
- Deploy key SSH ed25519 (read-only) configurée
- Cert SSL OVH Sectigo wildcard `*.dream-digital.info` + apex
  (validité 2026-01-09 → 2026-10-15)
- HTTPS opérationnel : TLS 1.2+1.3 only, HSTS 1 an, OCSP stapling,
  ciphers modernes
- Basic Auth : user `dreamdigital`, password noté côté PO
- robots.txt : Disallow / (pas d'indexation Google)
- Routing fix (commit db632ac) : `/` pointe sur
  `/front-pages/landing` (vue Sneat marketing temporaire)

### Décisions stratégiques actées
- Scénario B initial (page d'attente séparée pré-S1.5) abandonné
  en cours de session — PO a opté pour déploiement direct du code
  master tel quel
- Adaptation landing en page d'attente Dream Digital différée à
  Sprint 1.5
- WordPress legacy sur mutualisé OVH déconnecté du domaine
  (DNS basculé vers VPS dédié)

### Dette technique reportée (voir TECH_DEBT.md)
- TD-001 : Doublons name `dashboard-analytics` (4 routes)
- TD-002 : Résidus marketing Sneat dans pages front
- TD-003 : Renouvellement cert SSL au 2026-09-15
- TD-004 : Configuration backups VPS

### Calendrier critique
- ⚠️ **2026-09-15** : alerte renouvellement SSL (30j avant
  expiration)
- **2026-10-15** : expiration cert OVH (deadline absolue)

### Prochaines étapes
- **Sprint 1.5** (redesign vitrine ITSP/CPaaS) sur branche
  `feature/sprint-1-5-redesign` — durée estimée 4-6 jours
- Démarrage : vendredi 8 mai 2026 (mode marathon)
- Brief de référence : `BRIEF_SPRINT_1_5_REDESIGN.md` (commit
  73d076f, amendements A-I appliqués)

### Statut
✅ **Pré-S1.5 OFFICIELLEMENT CLÔTURÉ** au 2026-05-08

---

*Checkpoint généré automatiquement avant reboot machine PO. Mise à jour à chaque transition de sous-tâche dans la session post-reboot.*
