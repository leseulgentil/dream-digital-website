<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\User;
use Database\Seeders\BlogContentSeeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\LegalPageSeeder;
use Database\Seeders\MarketingPageSeeder;
use Database\Seeders\ServicePriceSeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LaunchReadinessCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        putenv('DD_PUBLIC_INDEXABLE=false');
        putenv('LOG_LEVEL=debug');

        parent::tearDown();
    }

    public function test_launch_check_fails_when_business_fields_are_missing(): void
    {
        $this->seedLaunchData();

        $exitCode = Artisan::call('dd:launch-check');
        $output = Artisan::output();

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('company.legal_name', $output);
        $this->assertStringContainsString('contact.email_support', $output);
        $this->assertStringContainsString('contact.phone', $output);
        $this->assertStringContainsString('contact.whatsapp', $output);
    }

    public function test_testing_launch_check_allows_missing_phone_and_whatsapp_for_remote_qa(): void
    {
        $this->seedLaunchData();
        $this->seedCompanyProfiles();

        CompanyProfile::query()->update([
            'public_phone' => null,
            'whatsapp_number' => null,
        ]);

        $exitCode = Artisan::call('dd:launch-check', ['--testing' => true]);
        $output = Artisan::output();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Mode: remote testing', $output);
        $this->assertStringContainsString('Business field `dream-digital.site.contact.phone` is missing', $output);
        $this->assertStringContainsString('Business field `dream-digital.site.contact.whatsapp` is missing', $output);
    }

    public function test_public_launch_check_fails_when_operator_confirmations_are_missing(): void
    {
        $this->seedLaunchData();
        $this->seedCompanyProfiles(confirmOps: true);
        $this->setBusinessConfig();
        putenv('DD_PUBLIC_INDEXABLE=true');
        putenv('LOG_LEVEL=info');

        config([
            'app.debug' => false,
            'app.env' => 'production',
            'app.url' => 'https://dream-digital.info',
        ]);

        $exitCode = Artisan::call('dd:launch-check', ['--public' => true]);
        $output = Artisan::output();

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('Admin password rotation', $output);
        $this->assertStringContainsString('Legal pages validation', $output);
        $this->assertStringContainsString('Public Basic Auth removal', $output);
        $this->assertStringContainsString('VPS backups', $output);
        $this->assertStringContainsString('.env backup', $output);
        $this->assertStringContainsString('Deployment runbook review', $output);
    }

    public function test_public_launch_check_passes_when_required_data_and_flags_are_ready(): void
    {
        $this->seedLaunchData();
        $this->seedCompanyProfiles(confirmOps: true);
        $this->setBusinessConfig();
        putenv('DD_PUBLIC_INDEXABLE=true');

        config([
            'app.debug' => false,
            'app.env' => 'production',
            'app.url' => 'https://dream-digital.info',
            'session.secure' => true,
            'session.http_only' => true,
            'session.encrypt' => true,
            'session.same_site' => 'lax',
            'logging.channels.single.level' => 'info',
            'dream-digital.launch.admin_password_rotated' => true,
            'dream-digital.launch.legal_validated' => true,
            'dream-digital.launch.public_basic_auth_disabled' => true,
            'dream-digital.launch.backups_configured' => true,
            'dream-digital.launch.env_backed_up' => true,
            'dream-digital.launch.deployment_runbook_reviewed' => true,
            'dream-digital.launch.backups.path' => storage_path('framework/testing/backups'),
            'dream-digital.launch.backups.require_recent_database_backup' => true,
        ]);
        File::ensureDirectoryExists(storage_path('framework/testing/backups'));
        File::put(storage_path('framework/testing/backups/dream-digital-test.sql'), '-- test backup');

        $exitCode = Artisan::call('dd:launch-check', ['--public' => true]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Launch check OK', Artisan::output());
    }

    public function test_launch_check_reads_business_fields_from_company_profile(): void
    {
        $this->seedLaunchData();
        $this->seedCompanyProfiles();

        $exitCode = Artisan::call('dd:launch-check');
        $output = Artisan::output();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Business field `dream-digital.site.company.legal_name` is filled', $output);
        $this->assertStringContainsString('Admin password rotation has been confirmed', $output);
        $this->assertStringContainsString('Legal validation has been confirmed', $output);
    }

    public function test_backup_command_copies_sqlite_database_file(): void
    {
        $source = storage_path('framework/testing/source.sqlite');
        $target = storage_path('framework/testing/backups-command');

        File::ensureDirectoryExists(dirname($source));
        File::ensureDirectoryExists($target);
        File::put($source, 'sqlite content');

        config([
            'database.connections.backup_test' => [
                'driver' => 'sqlite',
                'database' => $source,
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
        ]);

        $exitCode = Artisan::call('dd:backup-db', [
            '--connection' => 'backup_test',
            '--path' => $target,
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Database backup created:', Artisan::output());
        $this->assertNotEmpty(File::glob($target . DIRECTORY_SEPARATOR . '*.sqlite'));
    }

    public function test_backup_command_supports_pgsql_driver_validation(): void
    {
        config([
            'database.connections.pgsql_missing_database' => [
                'driver' => 'pgsql',
                'host' => '127.0.0.1',
                'port' => '5432',
                'database' => '',
                'username' => 'dreamdigital',
                'password' => '',
                'charset' => 'utf8',
                'prefix' => '',
                'schema' => 'public',
                'sslmode' => 'prefer',
            ],
        ]);

        $exitCode = Artisan::call('dd:backup-db', [
            '--connection' => 'pgsql_missing_database',
            '--path' => storage_path('framework/testing/backups-command'),
        ]);

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('PostgreSQL backup requires DB_DATABASE', Artisan::output());
    }

    private function seedLaunchData(): void
    {
        $this->seed([
            CountrySeeder::class,
            ServiceSeeder::class,
            ServicePriceSeeder::class,
            LegalPageSeeder::class,
            MarketingPageSeeder::class,
            BlogContentSeeder::class,
        ]);

        User::factory()->create(['email' => 'admin@dream-digital.info']);
    }

    private function setBusinessConfig(): void
    {
        config([
            'dream-digital.site.company.legal_name' => 'Dream Digital SARL',
            'dream-digital.site.company.geo.latitude' => '-4.3250',
            'dream-digital.site.company.geo.longitude' => '15.3222',
            'dream-digital.site.company.entities' => [
                ['country_code' => 'cd', 'latitude' => '-4.3250', 'longitude' => '15.3222'],
                ['country_code' => 'ci', 'latitude' => '5.3599', 'longitude' => '-4.0083'],
                ['country_code' => 'cg', 'latitude' => '-4.2634', 'longitude' => '15.2429'],
            ],
            'dream-digital.site.contact.email_support' => 'support@dream-digital.info',
            'dream-digital.site.contact.phone' => '+243000000000',
            'dream-digital.site.contact.whatsapp' => '+243999999999',
            'dream-digital.site.social.linkedin' => 'https://www.linkedin.com/company/dream-digital',
            'dream-digital.site.social.twitter' => 'https://x.com/dreamdigital',
            'dream-digital.site.social.github' => 'https://github.com/dream-digital',
            'dream-digital.site.meta.og_image' => '/img/og/dream-digital-launch.png',
        ]);
    }

    private function seedCompanyProfiles(bool $confirmOps = false): void
    {
        foreach ([
            'cd' => ['city' => 'Kinshasa', 'country' => 'RDC', 'lat' => '-4.3250', 'lng' => '15.3222'],
            'ci' => ['city' => 'Abidjan', 'country' => 'Cote d Ivoire', 'lat' => '5.3599', 'lng' => '-4.0083'],
            'cg' => ['city' => 'Brazzaville', 'country' => 'Congo', 'lat' => '-4.2634', 'lng' => '15.2429'],
        ] as $countryCode => $entity) {
            CompanyProfile::create([
                'country_code' => $countryCode,
                'locale' => 'fr',
                'company_name' => 'Dream Digital',
                'legal_name' => 'DREAM DIGITAL',
                'public_phone' => $countryCode === 'cd' ? '+243000000000' : null,
                'whatsapp_number' => $countryCode === 'cd' ? '+243999999999' : null,
                'city' => $entity['city'],
                'country_label' => $entity['country'],
                'latitude' => $entity['lat'],
                'longitude' => $entity['lng'],
                'email_sales' => 'sales@dream-digital.info',
                'email_support' => 'support@dream-digital.info',
                'email_security' => 'security@dream-digital.info',
                'email_privacy' => 'privacy@dream-digital.info',
                'social_linkedin' => 'https://www.linkedin.com/company/dream-digital',
                'social_twitter' => 'https://x.com/dreamdigital',
                'social_github' => 'https://github.com/dream-digital',
                'og_image_path' => '/img/brand/logo-dd-horizontal.png',
                'legal_validated' => true,
                'admin_password_rotated' => true,
                'public_basic_auth_disabled' => $confirmOps,
                'backups_configured' => $confirmOps,
                'env_backed_up' => $confirmOps,
                'deployment_runbook_reviewed' => $confirmOps,
            ]);
        }
    }
}
