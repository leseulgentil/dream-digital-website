<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CountrySeeder;
use Database\Seeders\LegalPageSeeder;
use Database\Seeders\MarketingPageSeeder;
use Database\Seeders\ServicePriceSeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LaunchReadinessCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        putenv('DD_PUBLIC_INDEXABLE=false');

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
    }

    public function test_public_launch_check_fails_when_operator_confirmations_are_missing(): void
    {
        $this->seedLaunchData();
        $this->setBusinessConfig();
        putenv('DD_PUBLIC_INDEXABLE=true');

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
    }

    public function test_public_launch_check_passes_when_required_data_and_flags_are_ready(): void
    {
        $this->seedLaunchData();
        $this->setBusinessConfig();
        putenv('DD_PUBLIC_INDEXABLE=true');

        config([
            'app.debug' => false,
            'app.env' => 'production',
            'app.url' => 'https://dream-digital.info',
            'dream-digital.launch.admin_password_rotated' => true,
            'dream-digital.launch.legal_validated' => true,
            'dream-digital.launch.public_basic_auth_disabled' => true,
            'dream-digital.launch.backups_configured' => true,
        ]);

        $exitCode = Artisan::call('dd:launch-check', ['--public' => true]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Launch check OK', Artisan::output());
    }

    private function seedLaunchData(): void
    {
        $this->seed([
            CountrySeeder::class,
            ServiceSeeder::class,
            ServicePriceSeeder::class,
            LegalPageSeeder::class,
            MarketingPageSeeder::class,
        ]);

        User::factory()->create(['email' => 'admin@dream-digital.info']);
    }

    private function setBusinessConfig(): void
    {
        config([
            'dream-digital.site.company.legal_name' => 'Dream Digital SARL',
            'dream-digital.site.contact.email_support' => 'support@dream-digital.info',
            'dream-digital.site.contact.phone' => '+243000000000',
            'dream-digital.site.social.linkedin' => 'https://www.linkedin.com/company/dream-digital',
            'dream-digital.site.social.twitter' => 'https://x.com/dreamdigital',
            'dream-digital.site.social.github' => 'https://github.com/dream-digital',
            'dream-digital.site.meta.og_image' => '/img/og/dream-digital-launch.png',
        ]);
    }
}
