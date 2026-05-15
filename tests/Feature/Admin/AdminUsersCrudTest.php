<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUsersCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $this->actingAs($this->owner);
    }

    public function test_guest_cannot_access_users_admin(): void
    {
        auth()->logout();

        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));
    }

    public function test_editor_cannot_manage_users(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));

        $this->get(route('admin.users.index'))->assertForbidden();
        $this->get(route('admin.users.create'))->assertForbidden();
    }

    public function test_index_renders_for_owner(): void
    {
        User::factory()->create([
            'name' => 'Equipe Contenu',
            'role' => User::ROLE_EDITOR,
        ]);

        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Utilisateurs')
            ->assertSee('Equipe Contenu');
    }

    public function test_admin_can_manage_users(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));

        $this->get(route('admin.users.index'))->assertOk();
        $this->get(route('admin.users.create'))->assertOk();
    }

    public function test_store_creates_user(): void
    {
        $this->post(route('admin.users.store'), [
            'name' => 'Redacteur SEO',
            'email' => 'seo@example.test',
            'role' => User::ROLE_EDITOR,
            'is_active' => '1',
            'password' => 'strong-password-2026',
            'password_confirmation' => 'strong-password-2026',
        ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'email' => 'seo@example.test',
            'role' => User::ROLE_EDITOR,
            'is_active' => true,
        ]);
    }

    public function test_update_modifies_user_without_requiring_password(): void
    {
        $user = User::factory()->create([
            'name' => 'Assistant',
            'role' => User::ROLE_VIEWER,
        ]);

        $this->put(route('admin.users.update', $user), [
            'name' => 'Assistant Admin',
            'email' => 'assistant@example.test',
            'role' => User::ROLE_ADMIN,
            'is_active' => '1',
            'password' => '',
            'password_confirmation' => '',
        ])
            ->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertSame('Assistant Admin', $user->name);
        $this->assertSame('assistant@example.test', $user->email);
        $this->assertSame(User::ROLE_ADMIN, $user->role);
    }

    public function test_destroy_deactivates_user_instead_of_deleting_it(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertFalse($user->is_active);
    }

    public function test_admin_cannot_deactivate_self(): void
    {
        $this->delete(route('admin.users.destroy', $this->owner))
            ->assertForbidden();

        $this->owner->refresh();
        $this->assertTrue($this->owner->is_active);
    }

    public function test_owner_can_reset_user_password_from_admin(): void
    {
        $user = User::factory()->create([
            'email' => 'reset-me@example.test',
            'password' => 'old-password-2026',
        ]);

        $this->post(route('admin.users.reset-password', $user))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('temporary_password')
            ->assertSessionHas('status');

        $temporaryPassword = session('temporary_password');

        $this->assertIsString($temporaryPassword);
        $this->assertGreaterThanOrEqual(16, strlen($temporaryPassword));
        $this->assertTrue(Hash::check($temporaryPassword, $user->refresh()->password));
    }

    public function test_admin_user_seed_does_not_overwrite_existing_admin_password(): void
    {
        config(['app.env' => 'testing']);
        putenv('DD_ADMIN_EMAIL=gentil.mapendo@dream-digital.info');
        putenv('DD_ADMIN_PASSWORD=env-password-2026');

        $user = User::factory()->create([
            'email' => 'gentil.mapendo@dream-digital.info',
            'password' => 'cms-password-2026',
        ]);

        $this->seed(AdminUserSeeder::class);

        $this->assertTrue(Hash::check('cms-password-2026', $user->refresh()->password));

        putenv('DD_ADMIN_EMAIL');
        putenv('DD_ADMIN_PASSWORD');
    }
}
