# ANALYZE — Désanonymisation du template Sneat → Dream Digital

> Document de cadrage produit avant exécution. **Aucun fichier de code n'a été modifié.**
> Version : 1.0 — date : 2026-05-04 — branche : `feature/desanonymization`

---

## A. Compréhension des 10 objectifs du brief

| # | Objectif | Périmètre | Réversible ? |
|---|---|---|---|
| 1 | **Rebrand identité projet** | `composer.json`, `package.json`, `.env.example`, `config/variables.php`, métadonnées HTML, commentaires Blade/SCSS | Oui (find/replace) |
| 2 | **Renommer classes Sneat → `dd-*`** | `app-brand`, `layout-*`, `menu-*`, `bg-*-theme`, `template-customizer`. NE PAS toucher Bootstrap/libs/icons | Oui mais long |
| 3 | **Supprimer Template Customizer** | Composant Blade + JS + SCSS + entrée Vite + flag `hasCustomizer`/`displayCustomizer` dans `config/custom.php` | Oui (commit avant) |
| 4 | **Personnaliser variables design** | `resources/assets/vendor/scss/_custom-variables/_bootstrap-extended.scss` (actuellement quasi vide) + Google Fonts dans `commonMaster.blade.php` | Oui |
| 5 | **Remplacer logos & favicons** | Fichiers `public/assets/img/branding/` + références Blade dans navbar, footer, pages auth | Oui |
| 6 | **Supprimer démos non utilisées** | ⚠️ **NON APPLICABLE** dans la version Laravel — voir clarif Q8 | — |
| 7 | **Supprimer apps inutilisées** | Routes, controllers `app/Http/Controllers/apps/`, vues `resources/views/content/apps/`, entrées menu `resources/menu/verticalMenu.json` | Oui mais volumineux |
| 8 | **Configuration Vite production** | `vite.config.js` (déjà partiellement OK, à compléter pour `minify`/`sourcemap`/hashes explicites) | Oui |
| 9 | **Réécrire README.md** | ⚠️ Pas de `README.md` actuel — il s'agit donc de **CRÉER** un README Dream Digital, pas de remplacer | Oui |
| 10 | **Audit final résidus Sneat** | Génération `AUDIT_SNEAT.md` listant occurrences résiduelles avec fichier:ligne | — |

---

## B. État des lieux du Sneat tel qu'installé

### Branches Git
- Active : `feature/desanonymization`
- Filet de sécurité : **`master`** (pas `main` — à clarifier)
- 4 commits récents : 3 commits "Sneat installed and running" presque identiques + 1 commit briefs

### Stack confirmée
- Laravel **12.58.0** sur PHP **8.4.0** (alors que `composer.json` exige `^8.2`)
- Vite **6.3.5** + Bootstrap **5.3.5** + jQuery **3.7.1** + 70+ libs JS dans `package.json`
- DB par défaut : SQLite (fichier `dream_digital` à la racine, 86 KB, déjà migré)
- Serveur dev : `php artisan serve` sur port **8888**, Vite sur **5174**

### Structure clé observée
- `resources/views/layouts/` : 6 layouts (`commonMaster`, `contentNavbarLayout`, `horizontalLayout`, `blankLayout`, `layoutFront`, `layoutMaster`) + `sections/` (navbar, footer, menu, scripts, styles)
- `resources/views/content/` : ~150 vues regroupées par catégorie (`apps/`, `authentications/`, `cards/`, `dashboard/`, `front-pages/`, `tables/`, `forms/`, etc.)
- `resources/views/_partials/` : modals + offcanvas + macros
- `resources/assets/vendor/scss/` : SCSS Sneat structuré en `_bootstrap-extended/`, `_components/`, `_custom-variables/`, `pages/`
- `resources/assets/vendor/js/_template-customizer/` : 2 fichiers (`.scss` + `.html`) — le `.js` du customizer est ailleurs (probablement `resources/assets/vendor/js/template-customizer.js` car `.gitignore` ligne 47 le force-include)
- `app/Helpers/Helpers.php` : **génère dynamiquement** des classes `layout-menu-fixed`, `layout-navbar-fixed`, `layout-footer-fixed`, `layout-menu-collapsed` → ⚠️ **doit être renommé en cohérence** avec l'objectif 2
- `app/Http/Controllers/apps/` : 41 controllers correspondant aux apps Sneat (8 à supprimer, 4 à garder pour Users/Roles)
- `routes/web.php` : ~200 lignes, monolithique, 1 seule démo (pas de `demo-2` à `demo-5`)
- `config/variables.php` : **bourré d'URLs ThemeSelection** (creatorName, productPage, ogImage, repository, githubUrl, twitterUrl, instagramUrl…)
- `config/custom.php` : flags du customizer (`hasCustomizer=true`, `displayCustomizer=true`)
- `.env.example` : `APP_NAME=Laravel` (générique Laravel, rien de Sneat)
- **`README.md` ABSENT** à la racine
- `.gitignore` ligne 47 : `!/resources/assets/vendor/js/template-customizer.js` (force-include du JS customizer, à retirer après suppression)

