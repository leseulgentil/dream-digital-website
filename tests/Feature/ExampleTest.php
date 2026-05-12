<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/fr');
    }

    #[DataProvider('publicPageProvider')]
    public function test_public_marketing_pages_return_successful_response(string $uri): void
    {
        $this->get($uri)->assertStatus(200);
    }

    public static function publicPageProvider(): array
    {
        return [
            ['/fr'],
            ['/fr/products'],
            ['/fr/products/sms-a2p'],
            ['/fr/developers'],
            ['/fr/solutions'],
            ['/fr/coverage'],
            ['/fr/pricing'],
            ['/fr/company'],
            ['/fr/contact'],
        ];
    }
}
