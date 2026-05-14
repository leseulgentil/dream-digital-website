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
php artisan dd:backup-db
php artisan dd:launch-check --public
npm run audit:prod
```

Le check public doit rester rouge tant que les confirmations operateur ne sont pas posees. Les champs business et les confirmations `legal/admin password` sont pilotables depuis `/admin/company-profile`; les variables `.env` correspondantes restent des fallbacks/valeurs de seed.

## 2. Flags de readiness

Basculer ces flags uniquement apres verification reelle:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dream-digital.info
DD_PUBLIC_INDEXABLE=true
LOG_LEVEL=info
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
DD_CSP_ENABLED=true
DD_CSP_REPORT_ONLY=true
DD_PUBLIC_BASIC_AUTH_DISABLED=true
DD_BACKUPS_CONFIGURED=true
DD_ENV_BACKED_UP=true
DD_DEPLOYMENT_RUNBOOK_REVIEWED=true
```

Completer aussi `/admin/company-profile` pour les profils `FR` et `EN` :

- raison sociale (`DREAM DIGITAL` si valide juridiquement);
- telephone public;
- emails sales/support/security/privacy;
- URLs sociales;
- image OpenGraph par defaut;
- confirmations `Legal valide` et `Admin password rotate`.

## 3. Backups

Minimum attendu avant ouverture:

- dump SQL horodate avant chaque deploy;
- sauvegarde chiffree du `.env` production;
- snapshot VPS ou backup fournisseur active;
- procedure de restauration testee sur un environnement local/staging.

Commande projet:

```bash
php artisan dd:backup-db
```

Cette commande ecrit un dump horodate dans `storage/app/private/backups/database`
par defaut. Pour MySQL/MariaDB, elle utilise `mysqldump`; pour SQLite local,
elle copie le fichier de base. Le check public exige un backup recent
(`DD_DB_BACKUP_MAX_AGE_HOURS=24` par defaut).

Commandes indicatives si backup manuel necessaire:

```bash
php artisan down --render="errors::503"
mysqldump --single-transaction --quick --routines --triggers DB_NAME > backups/dream-digital-YYYYMMDD-HHMM.sql
php artisan up
```

## 4. Admin

Avant ouverture:

- creer au moins un compte owner/admin actif;
- remplacer tout mot de passe provisoire;
- completer `/admin/company-profile` pour FR et EN;
- activer les cookies de session securises (`SESSION_ENCRYPT`, `SESSION_SECURE_COOKIE`, `SESSION_HTTP_ONLY`);
- verifier les roles owner/admin/editor;
- confirmer que les menus admin inutiles restent caches ou non relies aux workflows publics;
- verifier les headers de securite dans le navigateur.

Les pages `/admin*` renvoient `Cache-Control: no-store` pour eviter le cache
navigateur/proxy des surfaces authentifiees.

## 5. Healthcheck

Endpoints publics minimaux pour reverse proxy ou monitoring:

```text
GET /healthz
GET /readyz
```

`/healthz` renvoie `{"status":"ok"}` si Laravel repond et si la connexion DB
accepte `select 1`, sinon `503`. `/readyz` verifie aussi que la table
`migrations` existe et qu'aucune migration disque n'est en attente.

Chaque reponse porte `X-Request-Id`; les requetes lentes au-dessus de
`DD_SLOW_REQUEST_MS` sont loggees avec cet identifiant.

## 6. CMS et IA

Le generateur d articles reste configurable:

```dotenv
DD_ARTICLE_GENERATOR_PROVIDER=openai
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-5-mini
```

Sans cle API, le CMS retombe sur le generateur local deterministic pour ne pas bloquer les editeurs.

## 7. QA navigateur

Smoke test local:

```bash
php artisan serve --host=127.0.0.1 --port=8899
npx playwright test tests/Browser/dream-digital-qa.spec.js --browser=chromium --workers=1
```

Ce test couvre le menu public desktop/mobile, l admin navigation, le modal Generate Article et le WYSIWYG en vrais clics.

## 8. Deploy script

Un script Linux de reference existe dans `scripts/deploy-production.sh`.
Il met le site en maintenance, cree un backup DB, pull `master`, installe les
dependances, build les assets, migre, seed, cache Laravel, lance le readiness
public puis remet le site en ligne.

Le workflow GitHub Actions `Deploy production` permet un dry-run SSH manuel
avant de lancer ce script. Secrets attendus : `DD_PROD_HOST`, `DD_PROD_USER`,
`DD_PROD_SSH_KEY`, `DD_PROD_PORT`, `DD_PROD_APP_DIR`.

## 9. Bundle production

`npm run build` compile uniquement le perimetre deploye : front public,
auth, admin Dream Digital et CMS. Les anciennes surfaces de demonstration
restent compilables via `npm run build:full` si necessaire en local/staging.
