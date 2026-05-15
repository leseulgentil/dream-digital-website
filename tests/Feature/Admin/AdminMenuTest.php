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
        $this->assertStringContainsString('Company Profile', $menuHtml);
        $this->assertStringContainsString('Blog', $menuHtml);
        $this->assertStringContainsString('Media CMS', $menuHtml);
        $this->assertStringContainsString('Pricing', $menuHtml);
        $this->assertStringContainsString('Leads', $menuHtml);
        $this->assertStringContainsString('Utilisateurs', $menuHtml);
        $this->assertStringContainsString('Profils &amp; acces', $menuHtml);
        $this->assertStringContainsString('Voir le site', $menuHtml);
        $this->assertStringContainsString('/admin/pages', $menuHtml);
        $this->assertStringContainsString('/admin/navigation', $menuHtml);
        $this->assertStringContainsString('/admin/company-profile', $menuHtml);
        $this->assertStringContainsString('/admin/pages?section=blog', $menuHtml);
        $this->assertStringContainsString('/admin/media', $menuHtml);
        $this->assertStringContainsString('/admin/pricing', $menuHtml);
        $this->assertStringContainsString('/admin/contact-leads', $menuHtml);
        $this->assertStringContainsString('/admin/users', $menuHtml);
        $this->assertStringContainsString('/admin/role-profiles', $menuHtml);

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
        $this->assertStringNotContainsString('Company Profile', $menuHtml);
        $this->assertStringNotContainsString('/admin/company-profile', $menuHtml);
        $this->assertStringNotContainsString('Profils &amp; acces', $menuHtml);
        $this->assertStringNotContainsString('/admin/role-profiles', $menuHtml);
    }

    public function test_vertical_menu_config_stays_compact(): void
    {
        $menu = json_decode(file_get_contents(base_path('resources/menu/verticalMenu.json')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame([
            'Dream Digital',
            'Dashboard',
            'Pages',
            'Navigation',
            'Company Profile',
            'Blog',
            'Media CMS',
            'Pricing',
            'Leads',
            'Utilisateurs',
            'Profils & acces',
            'Public',
            'Voir le site',
        ], collect($menu['menu'])->map(fn (array $item) => $item['menuHeader'] ?? $item['name'])->all());
    }

    public function test_pages_and_blog_menu_items_have_distinct_active_state(): void
    {
        $this->actingAs(User::factory()->create());

        $pagesMenuHtml = $this->extractVerticalMenu($this->get(route('admin.pages.index'))->assertOk()->getContent());
        $blogMenuHtml = $this->extractVerticalMenu($this->get(route('admin.pages.index', ['section' => 'blog']))->assertOk()->getContent());

        $this->assertSame(['Pages'], $this->activeMenuLabels($pagesMenuHtml));
        $this->assertSame(['Blog'], $this->activeMenuLabels($blogMenuHtml));
    }

    public function test_admin_chrome_does_not_expose_unused_template_widgets(): void
    {
        $this->actingAs(User::factory()->create());

        $html = $this->get(route('admin.dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('Backoffice interne', $html);
        $this->assertStringContainsString('Voir le site', $html);

        foreach (['Search [CTRL', 'Notifications', 'Shortcuts', 'Admin Templates', 'Documentation', 'License', 'Invoice App'] as $legacyLabel) {
            $this->assertStringNotContainsString($legacyLabel, $html);
        }
    }

    private function extractVerticalMenu(string $html): string
    {
        preg_match('/<aside id="dd-layout-menu".*?<\/aside>/s', $html, $matches);

        $this->assertNotEmpty($matches[0] ?? null, 'Le menu vertical admin doit etre present.');

        return $matches[0];
    }

    /**
     * @return list<string>
     */
    private function activeMenuLabels(string $menuHtml): array
    {
        preg_match_all('/<li class="dd-menu-item\s+[^"]*\bactive\b[^"]*">.*?<div>(.*?)<\/div>/s', $menuHtml, $matches);

        return collect($matches[1])
            ->map(fn (string $label): string => trim(html_entity_decode(strip_tags($label))))
            ->filter()
            ->values()
            ->all();
    }
}
