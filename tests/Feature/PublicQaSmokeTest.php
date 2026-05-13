<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\BlogContentSeeder;
use Database\Seeders\LegalPageSeeder;
use Database\Seeders\MarketingPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicQaSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_surfaces_render_without_template_residue(): void
    {
        $this->seed([
            LegalPageSeeder::class,
            MarketingPageSeeder::class,
            BlogContentSeeder::class,
        ]);

        $blogSlug = Page::published()
            ->where('section', 'blog')
            ->where('locale', 'fr')
            ->value('slug');

        $paths = [
            '/fr',
            '/en',
            '/fr/products',
            '/fr/products/sms-a2p',
            '/fr/developers',
            '/fr/solutions',
            '/fr/coverage',
            '/fr/pricing',
            '/fr/company',
            '/fr/contact',
            '/fr/blog',
            "/fr/blog/{$blogSlug}",
            '/fr/legal/mentions',
            '/fr/legal/cgu',
            '/fr/legal/rgpd',
        ];

        foreach ($paths as $path) {
            $this->get($path)
                ->assertOk()
                ->assertDontSee('Sneat', false)
                ->assertDontSee('Pixinvent', false)
                ->assertDontSee('ThemeSelection', false);
        }
    }
}
