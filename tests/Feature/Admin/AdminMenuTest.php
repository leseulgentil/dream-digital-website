<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_menu_only_exposes_dream_digital_modules(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->get(route('admin.dashboard'))->assertOk();
        $menuHtml = $this->extractVerticalMenu($response->getContent());

        $this->assertStringContainsString('Dashboard', $menuHtml);
        $this->assertStringContainsString('Pages', $menuHtml);
        $this->assertStringContainsString('Navigation', $menuHtml);
        $this->assertStringContainsString('Blog', $menuHtml);
        $this->assertStringContainsString('Media CMS', $menuHtml);
        $this->assertStringContainsString('Pricing', $menuHtml);
        $this->assertStringContainsString('Utilisateurs', $menuHtml);
        $this->assertStringContainsString('Voir le site', $menuHtml);
        $this->assertStringContainsString('/admin/pages', $menuHtml);
        $this->assertStringContainsString('/admin/navigation', $menuHtml);
        $this->assertStringContainsString('/admin/pages?section=blog', $menuHtml);
        $this->assertStringContainsString('/admin/media', $menuHtml);
        $this->assertStringContainsString('/admin/pricing', $menuHtml);
        $this->assertStringContainsString('/admin/users', $menuHtml);

        foreach (['eCommerce', 'Layouts', 'Academy', 'Form Elements', 'Datatables', 'Roles', 'Email', 'Discuter', 'Calendrier', 'Commerce', 'Logistique', 'Facture', 'Authentification', 'Laravel Example', 'Premi'] as $legacyLabel) {
            $this->assertStringNotContainsString($legacyLabel, $menuHtml);
        }
    }

    public function test_viewer_does_not_see_user_management_link(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_VIEWER]));

        $response = $this->get(route('admin.dashboard'))->assertOk();
        $menuHtml = $this->extractVerticalMenu($response->getContent());

        $this->assertStringNotContainsString('Utilisateurs', $menuHtml);
        $this->assertStringNotContainsString('/admin/users', $menuHtml);
    }

    public function test_vertical_menu_config_stays_compact(): void
    {
        $menu = json_decode(file_get_contents(base_path('resources/menu/verticalMenu.json')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame([
            'Dream Digital',
            'Dashboard',
            'Pages',
            'Navigation',
            'Blog',
            'Media CMS',
            'Pricing',
            'Utilisateurs',
            'Public',
            'Voir le site',
        ], collect($menu['menu'])->map(fn (array $item) => $item['menuHeader'] ?? $item['name'])->all());
    }

    private function extractVerticalMenu(string $html): string
    {
        preg_match('/<aside id="dd-layout-menu".*?<\/aside>/s', $html, $matches);

        $this->assertNotEmpty($matches[0] ?? null, 'Le menu vertical admin doit etre present.');

        return $matches[0];
    }
}
