<?php

namespace Tests\Feature;

use Database\Seeders\CountrySeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoIndexableTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_disallows_when_not_indexable(): void
    {
        putenv('DD_PUBLIC_INDEXABLE=false');

        $response = $this->get('/robots.txt');
        $response->assertOk();
        $response->assertHeader('content-type', 'text/plain; charset=UTF-8');
        $response->assertSeeText('User-agent: *');
        $response->assertSeeText('Disallow: /');
        $response->assertDontSee('Sitemap:');
    }

    public function test_robots_txt_allows_when_indexable(): void
    {
        putenv('DD_PUBLIC_INDEXABLE=true');

        $response = $this->get('/robots.txt');
        $response->assertOk();
        $response->assertSeeText('User-agent: *');
        $response->assertSeeText('Allow: /');
        $response->assertSeeText('Sitemap:');
        $response->assertSeeText('/sitemap.xml');
    }

    public function test_sitemap_returns_410_when_not_indexable(): void
    {
        putenv('DD_PUBLIC_INDEXABLE=false');

        $this->get('/sitemap.xml')->assertStatus(410);
    }

    public function test_sitemap_returns_xml_when_indexable(): void
    {
        $this->seed([CountrySeeder::class, ServiceSeeder::class]);
        putenv('DD_PUBLIC_INDEXABLE=true');

        $response = $this->get('/sitemap.xml');
        $response->assertOk();
        $response->assertHeader('content-type', 'application/xml; charset=UTF-8');
        $response->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false);
        $response->assertSee('<urlset', false);
        $response->assertSee('</urlset>', false);
    }

    public function test_sitemap_contains_locale_homes(): void
    {
        $this->seed([CountrySeeder::class, ServiceSeeder::class]);
        putenv('DD_PUBLIC_INDEXABLE=true');

        $response = $this->get('/sitemap.xml');
        $response->assertOk();
        $response->assertSee('/fr</loc>', false);
        $response->assertSee('/en</loc>', false);
    }

    public function test_sitemap_contains_marketing_hubs_both_locales(): void
    {
        $this->seed([CountrySeeder::class, ServiceSeeder::class]);
        putenv('DD_PUBLIC_INDEXABLE=true');

        $response = $this->get('/sitemap.xml');
        $response->assertOk();
        foreach (['products', 'developers', 'solutions', 'coverage', 'pricing', 'company', 'contact'] as $hub) {
            $response->assertSee("/fr/{$hub}</loc>", false);
            $response->assertSee("/en/{$hub}</loc>", false);
        }
    }

    public function test_sitemap_contains_legal_pages_both_locales(): void
    {
        $this->seed([CountrySeeder::class, ServiceSeeder::class]);
        putenv('DD_PUBLIC_INDEXABLE=true');

        $response = $this->get('/sitemap.xml');
        $response->assertOk();
        foreach (['mentions', 'cgu', 'rgpd'] as $slug) {
            $response->assertSee("/fr/legal/{$slug}</loc>", false);
            $response->assertSee("/en/legal/{$slug}</loc>", false);
        }
    }

    public function test_sitemap_contains_active_service_product_pages(): void
    {
        $this->seed([CountrySeeder::class, ServiceSeeder::class]);
        putenv('DD_PUBLIC_INDEXABLE=true');

        $response = $this->get('/sitemap.xml');
        $response->assertOk();
        // 6 services config (sms-a2p, voice, did, sip, dialo, esim)
        $response->assertSee('/fr/products/sms-a2p</loc>', false);
        $response->assertSee('/en/products/voice</loc>', false);
    }

    protected function tearDown(): void
    {
        // Restaure l'etat env apres chaque test pour eviter pollution.
        putenv('DD_PUBLIC_INDEXABLE=false');
        parent::tearDown();
    }
}
