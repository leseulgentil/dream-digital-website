# TECH_DEBT — Dette technique Dream Digital

Ce fichier liste les dettes techniques héritées du template Sneat
ou accumulées pendant les sprints, à régler dans des sprints
ultérieurs.

## Format
- **ID** : identifiant court
- **Origine** : sprint où la dette a été identifiée
- **Plan de résolution** : sprint cible où elle sera réglée

═══════════════════════════════════════════════════════════════

## TD-001 — Doublons de noms de routes dashboard-analytics

**Origine** : Pré-S1.5 (post-routing fix db632ac)
**Plan de résolution** : Sprint 1.5 (refactoring routing global)
**Sévérité** : Moyenne (impact perf, pas fonctionnel)

### Description
Le fichier `routes/web.php` hérité de Sneat contient 4 routes qui
partagent le même name `dashboard-analytics` (lignes 165, 176,
177, 178). Cela rend `php artisan route:cache` impossible :
Laravel refuse de sérialiser des routes avec des noms dupliqués.

### Impact
- `route:cache` échoue → Laravel re-parse `routes/web.php` à
  chaque requête HTTP
- Perte de perf imperceptible en staging (basic auth, faible
  trafic) mais significative en production avec trafic réel
- Pas d'impact fonctionnel : le routing fonctionne en mode
  runtime sans cache

### Plan de résolution
À régler en Sprint 1.5 lors du refactoring complet du routing
(intégration i18n + multi-pays). Les routes Sneat redondantes
seront soit supprimées, soit renommées de manière unique
(`dashboard-analytics-2`, `dashboard-analytics-crm`, etc.).

### Workaround actuel
On tourne sans `route:cache`. Acceptable pour staging.

═══════════════════════════════════════════════════════════════

## TD-002 — Résidus marketing Sneat dans les pages front

**Origine** : Sprint 0 (S10 — audit final)
**Plan de résolution** : Sprint 1.5 (redesign vitrine ITSP)
**Sévérité** : Haute (image publique du site)

### Description
Plusieurs pages contiennent encore du contenu marketing/démo Sneat :

- `/front-pages/landing` : gradient violet hero, mockup PNG
  dashboard Sneat, copy "Production-ready Admin Template",
  "One dashboard to manage all your businesses"
- `/layouts-example/*` : 8 vues de démo des layouts Sneat
- `/apps/ecommerce-referrals` : placeholder pixinvent.com
- `extended-ui-sweetalert2.js` : lien démo pixinvent
- `ui-app-brand.js` : script ciblant `#layout-menu1/2/3/4` de
  démo (non utilisé en production)

### Impact
- Image publique inappropriée pour un opérateur télécom B2B
  carrier-grade
- Confusion potentielle pour les visiteurs (référence à des
  produits Sneat / pixinvent étrangers à Dream Digital)

### Plan de résolution
À régler en Sprint 1.5 lors de la refonte complète de la vitrine
selon le brief BRIEF_SPRINT_1_5_REDESIGN.md (commit 73d076f,
amendements A-I appliqués).

═══════════════════════════════════════════════════════════════

## TD-003 — Renouvellement cert SSL OVH

**Origine** : Pré-S1.5 (déploiement initial)
**Plan de résolution** : Action manuelle PO en septembre 2026
**Sévérité** : Critique (expiration = site inaccessible)

### Description
Le cert SSL installé est un OVH Sectigo wildcard
*.dream-digital.info + apex, valide du 2026-01-09 au 2026-10-15
(durée raccourcie ~9 mois conforme aux nouvelles règles
CA/Browser Forum 2024+).

### Impact si non-traité
À l'expiration du 2026-10-15, le site basculera sur "cert
expiré" → tous les navigateurs afficheront un warning rouge
"site non sécurisé" → impact réputationnel et commercial majeur.

### Plan de résolution
Mettre une alerte calendrier au **2026-09-15** (30 jours avant
expiration). Deux options à ce moment :
1. Renouveler manuellement chez OVH (refaire la procédure
   actuelle)
2. **Recommandation** : migrer vers Let's Encrypt avec
   renouvellement automatique via certbot (une seule fois pour
   toutes)

═══════════════════════════════════════════════════════════════

## TD-004 — Configuration backups VPS

**Origine** : Pré-S1.5 (déploiement initial)
**Plan de résolution** : Sprint à définir (avant montée en charge
du trafic)
**Sévérité** : Haute (perte de données potentielle)

### Description
Aucun système de backup automatique configuré pour :
- Dossier `/var/www/dream-digital/` (code + storage)
- Base de données PostgreSQL `dreamdigital_db`
- Configuration Nginx + certs SSL

### Plan de résolution
À mettre en place avant la mise en production publique (fin
Sprint 1.5 ou Sprint 1) :
- Snapshots VPS via panel OVH (si supporté)
- `pg_dump` quotidien avec rotation 7 jours
- Rsync hors-VPS (vers un stockage externe)

═══════════════════════════════════════════════════════════════

## TD-005 — Configs CMS i18n EN identique au FR — RÉSOLU 2026-05-12

**Origine** : Sprint 1.5 Étape 2 (configs CMS-ready, commit `a8b2589`)
**Plan de résolution** : Sprint 1 (fondations multi-pays + i18n FR/EN)
**Sévérité** : Moyenne (impact : visiteurs EN reçoivent du texte FR)
**Résolu par** : commit de traduction i18n EN complète, 2026-05-12 (suite chantier post-Sprint 1.5 + Sprint correctif).

