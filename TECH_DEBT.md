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

## Résolutions historiques

(à remplir au fur et à mesure que des dettes sont réglées)
