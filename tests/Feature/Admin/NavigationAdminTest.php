<?php

namespace Tests\Feature\Admin;

use App\Models\NavigationItem;
use App\Models\User;
use Database\Seeders\NavigationItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));
    }

    public function test_navigation_admin_lists_items_and_route_suggestions(): void
    {
        $this->seed(NavigationItemSeeder::class);

        $this->get(route('admin.navigation.index'))
            ->assertOk()
            ->assertSee('Navigation principale')
            ->assertSee('Blog')
            ->assertSee('/{locale}/blog')
            ->assertSee('Adresses disponibles');
    }

    public function test_editor_can_create_custom_navigation_link(): void
    {
        $payload = [
            'menu_area' => 'main',
            'label_fr' => 'Ressources',
            'label_en' => 'Resources',
            'type' => NavigationItem::TYPE_LINK,
            'url' => '/{locale}/legal/mentions',
            'sort_order' => 90,
            'settings_description_fr' => 'Documents et ressources utiles',
            'is_active' => '1',
        ];

        $this->post(route('admin.navigation.store'), $payload)
            ->assertRedirect(route('admin.navigation.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('navigation_items', [
            'label_fr' => 'Ressources',
            'url' => '/{locale}/legal/mentions',
            'is_active' => true,
        ]);
    }
}
