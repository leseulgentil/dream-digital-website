<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMetaTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_page_sets_og_title_with_eyebrow(): void
    {
        $response = $this->get('/fr/products');
        $response->assertOk();
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('Le catalogue telecom Dream Digital', false);
    }

    public function test_marketing_page_sets_og_description_from_lead(): void
    {
        $response = $this->get('/fr/coverage');
        $response->assertOk();
        $response->assertSee('<meta property="og:description"', false);
        // Le lead /fr/coverage contient "Plus de 200 destinations"
        $response->assertSee('Plus de 200 destinations', false);
    }

    public function test_marketing_page_meta_description_uses_lead(): void
    {
        $response = $this->get('/fr/pricing');
        $response->assertOk();
        // Le lead /fr/pricing contient "Commencez par un test"
        $response->assertSee('Commencez par un test', false);
    }

    public function test_legal_page_sets_og_type_article(): void
    {
        $response = $this->get('/fr/legal/mentions');
        $response->assertOk();
        $response->assertSee('<meta property="og:type" content="article"', false);
    }

    public function test_canonical_url_matches_request_url(): void
    {
        $response = $this->get('/fr/products');
        $response->assertOk();
        $response->assertSee('<link rel="canonical" href="http://127.0.0.1', false);
        $response->assertSee('/fr/products"', false);
    }

    public function test_og_image_falls_back_to_brand_logo(): void
    {
        $response = $this->get('/fr');
        $response->assertOk();
        $response->assertSee('<meta property="og:image"', false);
        $response->assertSee('logo-dd-icon.png', false);
    }

    public function test_twitter_card_present_with_summary_large_image(): void
    {
        $response = $this->get('/fr/products');
        $response->assertOk();
        $response->assertSee('<meta name="twitter:card" content="summary_large_image"', false);
        $response->assertSee('<meta name="twitter:title"', false);
        $response->assertSee('<meta name="twitter:description"', false);
    }

    public function test_og_locale_changes_with_route_locale(): void
    {
        $this->get('/fr')
            ->assertOk()
            ->assertSee('<meta property="og:locale" content="fr_FR"', false);

        $this->get('/en')
            ->assertOk()
            ->assertSee('<meta property="og:locale" content="en_US"', false);
    }

    public function test_robots_remains_noindex_when_indexable_disabled(): void
    {
        config(['app.env' => 'testing']);
        $response = $this->get('/fr');
        $response->assertOk();
        $response->assertSee('<meta name="robots" content="noindex, nofollow"', false);
    }
}
