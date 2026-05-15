<?php

use App\Models\Country;
use App\Models\CompanyProfile;
use App\Models\AiKnowledgeWebSource;
use App\Models\Page;
use App\Models\Service;
use App\Models\ServicePrice;
use App\Models\User;
use App\Services\Ai\AiWebKnowledgeImporter;
use App\Services\CompanyProfileService;
use App\Support\DatabaseBackup;
use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('dd:launch-check {--public : Require conditions for public opening} {--testing : Allow remote testing before all public contact data is filled}', function () {
    /** @var ClosureCommand $this */
    $public = (bool) $this->option('public');
    $testing = ! $public && (bool) $this->option('testing');
    $failures = [];
    $warnings = [];
    $passes = [];

    $record = function (bool $ok, string $message, bool $blocking = true, ?string $failedMessage = null) use (&$failures, &$warnings, &$passes): void {
        if ($ok) {
            $passes[] = $message;
            return;
        }

        $message = $failedMessage ?? $message;

        if ($blocking) {
            $failures[] = $message;
            return;
        }

        $warnings[] = $message;
    };

    app(CompanyProfileService::class)->applyToConfig('fr');

    $bool = fn (string $key): bool => filter_var(config("dream-digital.launch.{$key}", false), FILTER_VALIDATE_BOOLEAN);
    $configBool = fn (string $key): bool => filter_var(config($key, false), FILTER_VALIDATE_BOOLEAN);
    $pendingMigrationNames = function (): array {
        $migrationFiles = collect(glob(database_path('migrations/*.php')) ?: [])
            ->map(fn (string $path) => basename($path, '.php'))
            ->values();

        $ran = Schema::hasTable('migrations')
            ? DB::table('migrations')->pluck('migration')
            : collect();

        return $migrationFiles->diff($ran)->values()->all();
    };
    $unknownMigrationNames = function (): array {
        if (! Schema::hasTable('migrations')) {
            return [];
        }

        $migrationFiles = collect(glob(database_path('migrations/*.php')) ?: [])
            ->map(fn (string $path) => basename($path, '.php'))
            ->values();

        return DB::table('migrations')
            ->pluck('migration')
            ->diff($migrationFiles)
            ->values()
            ->all();
    };
    $duplicateMigrationNames = function (): array {
        if (! Schema::hasTable('migrations')) {
            return [];
        }

        return DB::table('migrations')
            ->select('migration')
            ->groupBy('migration')
            ->havingRaw('count(*) > 1')
            ->pluck('migration')
            ->values()
            ->all();
    };
    $recentBackupPath = function (): ?string {
        $backupDir = (string) config('dream-digital.launch.backups.path');
        $maxAgeHours = max(1, (int) config('dream-digital.launch.backups.max_age_hours', 24));

        if (! is_dir($backupDir)) {
            return null;
        }

        $cutoff = now()->subHours($maxAgeHours)->getTimestamp();
        $patterns = ['*.sql', '*.sql.gz', '*.sqlite', '*.sqlite.gz'];

        return collect($patterns)
            ->flatMap(fn (string $pattern) => glob($backupDir . DIRECTORY_SEPARATOR . $pattern) ?: [])
            ->filter(fn (string $path) => is_file($path) && filemtime($path) >= $cutoff && filesize($path) > 0)
            ->sortByDesc(fn (string $path) => filemtime($path))
            ->first();
    };

    $this->line('Dream Digital launch readiness check');
    $this->line($public ? 'Mode: public opening' : ($testing ? 'Mode: remote testing' : 'Mode: pre-launch'));

    try {
        DB::select('select 1');
        $record(true, 'Database connection responds to SELECT 1');
    } catch (Throwable $exception) {
        $record(false, 'Database connection responds to SELECT 1', true, 'Database connection failed: ' . $exception->getMessage());
    }

    $record(Schema::hasTable('migrations'), 'Migration repository exists');
    $record(Schema::hasTable('countries'), 'Database table `countries` exists');
    $record(Schema::hasTable('services'), 'Database table `services` exists');
    $record(Schema::hasTable('service_prices'), 'Database table `service_prices` exists');
    $record(Schema::hasTable('pages'), 'Database table `pages` exists');
    $record(Schema::hasTable('company_profiles'), 'Database table `company_profiles` exists');
    $record(Schema::hasTable('users'), 'Database table `users` exists');
    $record(Schema::hasTable('cache'), 'Database table `cache` exists');
    $record(Schema::hasTable('sessions'), 'Database table `sessions` exists');

    if (config('queue.default') === 'database') {
        $record(Schema::hasTable('jobs'), 'Database table `jobs` exists for database queue');
    }

    $pendingMigrations = $pendingMigrationNames();
    $pendingSummary = implode(', ', array_slice($pendingMigrations, 0, 5));
    if (count($pendingMigrations) > 5) {
        $pendingSummary .= ' ... +' . (count($pendingMigrations) - 5) . ' more';
    }
    $record(
        $pendingMigrations === [],
        'No pending migrations',
        $public,
        'Pending migrations: ' . ($pendingSummary ?: 'migration repository missing')
    );

    $unknownMigrations = $unknownMigrationNames();
    $record(
        $unknownMigrations === [],
        'Migration repository only references files present on disk',
        $public,
        'Migration table has records missing from disk: ' . (implode(', ', array_slice($unknownMigrations, 0, 5)) ?: 'unknown')
    );

    $duplicateMigrations = $duplicateMigrationNames();
    $record(
        $duplicateMigrations === [],
        'Migration repository has no duplicate migration rows',
        $public,
        'Migration table has duplicate rows: ' . (implode(', ', array_slice($duplicateMigrations, 0, 5)) ?: 'unknown')
    );

    if (Schema::hasTable('countries')) {
        $record(Country::active()->count() >= 4, 'At least 4 active countries are seeded');
    }

    if (Schema::hasTable('services')) {
        $record(Service::active()->count() >= 6, 'At least 6 active services are seeded');
    }

    if (Schema::hasTable('service_prices')) {
        $record(ServicePrice::published()->count() >= 5, 'At least 5 published service prices are seeded');
    }

    if (Schema::hasTable('pages')) {
        $record(Page::published()->where('section', 'legal')->count() >= 6, '6 published legal pages exist');
        $record(Page::published()->where('section', 'marketing')->count() >= 14, '14 published marketing pages exist');
        $record(Page::published()->where('section', 'blog')->where('locale', 'fr')->count() >= 10, '10 published FR blog articles exist');
        $record(Page::published()->where('section', 'blog')->where('locale', 'en')->count() >= 10, '10 published EN blog articles exist');
    }

    if (Schema::hasTable('users')) {
        $record(
            User::query()
                ->where('is_active', true)
                ->whereIn('role', [User::ROLE_OWNER, User::ROLE_ADMIN])
                ->exists(),
            'At least one active owner/admin user exists',
            true,
            'No active owner/admin user exists'
        );
    }

    foreach ([
        'company.legal_name' => true,
        'contact.email_support' => true,
        'contact.phone' => ! $testing,
        'contact.whatsapp' => ! $testing,
        'company.geo.latitude' => true,
        'company.geo.longitude' => true,
    ] as $key => $blocking) {
        $record(
            filled(data_get(config('dream-digital.site'), $key)),
            "Business field `dream-digital.site.{$key}` is filled",
            $blocking,
            "Business field `dream-digital.site.{$key}` is missing"
        );
    }

    if (
        Schema::hasTable('company_profiles')
        && Schema::hasColumn('company_profiles', 'country_code')
        && Schema::hasColumn('company_profiles', 'latitude')
        && Schema::hasColumn('company_profiles', 'longitude')
    ) {
        $entityProfilesWithGps = CompanyProfile::query()
            ->where('locale', 'fr')
            ->whereIn('country_code', array_keys(CompanyProfile::ENTITY_COUNTRIES))
            ->whereNotNull('latitude')
            ->where('latitude', '!=', '')
            ->whereNotNull('longitude')
            ->where('longitude', '!=', '')
            ->count();

        $record(
            $entityProfilesWithGps >= count(CompanyProfile::ENTITY_COUNTRIES),
            'GPS coordinates are configured for the 3 country entities',
            true,
            'GPS coordinates are missing for one or more country entities'
        );
    }

    foreach ([
        'social.linkedin',
        'social.twitter',
        'social.github',
    ] as $key) {
        $record(
            filled(data_get(config('dream-digital.site'), $key)),
            "Optional social field `dream-digital.site.{$key}` is filled",
            false,
            "Optional social field `dream-digital.site.{$key}` is missing"
        );
    }

    $ogImage = data_get(config('dream-digital.site'), 'meta.og_image');
    $record(
        filled($ogImage) && $ogImage !== '/img/og/dream-digital-default.png',
        'Dedicated OpenGraph image is configured',
        false,
        'Dedicated OpenGraph image still uses the default placeholder'
    );

    $record(app()->configurationIsCached(), 'Configuration is cached', false, 'Configuration is not cached');
    $record(app()->routesAreCached(), 'Routes are cached', false, 'Routes are not cached');
    $record(
        filter_var(config('dream-digital.security.csp.enabled', true), FILTER_VALIDATE_BOOLEAN),
        'Content Security Policy header is configured',
        false,
        'Content Security Policy header is disabled'
    );
    $record(
        filled(config('dream-digital.security.security_txt.contact')),
        'Security contact is configured',
        false,
        'Security contact is missing'
    );

    $backupRequired = filter_var(config('dream-digital.launch.backups.require_recent_database_backup', true), FILTER_VALIDATE_BOOLEAN);
    $latestBackup = $recentBackupPath();
    $record(
        $latestBackup !== null,
        'Recent database backup exists: ' . ($latestBackup ? basename($latestBackup) : ''),
        $public && $backupRequired,
        'No recent database backup found; run `php artisan dd:backup-db` before deploy'
    );

    if ($public) {
        $indexable = $bool('public_indexable');

        $record(config('app.env') === 'production', 'APP_ENV is production', true, 'APP_ENV is not production');
        $record(config('app.debug') === false, 'APP_DEBUG is false', true, 'APP_DEBUG is not false');
        $record(str_starts_with((string) config('app.url'), 'https://'), 'APP_URL uses HTTPS', true, 'APP_URL does not use HTTPS');
        $record($indexable, 'DD_PUBLIC_INDEXABLE is true', true, 'DD_PUBLIC_INDEXABLE is not true');
        $logLevel = strtolower((string) config('logging.channels.single.level', env('LOG_LEVEL', 'debug')));
        $record($logLevel !== 'debug', 'LOG_LEVEL is not debug', true, 'LOG_LEVEL must not be debug in production');
        $record($configBool('session.secure'), 'Session secure cookie is enabled', true, 'SESSION_SECURE_COOKIE is not true');
        $record($configBool('session.http_only'), 'Session HTTP-only cookie is enabled', true, 'SESSION_HTTP_ONLY is not true');
        $record($configBool('session.encrypt'), 'Session encryption is enabled', true, 'SESSION_ENCRYPT is not true');
        $record(
            in_array(config('session.same_site'), ['lax', 'strict'], true),
            'Session SameSite policy is lax or strict',
            true,
            'SESSION_SAME_SITE must be lax or strict'
        );
        $record($bool('admin_password_rotated'), 'Admin password rotation has been confirmed', true, 'Admin password rotation is not confirmed');
        $record($bool('legal_validated'), 'Legal pages validation has been confirmed', true, 'Legal pages validation is not confirmed');
        $record($bool('public_basic_auth_disabled'), 'Public Basic Auth removal has been confirmed', true, 'Public Basic Auth removal is not confirmed');
        $record($bool('backups_configured'), 'VPS backups have been confirmed', true, 'VPS backups are not confirmed');
        $record($bool('env_backed_up'), '.env backup has been confirmed', true, '.env backup is not confirmed');
        $record($bool('deployment_runbook_reviewed'), 'Deployment runbook review has been confirmed', true, 'Deployment runbook review is not confirmed');
    } else {
        $record($bool('admin_password_rotated'), 'Admin password rotation has been confirmed', false, 'Admin password rotation confirmation is pending');
        $record($bool('legal_validated'), 'Legal validation has been confirmed', false, 'Legal validation confirmation is pending');
        $record($bool('public_basic_auth_disabled'), 'Public Basic Auth removal has been confirmed', false, 'Public Basic Auth removal confirmation is pending');
        $record($bool('backups_configured'), 'VPS backups have been confirmed', false, 'VPS backup confirmation is pending');
        $record($bool('env_backed_up'), '.env backup has been confirmed', false, '.env backup confirmation is pending');
        $record($bool('deployment_runbook_reviewed'), 'Deployment runbook review has been confirmed', false, 'Deployment runbook review confirmation is pending');
    }

    foreach ($passes as $message) {
        $this->line("  [OK] {$message}");
    }

    foreach ($warnings as $message) {
        $this->warn("  [WARN] {$message}");
    }

    foreach ($failures as $message) {
        $this->error("  [FAIL] {$message}");
    }

    if ($failures !== []) {
        $this->newLine();
        $this->error('Launch check failed. Fix the failures above before opening publicly.');
        return 1;
    }

    $this->newLine();
    $this->info('Launch check OK.');
    return 0;
})->purpose('Check Dream Digital readiness before public opening');

