<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\RoleProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_viewer_can_read_admin_but_cannot_write_content(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_VIEWER]));

        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.pages.index'))->assertOk();
        $this->get(route('admin.pricing.index'))->assertOk();

        $this->get(route('admin.pages.create'))->assertForbidden();
        $this->get(route('admin.pricing.create'))->assertForbidden();
        $this->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_editor_can_write_content_but_cannot_manage_users(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));

        $this->get(route('admin.pages.create'))->assertOk();
        $this->get(route('admin.pricing.create'))->assertOk();
        $this->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_inactive_user_cannot_view_admin(): void
    {
        $this->actingAs(User::factory()->create(['is_active' => false]));

        $this->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_owner_can_configure_role_permissions(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $this->actingAs($owner);

        $this->get(route('admin.role-profiles.edit'))
            ->assertOk()
            ->assertSee('Profils & acces')
            ->assertSee('Pages CMS');

        $this->put(route('admin.role-profiles.update'), [
            'profiles' => [
                User::ROLE_OWNER => [
                    'permissions' => array_keys(RoleProfile::availablePermissions()),
                ],
                User::ROLE_ADMIN => [
                    'permissions' => RoleProfile::defaultPermissionsFor(User::ROLE_ADMIN),
                ],
                User::ROLE_EDITOR => [
                    'permissions' => [
                        RoleProfile::PERMISSION_ADMIN_ACCESS,
                        RoleProfile::PERMISSION_DASHBOARD_VIEW,
                        RoleProfile::PERMISSION_PAGES_VIEW,
                        RoleProfile::PERMISSION_USERS_MANAGE,
                    ],
                ],
                User::ROLE_VIEWER => [
                    'permissions' => RoleProfile::defaultPermissionsFor(User::ROLE_VIEWER),
                ],
            ],
        ])->assertRedirect(route('admin.role-profiles.edit'));

        $editor = User::factory()->create(['role' => User::ROLE_EDITOR]);
        $this->actingAs($editor);

        $this->get(route('admin.pages.index'))->assertOk();
        $this->get(route('admin.pages.create'))->assertForbidden();
        $this->get(route('admin.users.index'))->assertOk();
    }

    public function test_editor_cannot_configure_role_permissions(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));

        $this->get(route('admin.role-profiles.edit'))->assertForbidden();
    }
}
