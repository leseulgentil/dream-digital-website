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
        $this->assertStringContainsString('Pricing', $menuHtml);
        $this->assertStringContainsString('Voir le site', $menuHtml);
        $this->assertStringContainsString('/admin/pages', $menuHtml);
        $this->assertStringContainsString('/admin/pricing', $menuHtml);

        foreach (['eCommerce', 'Layouts', 'Academy', 'Form Elements', 'Datatables', 'Roles', 'Utilisateurs'] as $legacyLabel) {
            $this->assertStringNotContainsString($legacyLabel, $menuHtml);
        }
    }

    public function test_vertical_menu_config_stays_compact(): void
    {
        $menu = json_decode(file_get_contents(base_path('resources/menu/verticalMenu.json')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame([
            'Dream Digital',
            'Dashboard',
            'Pages',
            'Pricing',
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
