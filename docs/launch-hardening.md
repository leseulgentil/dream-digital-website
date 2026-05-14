# Dream Digital Launch Hardening

Ce fichier sert de runbook partage pour Codex, Claude Code et les devs humains avant ouverture publique.

## 1. Checks bloquants

Avant mise en ligne:

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan test
npm run build
npm run build:public
php artisan dd:launch-check --public
```

Le check public doit rester rouge tant que les confirmations operateur ne sont pas posees dans `.env`.

## 2. Flags de readiness

Basculer ces flags uniquement apres verification reelle:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dream-digital.info
DD_PUBLIC_INDEXABLE=true
DD_ADMIN_PASSWORD_ROTATED=true
DD_LEGAL_VALIDATED=true
DD_PUBLIC_BASIC_AUTH_DISABLED=true
DD_BACKUPS_CONFIGURED=true
DD_ENV_BACKED_UP=true
DD_DEPLOYMENT_RUNBOOK_REVIEWED=true
```

## 3. Backups

Minimum attendu avant ouverture:

- dump SQL horodate avant chaque deploy;
- sauvegarde chiffree du `.env` production;
- snapshot VPS ou backup fournisseur active;
- procedure de restauration testee sur un environnement local/staging.

Commandes indicatives:

```bash
php artisan down --render="errors::503"
mysqldump --single-transaction --quick --routines --triggers DB_NAME > backups/dream-digital-YYYYMMDD-HHMM.sql
php artisan up
```

## 4. Admin

Avant ouverture:

- creer au moins un compte owner/admin actif;
- remplacer tout mot de passe provisoire;
- verifier les roles owner/admin/editor;
- confirmer que les menus admin inutiles restent caches ou non relies aux workflows publics;
- verifier les headers de securite dans le navigateur.

## 5. CMS et IA

Le generateur d articles reste configurable:

```dotenv
DD_ARTICLE_GENERATOR_PROVIDER=openai
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-5-mini
```

Sans cle API, le CMS retombe sur le generateur local deterministic pour ne pas bloquer les editeurs.

## 6. QA navigateur

Smoke test local:

```bash
php artisan serve --host=127.0.0.1 --port=8899
npx playwright test tests/Browser/dream-digital-qa.spec.js --browser=chromium --workers=1
```

Ce test couvre le menu public desktop/mobile, l admin navigation, le modal Generate Article et le WYSIWYG en vrais clics.
