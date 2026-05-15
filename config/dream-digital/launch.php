<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Public launch operator confirmations
    |--------------------------------------------------------------------------
    |
    | These flags document checks that cannot be inferred from Laravel code,
    | such as Nginx Basic Auth removal or legal validation. They are read by
    | `php artisan dd:launch-check --public` before opening the site.
    |
    */

    'admin_password_rotated' => env('DD_ADMIN_PASSWORD_ROTATED', false),
    'legal_validated' => env('DD_LEGAL_VALIDATED', false),
    'public_basic_auth_disabled' => env('DD_PUBLIC_BASIC_AUTH_DISABLED', false),
    'backups_configured' => env('DD_BACKUPS_CONFIGURED', false),
    'env_backed_up' => env('DD_ENV_BACKED_UP', false),
    'deployment_runbook_reviewed' => env('DD_DEPLOYMENT_RUNBOOK_REVIEWED', false),

    /*
    |--------------------------------------------------------------------------
    | Database backup guard
    |--------------------------------------------------------------------------
    |
    | `dd:backup-db` writes database dumps here before a deploy. The public
    | launch check requires a recent backup by default so migrations are never
    | pushed without an immediately restorable point.
    |
    */

    'backups' => [
        'path' => env('DD_DB_BACKUP_PATH') ?: storage_path('app/private/backups/database'),
        'mysqldump_binary' => env('DD_MYSQLDUMP_BINARY', 'mysqldump'),
        'pg_dump_binary' => env('DD_PG_DUMP_BINARY', 'pg_dump'),
        'max_age_hours' => (int) env('DD_DB_BACKUP_MAX_AGE_HOURS', 24),
        'require_recent_database_backup' => env('DD_REQUIRE_RECENT_DB_BACKUP', true),
    ],
];
