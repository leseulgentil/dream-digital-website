<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\MarketingPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingPagesDbTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_seeder_populates_14_rows(): void
    {
        $this->seed(MarketingPageSeeder::class);

        $this->assertSame(14, Page::where('section', 'marketing')->count());
        $this->assertSame(7, Page::where('section', 'marketing')->where('locale', 'fr')->count());
        $this->assertSame(7, Page::where('section', 'marketing')->where('locale', 'en')->count());

        foreach (['products', 'developers', 'solutions', 'coverage', 'pricing', 'company', 'contact'] as $slug) {
            $this->assertNotNull(
                Page::where('section', 'marketing')->where('slug', $slug)->where('locale', 'fr')->first(),
                "Missing FR row for slug=$slug",
            );
            $this->assertNotNull(
                Page::where('section', 'marketing')->where('slug', $slug)->where('locale', 'en')->first(),
                "Missing EN row for slug=$slug",
            );
        }
    }

    public function test_marketing_pages_have_custom_seo_images_and_editable_sections(): void
    {
        $this->seed(MarketingPageSeeder::class);

        $page = Page::where('section', 'marketing')
            ->where('slug', 'products')
            ->where('locale', 'fr')
            ->firstOrFail();

        $blocks = $page->content_blocks ?? [];

        $this->assertStringContainsString('CPaaS', $blocks['seo_title']);
        $this->assertStringStartsWith('https://images.unsplash.com/', $page->meta_image_path);
        $this->assertNotEmpty($blocks['seo_focus_keywords']);
        $this->assertGreaterThanOrEqual(2, count($blocks['faq'] ?? []));
        $this->assertNotEmpty($blocks['internal_links'] ?? []);
        $this->assertCount(3, $blocks['sections']);

        $this->get('/fr/products')
            ->assertOk()
            ->assertSee($blocks['seo_title'], false)
            ->assertSee($page->meta_description, false)
            ->assertSee($page->meta_image_path)
            ->assertSee('dd-page-hero__media', false)
            ->assertSee('Focus SEO et business utile')
            ->assertSee('"@type":"FAQPage"', false)
            ->assertSee('SMS A2P');
    }

    public function test_marketing_controller_prefers_db_over_config(): void
    {
        Page::create([
            'slug' => 'products',
            'section' => 'marketing',
            'country_id' => null,
            'locale' => 'fr',
            'title' => 'DB-PRODUCTS-TITLE',
            'content_blocks' => [
                'eyebrow' => 'DB-EYEBROW',
                'lead' => 'DB-LEAD-PROD',
            ],
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->get('/fr/products')
            ->assertOk()
            ->assertSee('DB-PRODUCTS-TITLE')
            ->assertSee('DB-EYEBROW')
            ->assertSee('DB-LEAD-PROD');
    }

    public function test_marketing_controller_falls_back_to_config_when_db_empty(): void
    {
        $this->assertSame(0, Page::where('section', 'marketing')->count());

        // Config (dream-digital.pages.pages.developers) doit prendre le relais
        $this->get('/fr/developers')
            ->assertOk()
            ->assertSee('parcours technique clair', false);
    }

    public function test_unpublished_marketing_page_falls_back_to_config(): void
    {
        Page::create([
            'slug' => 'company',
            'section' => 'marketing',
            'country_id' => null,
            'locale' => 'fr',
            'title' => 'COMPANY-DRAFT-NOT-PUBLISHED',
            'content_blocks' => ['eyebrow' => 'X', 'lead' => 'Y'],
            'is_published' => false,
        ]);

        $this->get('/fr/company')
            ->assertOk()
            ->assertDontSee('COMPANY-DRAFT-NOT-PUBLISHED')
            ->assertSee('operateur CPaaS global', false); // config lead /fr/company
    }

    public function test_marketing_pages_render_in_english_from_db(): void
    {
        Page::create([
            'slug' => 'coverage',
            'section' => 'marketing',
            'country_id' => null,
            'locale' => 'en',
            'title' => 'DB-EN-COVERAGE',
            'content_blocks' => [
                'eyebrow' => 'COVERAGE-EYEBROW-EN',
                'lead' => 'COVERAGE-LEAD-EN',
            ],
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->get('/en/coverage')
            ->assertOk()
            ->assertSee('DB-EN-COVERAGE')
            ->assertSee('COVERAGE-LEAD-EN');
    }

    public function test_unknown_marketing_slug_returns_404(): void
    {
        $this->get('/fr/inconnue')->assertNotFound();
    }
}
