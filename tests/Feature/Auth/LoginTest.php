<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Admin Dream Digital');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@dream-digital.info',
            'password' => Hash::make('correct-horse'),
        ]);

        $this->post(route('login'), [
            'email' => 'admin@dream-digital.info',
            'password' => 'correct-horse',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@dream-digital.info',
            'password' => Hash::make('correct-horse'),
        ]);

        $this->post(route('login'), [
            'email' => 'admin@dream-digital.info',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login_to_admin(): void
    {
        User::factory()->create([
            'email' => 'inactive@dream-digital.info',
            'password' => Hash::make('correct-horse'),
            'is_active' => false,
        ]);

        $this->post(route('login'), [
            'email' => 'inactive@dream-digital.info',
            'password' => 'correct-horse',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_register_route_does_not_exist(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [])->assertNotFound();
    }
}
