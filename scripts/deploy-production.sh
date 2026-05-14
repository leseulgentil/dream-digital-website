#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/dream-digital-website}"
BRANCH="${BRANCH:-master}"

cd "$APP_DIR"

php artisan down --retry=60 || true
restore_app() {
  php artisan up || true
}
trap restore_app EXIT

php artisan dd:backup-db

git fetch origin "$BRANCH"
git checkout "$BRANCH"
git pull --ff-only origin "$BRANCH"

composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
npm ci
npm run build:public

php artisan migrate --force
php artisan db:seed --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan queue:restart || true

php artisan dd:launch-check --public
trap - EXIT
php artisan up