### Volumétrie des résidus Sneat (scan rapide)
- "Sneat / themeselection / pixinvent" hors `vendor/` : **70 occurrences sur 9 fichiers** (briefs exclus du compte de cible)
- Classes `app-brand|layout-*|menu-*|template-customizer|bg-*-theme` dans `resources/` : **55+ occurrences sur 15+ fichiers** (truncated, probablement 200+)

### Démos
La version Laravel de Sneat **n'expose qu'UNE seule démo**. Pas de dossier `dashboards/` ni de routes `demo-2..demo-5`. **L'objectif 6 du brief ne s'applique pas** dans ce contexte. Les variations de layout (vertical/horizontal/blank/front) sont des **layouts partagés**, pas des démos séparées.

### Apps présentes (Controllers)
À **supprimer** : `Email`, `Chat`, `Calendar`, `Kanban`, `Invoice*` (5 controllers), `Ecommerce*` (19 controllers), `Academy*` (3), `Logistics*` (2). Total : **34 controllers + ~50 vues + ~50 routes**.
À **garder** : `UserList`, `UserView*` (5), `AccessRoles`, `AccessPermission`. Total : 7 controllers.

---

## C. Questions à clarifier avant exécution

| # | Question | Pourquoi c'est bloquant |
|---|---|---|
| Q1 | **Palette définitive ?** Ton intro propose `#0A1F44` (bleu profond) + `#00D9FF` (cyan) ; le brief propose `#1F4E79` + `#00D9FF`. Lequel applique-t-on ? Confirmer aussi `success / warning / danger`. | Variables SCSS à figer dès l'objectif 4. Si on change après, il faut recompiler tous les composants. |
| Q2 | **Typographie validée ?** Brief propose Outfit (body) + Bricolage Grotesque (titres) + JetBrains Mono (code) via Google Fonts CDN. OK ? Sinon préciser. | Impact perfs (CDN externe) + identité visuelle. |
| Q3 | **Customizer : supprimer ou désactiver ?** Le brief dit "supprimer entièrement". Or il est aussi possible de le **désactiver** via `config/custom.php` (`hasCustomizer=false`) — réversible et zéro risque de casser les classes utilisées ailleurs. Je recommande **désactiver d'abord, supprimer ensuite** une fois certain qu'aucune autre fonctionnalité n'en dépend. | Réversibilité + risque visuel. |
| Q4 | **Branche d'intégration ?** Le brief dit "merge sur `develop`" ; ton intro dit "branche `main` contient le commit initial". En réalité la branche initiale s'appelle **`master`**. Tu veux qu'on : (a) garde `master` comme filet, (b) renomme `master` → `main`, (c) crée `develop` ? | Convention de workflow pour la suite. |
| Q5 | **Logos provisoires :** OK pour utiliser le **texte "Dream Digital"** comme logo provisoire (confirmé en intro). Veux-tu aussi un **picto SVG temporaire** (par ex. cercle bleu + initiales DD) pour le favicon, ou on garde le favicon Sneat actuel jusqu'au logo final ? | Le favicon actuel est `public/assets/img/favicon/favicon.ico` (Sneat). Affecte la perception "résiduelle" Sneat. |
| Q6 | **`composer.json` à rebrander ?** Il est resté `laravel/laravel` (générique Laravel, pas Sneat). On le rebrand en `dream-digital/website` ou on laisse `laravel/laravel` ? | Cosmétique, pas bloquant. |
| Q7 | **Licence Sneat :** je n'ai trouvé aucun fichier `LICENSE` Sneat à la racine. Le brief demande de créer `_template-license/` + `.gitignore`. Confirmer qu'aucun PDF/MD de licence ThemeSelection n'existe ailleurs (Téléchargements ?) — sinon **où est-il** pour qu'on l'isole ? | Conformité licence. |
| Q8 | **Confirmation objectif 6 N/A ?** Je propose de marquer l'objectif 6 comme *"Non applicable — version Laravel monolithique, pas de démos multiples"* dans `AUDIT_SNEAT.md`. OK ? | Évite de chercher un travail inexistant. |
| Q9 | **3 commits "Sneat installed" très similaires** (`d604ee8`, `7b5803f`, `fa0a0af`) sur `master`. Intentionnel ou erreur ? Faut-il squash avant de partir ? | Cosmétique Git. |

---

## D. Plan d'attaque détaillé

Ordre choisi pour minimiser les risques de régression visuelle. **Commit atomique après chaque sous-tâche**, vérification `php artisan serve` + `npm run dev` à chaque fois.

