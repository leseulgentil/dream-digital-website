<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_healthz_returns_ok_when_database_responds(): void
    {
        $this->get('/healthz')
            ->assertOk()
            ->assertJson(['status' => 'ok']);
    }

    public function test_readyz_returns_ready_when_database_and_migrations_are_available(): void
    {
        $this->get('/readyz')
            ->assertOk()
            ->assertJsonPath('status', 'ready')
            ->assertJsonPath('checks.database', true)
            ->assertJsonPath('checks.migrations_table', true)
            ->assertJsonPath('checks.pending_migrations', true);
    }

    public function test_security_txt_exposes_security_contact(): void
    {
        config(['dream-digital.security.security_txt.contact' => 'security@example.test']);

        $this->get('/.well-known/security.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Contact: mailto:security@example.test', false)
            ->assertSee('Preferred-Languages: fr, en', false);
    }
}
