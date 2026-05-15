#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/dream-digital-website}"
BRANCH="${BRANCH:-master}"
DD_DEPLOY_MODE="${DD_DEPLOY_MODE:-public}"

case "$DD_DEPLOY_MODE" in
  public)
    LAUNCH_CHECK_ARGS=(--public)
    ;;
  testing|staging|test)
    LAUNCH_CHECK_ARGS=(--testing)
    ;;
  pre-launch|prelaunch)
    LAUNCH_CHECK_ARGS=()
    ;;
  *)
    echo "Unknown DD_DEPLOY_MODE: $DD_DEPLOY_MODE" >&2
    echo "Expected one of: public, testing, staging, test, pre-launch" >&2
    exit 1
    ;;
esac

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
npm run build

php artisan migrate --force
php artisan db:seed --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan queue:restart || true

php artisan dd:launch-check "${LAUNCH_CHECK_ARGS[@]}"
trap - EXIT
php artisan up