### Description
Dans les configs `config/dream-digital/*.php` créées en Étape 2
Sprint 1.5, plusieurs clés `'en'` contiennent du texte français
identique à la clé `'fr'` :

- `site.php` — `sub_headline.en`, `pitch.title.en`,
  `pitch.paragraphs[0-1].en`, `transition_cta.text.en`,
  `transition_cta.title.en`, `transition_cta.cta_*.en`
- `coverage.php` — `global.description.en`
- `services.php` — champs `'en'` aussi en français
- `industries.php` — idem

C'est une décision consciente PO pour livrer Sprint 1.5 vite. Les
vrais textes EN seront fournis et intégrés en Sprint 1 lors de
l'implémentation i18n.

Cas particulier : `site.tagline.fr` = `site.tagline.en` =
`"Voice. SMS. eSIM. And More."` est **intentionnel** (tagline EN
validé comme marque, ANALYZE Q11 Brand Kit v1.2).

### Impact si non-traité
Quand un Blade lit `config('dream-digital.site.sub_headline.en')`
côté visiteur EN, il sert du français. Acceptable temporairement
parce que :
1. Site sous Basic Auth (pas d'utilisateurs publics)
2. Sprint 1.5 n'expose pas encore de language switcher

Devient bloquant dès l'ouverture publique du site.

### Plan de résolution
Sprint 1 — Fondations multi-pays + i18n FR/EN :
- Traductions EN professionnelles fournies par PO
- Mise à jour des configs avec vrais textes EN
- Implémentation language switcher
- Tests de bascule FR ↔ EN
- Suppression de la convention NULL VALUE (cf. site.php
  docblock MED-5) une fois toutes les clés peuplées

Per paulrberg review finding MED-2
(`docs/REVIEW_sprint-1-5_2026-05-08.md`).

### Résolution effective 2026-05-12

Traductions EN appliquées :
- `site.php` : `sub_headline.en`, `pitch.title.en`, `pitch.paragraphs[].en`, `transition_cta.text.en` (tagline reste intentionnellement identique en FR/EN par décision PO marque)
- `services.php` : 6 services × `description.en` (tagline + cta_label étaient déjà OK)
- `industries.php` : 4 industries × `description.en`
- `coverage.php` : `global.description.en` + `global.countries_label.en`
- `site.meta.description_default` : conversion en array localisé fr/en (impact : meta description HTML correcte selon la locale, gestion array dans `commonMaster.blade.php` et `footer-front.blade.php`)
- Autres configs (home, footer, pages, trust-signals, partners) étaient déjà entièrement traduites

Convention NULL VALUE (docblock site.php) reste applicable tant que `legal_name`, `og_image`, `email_support`, `phone`, `social.*` sont à null (en attente input PO) — c'est un sujet distinct de TD-005 (qui couvrait spécifiquement les FR copiés dans EN).

Tests : `tests/Feature/I18nContentTest.php` — 6 tests vérifient que /en/* rend bien en anglais et que /fr/* reste en français.

═══════════════════════════════════════════════════════════════

## TD-006 — Cookie admin-primaryColor injection CSS via Helpers

**Origine** : Sneat default (héritage template, identifié en
review paulrberg 2026-05-08)
**Plan de résolution** : avant retrait Basic Auth (pré-production
publique)
**Sévérité** : Faible (self-XSS, exploitabilité limitée) mais
mauvaise pratique exposée

### Description
Dans `resources/views/layouts/commonMaster.blade.php:102`, le
helper `Helpers::generatePrimaryColorCSS()` injecte du CSS basé
sur le cookie `admin-primaryColor` via la syntaxe Blade `{!! !!}`
(sans échappement).

Si un attaquant set le cookie via JS console ou via une
vulnérabilité XSS ailleurs, il peut injecter du CSS arbitraire
(et potentiellement du JS via `expression()` IE-legacy, `:hover`,
`::before content`, etc.).

### Impact si non-traité
Faible parce que c'est self-XSS (utilisateur attaque son propre
cookie). Pas d'amplification multi-utilisateurs.

Mais : pattern dangereux qui peut empirer si une autre faille XSS
(par exemple via un input mal sanitisé) permet de set le cookie
côté victime — alors l'amplification devient réelle.

### Plan de résolution
Avant retrait Basic Auth en production publique :

1. **Validation côté Helper** : vérifier que le cookie
   `admin-primaryColor` matche `/^#[0-9a-fA-F]{6}$/` avant de
   l'utiliser. Rejeter sinon (fallback sur valeur par défaut).
2. **Alternative plus robuste** : remplacer la génération CSS
   dynamique par un set de classes CSS prédéfinies sélectionnées
   via un attribut `data-*` (whitelist au lieu d'interpolation
   string).
3. **Si possible** : retirer complètement la feature
   "primary color custom" héritée du Customizer Sneat (déjà
   désactivé en S2/S6) — elle n'est plus exposée à l'utilisateur
   final.

Per paulrberg review open question 1
(`docs/REVIEW_sprint-1-5_2026-05-08.md`).

═══════════════════════════════════════════════════════════════

## Résolutions historiques

(à remplir au fur et à mesure que des dettes sont réglées)
