<?php

namespace Tests\Feature;

use App\Models\NavigationItem;
use Database\Seeders\NavigationItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_main_menu_exposes_blog_link_from_navigation_table(): void
    {
        $this->seed(NavigationItemSeeder::class);

        $navHtml = $this->extractFrontNav($this->get('/fr')->assertOk()->getContent());

        $this->assertStringContainsString('Blog', $navHtml);
        $this->assertStringContainsString('/fr/blog', $navHtml);
    }

    public function test_admin_can_hide_or_add_public_menu_links(): void
    {
        $this->seed(NavigationItemSeeder::class);

        NavigationItem::where('label_fr', 'Blog')->update(['is_active' => false]);
        NavigationItem::create([
            'menu_area' => 'main',
            'label_fr' => 'Ressources',
            'label_en' => 'Resources',
            'type' => NavigationItem::TYPE_LINK,
            'url' => '/{locale}/legal/mentions',
            'sort_order' => 95,
            'is_active' => true,
        ]);

        $navHtml = $this->extractFrontNav($this->get('/fr')->assertOk()->getContent());

        $this->assertStringNotContainsString('>Blog</a>', $navHtml);
        $this->assertStringContainsString('Ressources', $navHtml);
        $this->assertStringContainsString('/fr/legal/mentions', $navHtml);
    }

    private function extractFrontNav(string $html): string
    {
        preg_match('/<nav class="dd-layout-navbar.*?<\/nav>/s', $html, $matches);

        $this->assertNotEmpty($matches[0] ?? null, 'Le menu public doit etre present.');

        return $matches[0];
    }
}
