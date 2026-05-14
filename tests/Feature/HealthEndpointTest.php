<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthEndpointTest extends TestCase
{
    public function test_healthz_returns_ok_when_database_responds(): void
    {
        $this->get('/healthz')
            ->assertOk()
            ->assertJson(['status' => 'ok']);
    }
}
