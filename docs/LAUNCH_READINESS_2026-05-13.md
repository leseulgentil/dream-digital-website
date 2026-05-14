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
LOG_LEVEL=info
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
DD_CSP_ENABLED=true
DD_CSP_REPORT_ONLY=true

DD_ADMIN_EMAIL=admin@dream-digital.info
DD_ADMIN_NAME="Admin Dream Digital"
DD_ADMIN_PASSWORD="mot-de-passe-temporaire-puis-rotation"

DD_SALES_EMAIL=sales@dream-digital.info
DD_SUPPORT_EMAIL=support@dream-digital.info
DD_SECURITY_EMAIL=security@dream-digital.info
DD_PRIVACY_EMAIL=privacy@dream-digital.info
DD_PUBLIC_PHONE="+243..."
DD_COMPANY_LEGAL_NAME="DREAM DIGITAL"
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

Depuis le sprint admin, `DD_PUBLIC_PHONE`, `DD_COMPANY_LEGAL_NAME`, les URLs sociales, l'image OG et les confirmations `DD_ADMIN_PASSWORD_ROTATED` / `DD_LEGAL_VALIDATED` peuvent aussi etre geres depuis `/admin/company-profile` en FR et EN. Les variables `.env` restent utiles comme fallback ou seed initial.

## Garde-fou

Ne pas activer `DD_PUBLIC_INDEXABLE=true` tant que :

- les mentions legales, CGU et RGPD FR/EN n'ont pas ete validees ;
- le mot de passe admin initial n'a pas ete change ;
- la protection Basic Auth publique a ete retiree volontairement ;
- les backups VPS sont confirmes et `php artisan dd:backup-db` a produit un dump recent ;
- `GET /healthz`, `GET /readyz` et `/.well-known/security.txt` repondent en 200 ;
- `npm run audit:prod` passe sans vulnerabilite production connue ;
- le test `php artisan dd:launch-check --public` sort `Launch check OK`.
