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

🟡 **En attente de validation Q10** :
- Quelle option (A / B recommandée / C / D) ?
- Pour la question annexe : on exclut aussi `dream_digital` et on l'ajoute au `.gitignore` ?
- Si option B : tu valides la rotation `APP_KEY` après squash (commande : `php artisan key:generate`) ?
