<?php

namespace Tests\Feature;

use App\Helpers\PriceFormatter;
use App\Models\Country;
use App\Models\Service;
use App\Models\ServicePrice;
use Database\Seeders\CountrySeeder;
use Database\Seeders\ServicePriceSeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Sprint1FoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            CountrySeeder::class,
            ServiceSeeder::class,
            ServicePriceSeeder::class,
        ]);
    }

    public function test_seeders_create_country_and_service_foundations(): void
    {
        $this->assertSame(4, Country::count());
        $this->assertSame(6, Service::count());
        $this->assertGreaterThanOrEqual(1, ServicePrice::count());
    }

    public function test_country_routes_bind_locale_and_country(): void
    {
        $this->get('/cd/fr')
            ->assertOk()
            ->assertSee('Country: Republique Democratique du Congo')
            ->assertSee('Locale: fr')
            ->assertSee('Country: CD');

        $this->get('/ci/en')
            ->assertOk()
            ->assertSee('Country: Ivory Coast')
            ->assertSee('Locale: en');
    }

    public function test_geo_detection_respects_country_cookie(): void
    {
        $this->withCookie('dd_country_pref', 'cd')
            ->get('/')
            ->assertRedirect('/cd/fr');
    }

    public function test_reset_country_redirects_to_global_and_clears_cookie(): void
    {
        $response = $this->get('/_reset-country?locale=en');

        $response->assertRedirect('/en');
        $response->assertCookieExpired('dd_country_pref');
    }

    public function test_price_formatter_uses_dual_currency_for_drc(): void
    {
        $country = Country::where('code', 'cd')->firstOrFail();
        $price = ServicePrice::whereHas('service', fn ($query) => $query->where('slug', 'sms'))->firstOrFail();

        $formatted = PriceFormatter::display($price, $country);

        $this->assertStringContainsString('$0.0089', $formatted);
        $this->assertStringContainsString('CDF', $formatted);
    }

    public function test_sprint_test_page_handles_empty_price_table(): void
    {
        ServicePrice::query()->delete();

        $this->get('/fr/test')
            ->assertOk()
            ->assertSee('Affichage adaptatif')
            ->assertSee('$0.0089');
    }
}