| Sous-tâche | Description | Fichiers touchés (estim.) | Risque |
|---|---|---|---|
| **S0** | Préparation : créer `_template-license/.gitignore`, scripts d'audit (scan classes), capture d'écran "avant" | ~2 fichiers | Nul |
| **S1** | **Obj. 1 + 9 fusionnés** — Rebrand identité : `package.json`, `composer.json` (si Q6), `.env.example` (`APP_NAME`), `config/variables.php` (toutes URLs/textes), création `README.md` Dream Digital | ~5 fichiers | Faible |
| **S2** | **Obj. 3 partie A** — *Désactiver* le customizer via `config/custom.php` (`hasCustomizer=false`, `displayCustomizer=false`). Vérifier que le site rend toujours sans le panel. | ~1 fichier | Faible |
| **S3** | **Obj. 4** — Variables design Dream Digital dans `_custom-variables/_bootstrap-extended.scss` + ajout Google Fonts dans `commonMaster.blade.php` (head) + `layoutFront.blade.php`. `npm run dev` et capture pour comparer. | ~3 fichiers | **Moyen** (visuel) |
| **S4** | **Obj. 5** — Logos provisoires : créer `public/img/brand/` avec SVG texte + remplacer les `<img src=...>` Sneat dans navbar, footer, layouts auth, layouts front | ~10-15 fichiers | Faible |
| **S5** | **Obj. 7** — Suppression apps inutiles : supprimer routes (web.php), controllers (`app/Http/Controllers/apps/Email.php` etc.), vues (`resources/views/content/apps/app-email*.blade.php` etc.), entrées menu (`resources/menu/verticalMenu.json`), assets SCSS pages (`pages/app-email.scss` etc.) | **~150 fichiers / suppressions** | **Moyen** (effets de bord routes) |
| **S6** | **Obj. 3 partie B** — Suppression complète du customizer : supprimer `resources/assets/vendor/js/_template-customizer/`, le `template-customizer.js` (chercher), inclusion Blade, retirer ligne `.gitignore`, retirer la clé `customizerControls` de `config/custom.php` | ~5 fichiers | Faible (déjà désactivé en S2) |
| **S7** | **Obj. 2** — Renommage classes `app-brand → dd-brand`, `layout-* → dd-layout-*`, etc. : (1) scan complet, (2) génération `_class-rename-map.json`, (3) script de remplacement word-boundary `\b`, (4) **mettre à jour `Helpers.php` qui génère ces classes**, (5) test après chaque batch (4-5 commits) | **~80-120 fichiers** | **ÉLEVÉ** (visuel + JS bind) |
| **S8** | **Obj. 8** — Compléter `vite.config.js` (`build.minify`, `sourcemap: false`, `output.entryFileNames` hashés), `npm run build`, vérifier `public/build/manifest.json` et noms hashés | ~1 fichier | Faible |
| **S9** | **Obj. 10** — Audit final : script de scan `Sneat|themeselection|pixinvent|app-|layout-|menu-|template-customizer|bg-*-theme`, génération `AUDIT_SNEAT.md` avec fichier:ligne + suggestion par résidu | ~1 fichier généré | Nul |
| **S10** | Validation finale : `npm run build` + `php artisan serve` + parcours navigateur des principales pages (dashboard, login, users, roles), capture "après" | — | Nul |

**Estimation effort** : conforme au brief (2 à 4 sessions Claude Code supervisées).

---

## E. Risques techniques identifiés

1. **R1 — Helpers.php génère des classes `layout-*` dynamiquement**
   `Helpers::appClasses()` retourne `'layout-menu-fixed'`, `'layout-navbar-fixed'`, `'layout-footer-fixed'`, `'layout-menu-collapsed'`. Si on renomme les classes CSS sans toucher au PHP, la grille de layout casse. **Mitigation** : renommer Helpers.php DANS LE MÊME COMMIT que le SCSS associé.

2. **R2 — Le JS du customizer manipule `layout-menu-toggle`, `layout-overlay`, `bg-menu-theme`**
   Si on supprime le customizer en S6 *après* avoir renommé les classes en S7, plus de souci. Si on inverse l'ordre, risque de JS qui pointe vers une classe inexistante.

