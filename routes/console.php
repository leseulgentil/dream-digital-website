<?php

use App\Models\Country;
use App\Models\Page;
use App\Models\Service;
use App\Models\ServicePrice;
use App\Models\User;
use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('dd:launch-check {--public : Require conditions for public opening}', function () {
    /** @var ClosureCommand $this */
    $public = (bool) $this->option('public');
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

    $bool = fn (string $key): bool => filter_var(config("dream-digital.launch.{$key}", false), FILTER_VALIDATE_BOOLEAN);

    $this->line('Dream Digital launch readiness check');
    $this->line($public ? 'Mode: public opening' : 'Mode: pre-launch');

    $record(Schema::hasTable('countries'), 'Database table `countries` exists');
    $record(Schema::hasTable('services'), 'Database table `services` exists');
    $record(Schema::hasTable('service_prices'), 'Database table `service_prices` exists');
    $record(Schema::hasTable('pages'), 'Database table `pages` exists');
    $record(Schema::hasTable('users'), 'Database table `users` exists');

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
    }

    if (Schema::hasTable('users')) {
        $record(User::query()->exists(), 'At least one admin user exists');
    }

    foreach ([
        'company.legal_name',
        'contact.email_support',
        'contact.phone',
    ] as $key) {
        $record(
            filled(data_get(config('dream-digital.site'), $key)),
            "Business field `dream-digital.site.{$key}` is filled",
            true,
            "Business field `dream-digital.site.{$key}` is missing"
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

    if ($public) {
        $indexable = filter_var(env('DD_PUBLIC_INDEXABLE', false), FILTER_VALIDATE_BOOLEAN);

        $record(config('app.env') === 'production', 'APP_ENV is production', true, 'APP_ENV is not production');
        $record(config('app.debug') === false, 'APP_DEBUG is false', true, 'APP_DEBUG is not false');
        $record(str_starts_with((string) config('app.url'), 'https://'), 'APP_URL uses HTTPS', true, 'APP_URL does not use HTTPS');
        $record($indexable, 'DD_PUBLIC_INDEXABLE is true', true, 'DD_PUBLIC_INDEXABLE is not true');
        $record($bool('admin_password_rotated'), 'Admin password rotation has been confirmed', true, 'Admin password rotation is not confirmed');
        $record($bool('legal_validated'), 'Legal pages validation has been confirmed', true, 'Legal pages validation is not confirmed');
        $record($bool('public_basic_auth_disabled'), 'Public Basic Auth removal has been confirmed', true, 'Public Basic Auth removal is not confirmed');
        $record($bool('backups_configured'), 'VPS backups have been confirmed', true, 'VPS backups are not confirmed');
    } else {
        $record($bool('admin_password_rotated'), 'Admin password rotation has been confirmed', false, 'Admin password rotation confirmation is pending');
        $record($bool('legal_validated'), 'Legal validation has been confirmed', false, 'Legal validation confirmation is pending');
        $record($bool('public_basic_auth_disabled'), 'Public Basic Auth removal has been confirmed', false, 'Public Basic Auth removal confirmation is pending');
        $record($bool('backups_configured'), 'VPS backups have been confirmed', false, 'VPS backup confirmation is pending');
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
