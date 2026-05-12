<?php

namespace Tests\Feature;

use Database\Seeders\CountrySeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HreflangTest extends TestCase
{
    use RefreshDatabase;

    public function test_fr_home_renders_hreflang_alternates(): void
    {
        $this->get('/fr')
            ->assertOk()
            ->assertSee('hreflang="fr" href="http://127.0.0.1:8888/fr"', false)
            ->assertSee('hreflang="en" href="http://127.0.0.1:8888/en"', false)
            ->assertSee('hreflang="x-default" href="http://127.0.0.1:8888/fr"', false);
    }

    public function test_en_marketing_page_renders_hreflang_alternates(): void
    {
        $this->get('/en/products')
            ->assertOk()
            ->assertSee('hreflang="fr" href="http://127.0.0.1:8888/fr/products"', false)
            ->assertSee('hreflang="en" href="http://127.0.0.1:8888/en/products"', false);
    }

    public function test_legal_page_renders_hreflang_with_full_path(): void
    {
        $this->get('/fr/legal/cgu')
            ->assertOk()
            ->assertSee('hreflang="fr" href="http://127.0.0.1:8888/fr/legal/cgu"', false)
            ->assertSee('hreflang="en" href="http://127.0.0.1:8888/en/legal/cgu"', false);
    }

    public function test_product_detail_page_renders_hreflang(): void
    {
        $this->seed([CountrySeeder::class, ServiceSeeder::class]);

        $this->get('/fr/products/sms-a2p')
            ->assertOk()
            ->assertSee('hreflang="fr" href="http://127.0.0.1:8888/fr/products/sms-a2p"', false)
            ->assertSee('hreflang="en" href="http://127.0.0.1:8888/en/products/sms-a2p"', false);
    }

    public function test_login_page_does_not_render_hreflang(): void
    {
        // /login n'est pas une page localisee -> pas d'alternates
        $this->get('/login')
            ->assertOk()
            ->assertDontSee('rel="alternate" hreflang', false);
    }
}
