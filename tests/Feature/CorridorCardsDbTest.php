<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Service;
use App\Models\ServicePrice;
use Database\Seeders\CountrySeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\ServicePriceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CorridorCardsDbTest extends TestCase
{
    use RefreshDatabase;

    public function test_pricing_page_renders_corridor_cards_from_db(): void
    {
        $this->seed([CountrySeeder::class, ServiceSeeder::class, ServicePriceSeeder::class]);

        $response = $this->get('/fr/pricing');
        $response->assertOk();
        $response->assertSee('Route prioritaire'); // status_fr seedé
        $response->assertSee('Corridor suivi');
        $response->assertSee('Interconnexion active');
    }

    public function test_pricing_page_renders_localized_status_en(): void
    {
        $this->seed([CountrySeeder::class, ServiceSeeder::class, ServicePriceSeeder::class]);

        $response = $this->get('/en/pricing');
        $response->assertOk();
        $response->assertSee('Priority route');
        $response->assertSee('Monitored corridor');
        $response->assertSee('Active interconnect');
    }

    public function test_coverage_page_renders_corridor_cards_from_db(): void
    {
        $this->seed([CountrySeeder::class, ServiceSeeder::class, ServicePriceSeeder::class]);

        $response = $this->get('/fr/coverage');
        $response->assertOk();
        $response->assertSee('Route prioritaire');
    }

    public function test_corridors_quality_renders_correct_stars(): void
    {
        $this->seed([CountrySeeder::class, ServiceSeeder::class]);

        $sms = Service::firstOrFail();
        $country = Country::firstOrFail();
        ServicePrice::create([
            'service_id' => $sms->id,
            'country_id' => $country->id,
            'destination_country' => 'FR',
            'label_fr' => 'Test corridor FR',
            'label_en' => 'Test corridor EN',
            'price_usd' => 0.005,
            'unit' => 'sms',
            'quality' => 5,
            'status_fr' => 'TestStatusFR',
            'status_en' => 'TestStatusEN',
            'is_published' => true,
        ]);

        $this->get('/fr/pricing')
            ->assertOk()
            ->assertSee('TestStatusFR')
            ->assertSee('Route quality 5 sur 5', false);
    }

    public function test_unpublished_service_price_not_rendered_as_corridor(): void
    {
        $this->seed([CountrySeeder::class, ServiceSeeder::class]);

        $sms = Service::firstOrFail();
        $country = Country::firstOrFail();
        ServicePrice::create([
            'service_id' => $sms->id,
            'country_id' => $country->id,
            'destination_country' => 'FR',
            'label_fr' => 'Draft corridor',
            'label_en' => 'Draft corridor EN',
            'price_usd' => 0.005,
            'unit' => 'sms',
            'quality' => 5,
            'status_fr' => 'DRAFT-CORRIDOR-FR',
            'status_en' => 'DRAFT-CORRIDOR-EN',
            'is_published' => false,
        ]);

        $this->get('/fr/pricing')
            ->assertOk()
            ->assertDontSee('DRAFT-CORRIDOR-FR');
    }

    public function test_corridors_fallback_to_config_when_no_db_published(): void
    {
        $this->seed([CountrySeeder::class, ServiceSeeder::class]);

        // Aucune ServicePrice avec destination_country = fallback config
        $this->assertSame(0, ServicePrice::published()->whereNotNull('destination_country')->count());

        $this->get('/fr/pricing')
            ->assertOk()
            ->assertSee('France vers RDC'); // config corridor title
    }
}