3. **R3 — Renommage par regex sans word-boundary**
   Remplacer `layout-menu` casserait `layout-menu-toggle`, `layout-menu-collapsed`, `layout-menu-fixed`. **Mitigation** : utiliser `\b{class}\b` strict + ordre par longueur décroissante (remplacer d'abord les classes les plus longues).

4. **R4 — Classes ressemblant à Sneat dans des libs tierces**
   Ex : `menu-content` chez Bootstrap, `swiper-slide-active` (false-positive `swiper-*`), `dropdown-menu` (Bootstrap, **NE PAS** renommer). **Mitigation** : whitelist explicite dans le script + relecture humaine du diff avant chaque commit.

5. **R5 — `data-template`, `data-skin`, `data-bs-theme`**
   Le HTML `<html data-template="vertical-menu-template">` est lu par le JS pour appliquer des comportements. Si on renomme les classes mais pas les data-attributes, désynchro. **Mitigation** : ne PAS renommer les data-attributes (ce sont des conventions internes Bootstrap/Sneat-JS), seulement les classes.

6. **R6 — Cookies `customize_skin`, `customize_semi_dark`, `LayoutCollapsed`, `contentLayout`, `navbarType`, `headerType`, `admin-mode`, `front-mode`, `direction`**
   Lus par `Helpers.php` pour persister les choix utilisateur. À nettoyer du navigateur entre les tests sinon résultats incohérents.

7. **R7 — RTL et thème dark**
   Sneat supporte `data-bs-theme="dark"` + RTL. Tester les deux modes après les changements de variables (S3) et après le renommage (S7).

8. **R8 — `package-lock.json` contient "sneat"**
   À régénérer après modification de `package.json` via `npm install` (créera un commit lourd mais cohérent).

9. **R9 — Vite cache & assets compilés**
   Si `npm run build` est lancé pendant la transition, des assets `sneat-*.css` peuvent rester dans `public/build/`. Vider `public/build/` avant le build final de S8.

10. **R10 — Pas de tests automatisés du rendu**
    Aucune couverture E2E (Playwright/Cypress) sur les pages. La détection de régression visuelle est **100% manuelle**. Une page critique non testée peut casser silencieusement.

---

## F. Dépendances et commandes à exécuter

### Avant de commencer (pré-flight)
```powershell
# Vérifier que tout tourne
php artisan serve --port=8888    # déjà OK (terminal séparé)
npm run dev                       # déjà OK sur 5174
git status                        # doit être clean sur feature/desanonymization
```

### Pendant l'exécution (par sous-tâche)
```powershell
# Après chaque modif Blade/PHP
# rien à recompiler — Laravel sert directement

# Après chaque modif SCSS/JS dans resources/assets/
# Vite hot-reload automatique (déjà actif)

# Vérification rapide après commit
git status
git log --oneline -5
```

### Pour la validation finale (S8 + S10)
```powershell
# Build production
npm run build

# Vérifier les artefacts
ls public/build/assets    # doivent être *-[hash].js / *-[hash].css
cat public/build/manifest.json | head -20

# Test serveur en mode "production-like"
# (Vite ne tourne plus, on charge depuis public/build/)
```

### Outils ponctuels à installer si nécessaire
- **Aucune nouvelle dépendance npm/composer** prévue
- Le script de renommage de classes (S7) sera un script Node.js inline ou PHP one-shot — **pas de package supplémentaire**

---

## Statut

🟡 **Validation reçue 2026-05-04**. Mais **stop avant S0** : alerte sécurité découverte au moment de préparer le squash Q9 (cf. Q10 ci-dessous).

---

## C-bis. Question imprévue Q10 — `.env` présent dans l'historique Git

**Découverte (2026-05-04, début S0)** : en préparant le soft reset pour squasher les 2 commits doublons "Sneat installed", j'ai constaté que le fichier `.env` (créé par `composer install`) a été committé dans `7b5803f` et est donc présent dans l'historique de `feature/desanonymization`. Il est en revanche **absent de `d604ee8`** (le commit "vendor template intact" sur `master`), donc le filet de sécurité reste sain.

**Cause** : la ligne `# .env` du `.gitignore` actuel (ligne 10) est commentée. Conséquence : `git add .` lors de l'install initiale a embarqué le `.env`. Le brief stipule explicitement *"Ne jamais committer les `.env`, ni les credentials"* (BRIEF_DD_DESANONYMIZATION.md ligne 18) — donc cette ligne du brief est **violée par l'état actuel** du repo.

**Aucun remote n'existe** → la fuite n'est pas matérialisée publiquement, mais l'`APP_KEY` (et tout autre secret présent dans le `.env`) est dans l'historique local. Si ce repo est poussé un jour sur GitHub/GitLab sans nettoyage, fuite.

### 4 options possibles

| Option | Action | Avantage | Inconvénient |
|---|---|---|---|
| **A** | Squash basique : garder `.env` dans le commit Sneat squashé | Le plus simple, conforme strict à Q9 | `.env` reste dans l'historique, problème reporté |
| **B** ⭐ | Squash + exclure `.env` du nouveau commit + activer `.env` dans `.gitignore` (décommenter la ligne 10) dans le même commit | Profite du squash pour nettoyer ; `.env` disparaît effectivement de l'historique de la branche ; gitignore corrigé | Recommande de **régénérer `APP_KEY`** (`php artisan key:generate`) car l'ancienne a été exposée localement |
| **C** | Annuler le squash Q9, gérer `.env` plus tard | Évite de mélanger 2 sujets | Manque l'opportunité de nettoyer en une seule passe ; problème non résolu |
| **D** | Squash + `git filter-repo`/`filter-branch` pour purger `.env` de TOUTE l'historique de la branche | Le plus sécurisé | Outil supplémentaire à installer, plus lourd, et inutile ici car `.env` est déjà dans un seul commit qui sera squashé en B |

**Recommandation : option B**, parce que (1) elle résout le problème en une opération, (2) elle est cohérente avec la règle "ne jamais committer `.env`" du brief, (3) le filet de sécurité `master` reste intact (pas de `.env` dedans), (4) aucun remote → pas d'impact sur des collaborateurs.

### Question annexe (pas bloquante mais à considérer)

Le fichier `dream_digital` (86 KB, base SQLite de dev) est aussi commité dans `7b5803f`. C'est inhabituel de versionner une DB de dev. **Faut-il l'exclure du squash et l'ajouter au `.gitignore` pendant qu'on y est ?** Pattern standard Laravel : `database/database.sqlite` est ignoré, ici c'est `dream_digital` à la racine.

---

✅ **Q10 résolu (2026-05-05)** : option B retenue. Filter-repo exécuté, `.env` + `dream_digital` purgés de l'historique de `feature/desanonymization`, master intact à `d604ee8`, `APP_KEY` rotée, `SECURITY.md` créé.

---

## C-ter. Q11 — Brand Kit v1.2 reçu (supersedes Q1, Q2 et réordonne la séquence)

**Reçu 2026-05-05** : `BRAND_KIT_DREAM_DIGITAL.md` (v1.2 finale, 15 sections) + `_dream-digital-tokens.scss` à la racine du projet, + dossier `LOGO DREAM DIGITAL OR/` contenant les 4 PNG officiels.

### Changements par rapport aux Q1–Q9

| Décision | Avant (Q1/Q2) | Après (Brand Kit v1.2) |
|---|---|---|
| Couleur primaire | `#0A1F44` (deep blue) | `#335F5F` (Petrol Teal) — usage ~30% |
| Couleur accent | `#00D9FF` (signal cyan) | `#14B8A6` (Teal-Cyan SPOT, max 5%, 3-4 occurrences/page) |
| Couleur CTA forts | n/a | `#0E121C` (Action Black) — usage ~15% |
| Status colors | inchangé | inchangé (sémantique uniquement) |
| Typographie body | Outfit | **Inter** (300/400/500/600/700/800) |
| Typographie display | Bricolage Grotesque | **Inter** (mêmes weights) |
| Typographie code | JetBrains Mono | JetBrains Mono (inchangé) |
| Logos | placeholder texte SVG provisoire | **PNG officiels** dans `LOGO DREAM DIGITAL OR/` à copier vers `public/img/brand/` (Phase 1) |
| Positionnement | "panafricain" (briefs) | **GLOBAL CPaaS/ITSP**, 200+ pays, 60%+ clients hors Afrique (jamais "Afrique" comme limitation) |
| Tagline | n/a | EN: *"Voice. SMS. eSIM. And More."* / FR: *"Voix. SMS. eSIM. Et bien plus."* |
| Mascotte | n/a | Pango le pangolin (provisoire SVG, usages limités) |

### Nouvelle séquence d'exécution (v1.2 amendée)

```
S0 → S1 → S2 → S6 → S7 → S8 → S9 → S3 → S5 → S10
```

S3 (Variables design) et S5 (Logos) sont déplacés **à la fin** car ils étaient bloqués sans Brand Kit v1.2. Désormais débloqués et exécutables. **S4 (placeholder logos provisoires) supprimé** — directement les vrais logos PNG en S5.

### Stops de validation visuelle (cumulés)

- ✅ Après **S0** (déjà fait)
- 🟡 Après **S1** (en cours)
- 🟡 Après **S3** (mandate Brand Kit v1.2)
- 🟡 Après **S5** (mandate Brand Kit v1.2)
- (workflow général : stop après chaque sous-tâche par défaut, conformément à `feedback_workflow`)

### Notes techniques implémentation

- `_dream-digital-tokens.scss` à copier dans `resources/assets/vendor/scss/_custom-variables/_dream-digital.scss` (S3)
- Importer en TÊTE de `_bootstrap-extended.scss` : `@import "./dream-digital";`
- Google Fonts (Inter + JetBrains Mono) à ajouter dans `commonMaster.blade.php` ET `layoutFront.blade.php` (S3)
- Logos PNG à renommer/copier de `LOGO DREAM DIGITAL OR/` vers `public/img/brand/` (S5)
- Dossier source `LOGO DREAM DIGITAL OR/` : à archiver dans `public/img/brand/originals/` ou à gitignorer (décision en S5)

---

## C-quater. Q12 — Divergence sur le scope de S7 (renommage classes)

**Découverte 2026-05-05, début S7** : ton message de validation S6 décrit S7 comme *"remplacer toutes les occurrences de classes CSS, IDs HTML, attributs `data-*` et variables JS contenant le préfixe `sneat-*`"* avec des exemples comme `.sneat-vertical-menu`, `data-sneat-template-name`, `window.SneatConfig`. 

**Or aucune de ces formes n'existe dans le code Sneat installé.** Sneat n'utilise pas le préfixe `sneat-` pour ses identifiants techniques.

### Mesures objectives

| Recherche | Résultat (hors `vendor/`) |
|---|---|
| Classes `.sneat-*` (case-insensitive) | **0 occurrence** dans le code applicatif |
| Identifiants `#sneat-*`, `data-sneat-*`, `window.Sneat*` | **0 occurrence** |
| Mention "sneat" | 4 fichiers : 3 docs (briefs/BRANDING/_template-license) + 1 URL doc dans `resources/menu/verticalMenu.json` (lien `themeselection.com/demo/sneat-...`) |
| Classes génériques Sneat (`app-brand`, `layout-*`, `menu-*`, `bg-menu-theme`, `bg-navbar-theme`) | **521+ occurrences** sur 30+ fichiers (probablement >700 sur l'ensemble du repo) |

### Quel est le vrai scope du brief ?

Le BRIEF_DD_DESANONYMIZATION.md ligne 38-76 (Objectif 2) **et mon ANALYZE section A ligne 13** sont alignés : ce qu'il faut renommer, ce sont les **classes Sneat-spécifiques génériques** (préfixées `app-`, `layout-`, `menu-`, `template-`, `bg-*-theme`), en leur **AJOUTANT** le préfixe `dd-` :

> ⚠️ **Table de mappings ci-dessous SUPERSEDED par Q14** (voir section C-sextus). Les mappings hétérogènes (`app-brand → dd-brand`, `bg-menu-theme → dd-bg-menu`) sont remplacés par la convention uniforme **prepend `dd-` simple** : `<classe>` → `dd-<classe>` pour TOUTES les classes. Le scope général Q12 (catégories de classes, exclusions Bootstrap/libs/`data-*`, procédure 6-lots) reste valide.

| Avant (obsolète) | Après (obsolète) |
|---|---|
| `app-brand` | `dd-brand` |
| `app-brand-link` | `dd-brand-link` |
| `layout-wrapper` | `dd-layout-wrapper` |
| `layout-menu` | `dd-layout-menu` |
| `layout-navbar-fixed` | `dd-layout-navbar-fixed` |
| `menu-vertical` | `dd-menu-vertical` |
| `menu-inner` | `dd-menu-inner` |
| `menu-link` | `dd-menu-link` |
| `bg-menu-theme` | `dd-bg-menu` |
| `bg-navbar-theme` | `dd-bg-navbar` |
| ... | ... |

**Et NE PAS toucher** : Bootstrap (`btn`, `card`, `nav`, etc.), libs tierces (`swiper-*`, `apex-*`, `select2-*`, `dataTable*`, `sweetalert*`), icons (`ti ti-*`, `bx bx-*`).

### Trois options possibles

| Option | Travail | Effet |
|---|---|---|
| **A** | Suivre ton message à la lettre (rechercher `sneat-*`) | Quasi-rien à faire (~1 URL dans menu JSON). S7 marquée "terminée" en 5 min. **Ne résout pas l'objectif 2 du brief.** |
| **B** ⭐ | Suivre le brief original + ANALYZE (préfixer `app-/layout-/menu-/bg-*-theme` avec `dd-`) | ~700 occurrences sur 30+ fichiers. **6 lots de commits** comme tu l'as proposé. Travail risqué nécessitant `Helpers.php` à mettre à jour en cohérence (cf. R1 dans mes risques). C'est ce que j'avais planifié. |
| **C** | Hybride : faire B et profiter pour aussi traiter les rares résidus textuels `sneat-*` (l'URL dans verticalMenu.json + commentaires SCSS où le mot apparaît) | ~700 occurrences + ~5 résidus textuels |

**Recommandation : option B (ou C qui est juste B + petit bonus).**

### Risques propres à l'option B (rappel R1, R2, R3, R5 de section E)

- **Helpers.php** génère dynamiquement `'layout-menu-fixed'`, `'layout-navbar-fixed'`, `'layout-footer-fixed'`, `'layout-menu-collapsed'` — doit être renommé **dans le même commit** que les classes CSS correspondantes
- **menu.js** (~1000 lignes) utilise des sélecteurs `'#layout-menu'`, `.layout-menu-toggle` en dur dans `document.querySelector(...)` — à renommer en sync avec le HTML
- **main.js / front-main.js / helpers.js** : idem, sélecteurs en dur
- **Word boundary strict** : remplacer `layout-menu` casserait `layout-menu-toggle`, `layout-menu-fixed`, etc. → ordre de remplacement par longueur décroissante OBLIGATOIRE
- **Le data-attribute `data-template="vertical-menu-template"`** et autres `data-*` ne sont PAS dans le scope (ce sont des conventions Sneat-JS internes, pas des classes CSS) — à confirmer

---

## C-quinquies. Q13 — Décision dark mode switcher pour S9

**Validé 2026-05-05** : restaurer le dark mode switcher (Light/Dark/System) en S9 comme contrôle UX standalone, sans dépendance au customizer disparu.

### Spécifications à implémenter en S9

1. **Lieu de réinjection** : `resources/views/layouts/sections/navbar/navbar-partial.blade.php` + `resources/views/layouts/sections/navbar/navbar-front.blade.php` (les blocs supprimés en S6 étaient gardés par `@if hasCustomizer == true`)
2. **Stockage** : `localStorage` sous clé `dd-theme` (indépendante du customizer), valeurs `light` / `dark` / `system`, défaut `system`
3. **Détection initiale** : `prefers-color-scheme` du navigateur quand `system` est sélectionné
4. **Application** : attribut `data-bs-theme` sur `<html>` (Bootstrap 5.3+ supporte nativement)
5. **Pas de référence** à `window.templateCustomizer` (qui sera retiré des 4 fichiers JS dead-safe `helpers.js`/`main.js`/`front-main.js`/`menu.js` en S9)
6. **Icônes** : Tabler Icons `ti-sun`, `ti-moon`, `ti-device-desktop` — cohérent avec le reste du UI

Ce point est ajouté à la sous-tâche S9 du plan.

---

✅ **Q12 résolu (2026-05-05)** : option **B** retenue (préfixer classes génériques avec `dd-`, `data-*` hors scope, résidus textuels "sneat" → S9). Voir SPRINT_STATE.md section 2.

---

## C-sextus. Q14 — Convention de renommage Option A retenue (supersedes table Q12)

**Validé 2026-05-05** : suite au scan exhaustif L1 du sprint S7, divergence détectée entre la table de renommage de Q12 (section C-quater — mappings hétérogènes `app-brand → dd-brand`, `bg-menu-theme → dd-bg-menu`) et la convention "prepend simple" plus naturelle. Décision : retenir l'**Option A — prepend systématique du préfixe `dd-`** pour TOUTES les classes du namespace.

### Convention figée

Pour toute classe Sneat ciblée par S7 : `<classe>` → `dd-<classe>` (prepend strict, aucune substitution).

| Avant | Après |
|---|---|
| `app-brand` | `dd-app-brand` |
| `app-brand-link` | `dd-app-brand-link` |
| `app-brand-img-collapsed` | `dd-app-brand-img-collapsed` |
| `layout-menu` | `dd-layout-menu` |
| `layout-menu-toggle` | `dd-layout-menu-toggle` |
| `layout-menu-fixed-offcanvas` | `dd-layout-menu-fixed-offcanvas` |
| `menu-item` | `dd-menu-item` |
| `menu-item-closing` | `dd-menu-item-closing` |
| `bg-menu-theme` (si réintroduit) | `dd-bg-menu-theme` |
| `bg-navbar-theme` | `dd-bg-navbar-theme` |

### Justifications

1. **Cohérence absolue** : préfixe `dd-` uniforme sur toutes les classes
2. **Traçabilité Git** : `git grep "\bdd-"` liste l'ensemble du namespace Dream Digital
3. **Réversibilité** : un revert via regex `\bdd-` est trivial si nécessaire
4. **Conformité conventions CSS** : aligné avec préfixage standard (BEM, Tailwind, Bootstrap utility-prefix)

### Conséquences sur Q12

La **table de renommage** de la section C-quater (Q12) qui proposait des mappings hétérogènes est **superseded by Q14**. Le scope général Q12 (catégories de classes à renommer + procédure 6-lots + exclusions Bootstrap/libs/icons/`data-*`) **reste valide** ; seule la table de mappings est remplacée par la convention uniforme Q14 ci-dessus.

### Scope L1 ajusté (post scan exhaustif 2026-05-05)

**+24 classes découvertes** au-delà de la liste explicite Q12, à inclure dans L1 (Q12 disait *"etc. à scanner exhaustivement"* pour `menu-*`, l'esprit s'étend à `layout-*` et `app-brand-*`) :

- **app-brand sub-classes** : `app-brand-img`, `app-brand-img-collapsed`
- **layout state-classes** : `layout-menu-fixed-offcanvas`, `layout-menu-offcanvas`, `layout-navbar-full`, `layout-menu-hover`, `layout-menu-horizontal`, `layout-menu-expanded`, `layout-transitioning`, `layout-no-transition`, `layout-menu-100vh`, `layout-menu-link-no-transition`
- **menu state-classes** : `menu-block`, `menu-header`, `menu-header-text`, `menu-no-animation`, `menu-item-closing`, `menu-item-animating`, `menu-horizontal-prev`, `menu-horizontal-next`, `menu-horizontal-wrapper`, `menu-divider`, `menu-collapsed`, `menu-mobile-toggler`, `menu-text`, `menu-inner-shadow`

**−5 classes anomalies** retirées du scope L1 (0 occurrence en SCSS) mais à vérifier exhaustivement en L2 (Blade) et L3 (JS) — si elles apparaissent en HTML/JS sans définition SCSS, on les renomme quand même pour éviter référence Sneat orpheline :

- `layout-compact` (probable état JS)
- `layout-wide` (probable état JS)
- `bg-menu-theme` (peut exister en Blade/JS, pas en SCSS)
- `menu-content` (résidu doc obsolète probable, à confirmer en L2/L3)
- `text-menu-icon` (à vérifier en L2 Blade)

**⚠️ Attention JS L3 — classes ajoutées dynamiquement** : trois classes sont injectées dynamiquement par JS (`addClass`/`removeClass`) pendant les animations. Leur renommage SCSS L1 + JS L3 doit être **strictement synchrone** sinon les animations cassent silencieusement (ne se déclenchent plus, sans erreur console) :

- `layout-transitioning`
- `layout-no-transition`
- `menu-no-animation`

### Total scope L1 final

- **633 occurrences** (sélecteurs CSS) sur **13 fichiers SCSS**
- **58 classes uniques**
- Ordre de remplacement : **longueur décroissante stricte** (sous-classes AVANT classes parentes)
- Pattern regex word-boundary strict : `\bCLASS(?![-\w])` (lookahead négatif → zéro collision préfixe/sous-classe)
- Variables Sass `$menu-*` et mixins `@include menu-*` : **hors scope L1** (compile-time, n'apparaissent pas dans le CSS final)

---

## C-septies. Q16 — `menu-content` et `text-menu-icon` retirées définitivement du scope S7

**Validé 2026-05-05** : suite au scan exhaustif L2 sur l'ensemble du codebase (SCSS + Blade), confirmation que les classes `menu-content` et `text-menu-icon` listées dans la table Q12 originale (et dans `BRIEF_DD_DESANONYMIZATION.md` Objectif 2) ne figurent dans **aucun fichier source** du Sneat installé.

### Mesures objectives

| Classe | Occurrences SCSS (sélecteurs) | Occurrences Blade (tous contextes) | Total |
|---|---:|---:|---:|
| `menu-content` | 0 | 0 | **0** |
| `text-menu-icon` | 0 | 0 | **0** |

### Cause probable

Ces classes ont probablement existé dans des versions antérieures de Sneat (ou dans d'autres templates ThemeSelection génériques) et leur mention dans le brief/Q12 reflète une référence à l'ensemble du catalogue Sneat sans vérification du sous-set installé (Sneat Pro **Laravel 12 v4.0.0**).

### Conséquences

- **Retirées définitivement** du scope total S7 : aucun lot L1 → L6 ne traite ces noms
- Aucun risque de régression : ces classes n'apparaissent dans aucun fichier source
- Les vérifications L3 (JS) et L4 (Helpers.php) **n'auront pas** à les chercher
- Scope final S7 : **66 classes uniques** = 58 L1 (SCSS) + 3 anomalies confirmées HTML (`layout-compact`, `layout-wide`, `bg-menu-theme`) + 5 découvertes L2 (`layout-demo-{wrapper,info,placeholder}`, `layout-example-{sidebar,content-inner}`)

### Ce qu'on garde dans la mémoire du sprint

Cette résolution est mémorialisée ici pour qu'un futur lecteur de l'ANALYZE / Q12 / Q14 ne perde pas de temps à chercher ces classes inexistantes. Si un jour Dream Digital migre vers une version plus récente de Sneat où ces classes apparaîtraient, il faudra réévaluer.

---

## C-octies. Q17/Q18 — Règle commentaires clarifiée pour S7 (et tout futur sprint)

**Validé 2026-05-05** : suite à la détection en audit L3 d'un commentaire JS contenant une classe cible (`assets/js/main.js:112` — `// Display menu toggle (layout-menu-toggle) on hover with delay`), la règle initiale "ne pas toucher aux commentaires" a été clarifiée.

### Q17 — Décision immédiate

Le commentaire JS référençant explicitement une classe technique (`layout-menu-toggle`) a été **renommé** lors du commit L3 pour éviter une drift documentation (un commentaire référençant `layout-menu-toggle` alors que le code utilise désormais `dd-layout-menu-toggle` induit en erreur tout dev qui lit le code dans 6 mois).

### Q18 — Règle générale codifiée

| Type de commentaire | Action S7 | Justification |
|---|---|---|
| **Blade `{{-- --}}`** | ❌ Ne pas toucher | Supprimés au rendu serveur, jamais visibles en prod |
| **HTML `<!-- -->`** | ✅ Renommer si référence technique | Présents dans la source HTML servie au navigateur (Ctrl+U), donc visibles |
| **JS `//` ou `/* */` avec référence à un nom de classe/ID précis** | ✅ Renommer | Cohérence documentaire — drift code/comment inacceptable |
| **JS contextuel sans référence technique** (ex : `// 2022 - Sneat purchased`) | ❌ Ne pas toucher | Contexte historique factuel, ne pollue pas la lecture du code |

### Application rétroactive

- **L2 (Blade)** : 1 commentaire HTML `<!-- ! Not required for layout-without-menu -->` déjà renommé en `dd-layout-without-menu` lors du commit L2 (`021f8cf`). Conforme à Q18.
- **L3 (JS)** : 1 commentaire JS `// Display menu toggle (layout-menu-toggle) on hover with delay` renommé en `dd-layout-menu-toggle` lors du commit L3 (`277dcff`). Conforme à Q18.

### Application future

Les lots restants (L4 Helpers.php, L5 menu JSON, L6 rename-map) suivront cette règle Q18 si un commentaire technique est rencontré.
