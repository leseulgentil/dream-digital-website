<?php

namespace Tests\Feature\Admin;

use App\Models\User;
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
}
