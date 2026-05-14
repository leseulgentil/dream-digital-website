<?php

namespace Tests\Feature;

use Database\Seeders\CountrySeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StructuredDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_json_ld_present_on_localized_pages(): void
    {
        $response = $this->get('/fr');
        $response->assertOk();
        $response->assertSee('"@type":"Organization"', false);
        $response->assertSee('"name":"Dream Digital"', false);
        $response->assertSee('"email":"sales@dream-digital.info"', false);
    }

    public function test_organization_json_ld_includes_offices(): void
    {
        $response = $this->get('/fr');
        $response->assertOk();
        $response->assertSee('"addressLocality":"Kinshasa"', false);
        $response->assertSee('"addressCountry":"CD"', false);
        $response->assertSee('"addressLocality":"Abidjan"', false);
        $response->assertSee('"addressCountry":"CI"', false);
        $response->assertSee('"addressLocality":"Brazzaville"', false);
        $response->assertSee('"addressCountry":"CG"', false);
    }

    public function test_organization_not_rendered_on_non_localized_pages(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertDontSee('"@type":"Organization"', false);
    }

    public function test_breadcrumb_json_ld_on_marketing_hub(): void
    {
        $response = $this->get('/fr/products');
        $response->assertOk();
        $response->assertSee('"@type":"BreadcrumbList"', false);
        $response->assertSee('"name":"Accueil"', false);
        $response->assertSee('"position":1', false);
        $response->assertSee('"position":2', false);
    }

    public function test_breadcrumb_json_ld_on_product_detail_has_3_levels(): void
    {
        $this->seed([CountrySeeder::class, ServiceSeeder::class]);

        $response = $this->get('/fr/products/sms-a2p');
        $response->assertOk();
        $response->assertSee('"@type":"BreadcrumbList"', false);
        $response->assertSee('"position":1', false);
        $response->assertSee('"position":2', false);
        $response->assertSee('"position":3', false);
        $response->assertSee('"name":"Produits"', false);
        $response->assertSee('SMS A2P', false);
    }

    public function test_breadcrumb_json_ld_on_legal_page(): void
    {
        $response = $this->get('/fr/legal/cgu');
        $response->assertOk();
        $response->assertSee('"@type":"BreadcrumbList"', false);
        $response->assertSee('"name":"Legal"', false);
        $response->assertSee('Conditions generales', false);
    }

    public function test_breadcrumb_en_uses_english_labels(): void
    {
        $response = $this->get('/en/products');
        $response->assertOk();
        $response->assertSee('"name":"Home"', false);
        $response->assertSee('"@type":"BreadcrumbList"', false);
    }
}
