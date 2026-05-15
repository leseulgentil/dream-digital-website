# Preprod and launch ABCD - 2026-05-14

## A. Preprod reelle

1. Creer une base MySQL/MariaDB dediee et importer les migrations.
2. Copier `.env.example` vers `.env`, renseigner les secrets reels, puis executer `php artisan key:generate`.
3. Installer Nginx avec `deploy/nginx/dream-digital.conf` et adapter `server_name`, `root`, PHP-FPM et SSL.
4. Installer Supervisor avec `deploy/supervisor/dream-digital-worker.conf` si `QUEUE_CONNECTION=database`.
5. Lancer un dry-run GitHub Actions `Deploy production` avec `dry_run=true` et `deploy_mode=testing` pour la QA distante.
6. Verifier `GET /healthz`, `GET /readyz`, `/robots.txt`, `/sitemap.xml` et une connexion `/admin`.
7. Apres deploiement, vider/reconstruire les caches Laravel pour exposer les nouvelles routes admin: `php artisan optimize:clear`, puis `php artisan route:cache` et `php artisan config:cache`.

Pour pousser une version testable sur le VPS sans ouvrir publiquement le site, utiliser `DD_DEPLOY_MODE=testing bash scripts/deploy-production.sh`. Ce mode execute `php artisan dd:launch-check --testing`, qui tolere temporairement telephone public et WhatsApp manquants dans le CMS. Avant l'ouverture reelle, repasser en `DD_DEPLOY_MODE=public` et obtenir `php artisan dd:launch-check --public` en vert.

## B. Company Profile admin

Ces valeurs sont pilotees depuis `/admin/company-profile`, par entite pays (`CD`, `CI`, `CG`) et par langue (`FR`, `EN`). Ne pas les stocker dans `.env`.

Avant `DD_PUBLIC_INDEXABLE=true`, verifier dans l'admin que les entites `CD`, `CI` et `CG` contiennent chacune les deux profils `FR` et `EN` avec raison sociale, telephone public, numero WhatsApp, adresse publique, coordonnees GPS, identifiants legaux utiles, horaires support, emails support/security/privacy, image OpenGraph par defaut et confirmations legal/admin.

Si `/admin/company-profile` repond 404 apres deploiement, verifier d'abord que le serveur execute bien la derniere revision et que le cache route a ete reconstruit: `php artisan route:list --path=admin/company-profile`.

## C. Performance bundle

`npm run build` compile maintenant le bundle de production utile : front public, auth, admin Dream Digital et CMS.

Pour reconstruire les pages de demonstration internes heritees du template :

```bash
npm run build:full
```

Pour ne compiler que la vitrine publique :

```bash
npm run build:public
```

## D. Observabilite et securite

- `X-Request-Id` est ajoute a chaque reponse et injecte dans le contexte de logs Laravel.
- Les requetes lentes au-dessus de `DD_SLOW_REQUEST_MS` sont journalisees.
- CSP progressive activee en report-only par defaut via `DD_CSP_REPORT_ONLY=true`.
- `/.well-known/security.txt` expose le contact securite.
- `npm run audit:prod` verifie Composer et les dependances npm de production.