Artisan::command('dd:backup-db {--connection= : Database connection name} {--path= : Target backup directory}', function () {
    /** @var ClosureCommand $this */
    $connection = $this->option('connection') ?: null;
    $path = $this->option('path') ?: null;

    try {
        $backupPath = app(DatabaseBackup::class)->create($connection, $path);
    } catch (Throwable $exception) {
        $this->error($exception->getMessage());

        return 1;
    }

    $this->info('Database backup created: ' . $backupPath);

    return 0;
})->purpose('Create a timestamped database backup before deploy');

Artisan::command('dd:sync-ai-web-sources', function (AiWebKnowledgeImporter $importer) {
    /** @var ClosureCommand $this */
    $syncedSources = 0;
    $createdChunks = 0;

    AiKnowledgeWebSource::query()
        ->due()
        ->each(function (AiKnowledgeWebSource $webSource) use ($importer, &$syncedSources, &$createdChunks): void {
            try {
                $createdChunks += $importer->sync($webSource);
                $syncedSources++;
            } catch (Throwable $exception) {
                $webSource->forceFill([
                    'last_error' => $exception->getMessage(),
                ])->save();

                $this->warn("Source {$webSource->id} failed: {$exception->getMessage()}");
            }
        });

    $this->info("Synced {$syncedSources} web source(s), {$createdChunks} segment(s) created or updated.");

    return 0;
})->purpose('Synchronize due AI knowledge web sources');

Schedule::command('dd:sync-ai-web-sources')->dailyAt('03:20');
