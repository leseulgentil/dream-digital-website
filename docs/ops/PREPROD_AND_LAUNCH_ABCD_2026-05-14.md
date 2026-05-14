# Preprod and launch ABCD - 2026-05-14

## A. Preprod reelle

1. Creer une base MySQL/MariaDB dediee et importer les migrations.
2. Copier `.env.example` vers `.env`, renseigner les secrets reels, puis executer `php artisan key:generate`.
3. Installer Nginx avec `deploy/nginx/dream-digital.conf` et adapter `server_name`, `root`, PHP-FPM et SSL.
4. Installer Supervisor avec `deploy/supervisor/dream-digital-worker.conf` si `QUEUE_CONNECTION=database`.
5. Lancer un dry-run GitHub Actions `Deploy production` avec `dry_run=true`.
6. Verifier `GET /healthz`, `GET /readyz`, `/robots.txt`, `/sitemap.xml` et une connexion `/admin`.

## B. Inputs PO requis

Ces valeurs restent a confirmer par le PO avant `DD_PUBLIC_INDEXABLE=true` :

```dotenv
DD_COMPANY_LEGAL_NAME=
DD_PUBLIC_PHONE=
DD_SOCIAL_LINKEDIN=
DD_SOCIAL_TWITTER=
DD_SOCIAL_GITHUB=
DD_LEGAL_VALIDATED=true
DD_ADMIN_PASSWORD_ROTATED=true
```

Les emails proposes par defaut sont `support@`, `security@` et `privacy@dream-digital.info`.

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
