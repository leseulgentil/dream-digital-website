# Launch readiness Dream Digital - 2026-05-13

Ce document garde le bloc **Ouverture publique readiness** actionnable pour Codex, Claude Code et le PO.

## Commandes de base

```powershell
php artisan migrate --force
php artisan db:seed --force
php artisan db:seed --class=AdminUserSeeder --force
php artisan dd:backup-db
php artisan config:cache
php artisan route:cache
php artisan dd:launch-check
```

Pour l'ouverture publique stricte :

```powershell
php artisan dd:launch-check --public
```

## Variables a renseigner en production

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dream-digital.info
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

DD_ADMIN_EMAIL=admin@dream-digital.info
DD_ADMIN_NAME="Admin Dream Digital"
DD_ADMIN_PASSWORD="mot-de-passe-temporaire-puis-rotation"

DD_SALES_EMAIL=sales@dream-digital.info
DD_SUPPORT_EMAIL=support@dream-digital.info
DD_PUBLIC_PHONE="+243..."
DD_COMPANY_LEGAL_NAME="Dream Digital ..."
DD_OG_IMAGE=/img/og/dream-digital-launch.png
DD_SOCIAL_LINKEDIN=
DD_SOCIAL_TWITTER=
DD_SOCIAL_GITHUB=
```

## Flags PO/ops obligatoires avant `DD_PUBLIC_INDEXABLE=true`

Ces flags ne doivent passer a `true` qu'apres validation reelle :

```dotenv
DD_ADMIN_PASSWORD_ROTATED=true
DD_LEGAL_VALIDATED=true
DD_PUBLIC_BASIC_AUTH_DISABLED=true
DD_BACKUPS_CONFIGURED=true
DD_ENV_BACKED_UP=true
DD_DEPLOYMENT_RUNBOOK_REVIEWED=true
DD_PUBLIC_INDEXABLE=true

DD_DB_BACKUP_MAX_AGE_HOURS=24
DD_REQUIRE_RECENT_DB_BACKUP=true
```

## Garde-fou

Ne pas activer `DD_PUBLIC_INDEXABLE=true` tant que :

- les mentions legales, CGU et RGPD FR/EN n'ont pas ete validees ;
- le mot de passe admin initial n'a pas ete change ;
- la protection Basic Auth publique a ete retiree volontairement ;
- les backups VPS sont confirmes et `php artisan dd:backup-db` a produit un dump recent ;
- le test `php artisan dd:launch-check --public` sort `Launch check OK`.
