<?php

namespace Tests\Feature;

use Database\Seeders\BlogContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPagePolishTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_renders_detail_blocks_from_config(): void
    {
        $this->get('/fr/products/sms-a2p')
            ->assertOk()
            ->assertSee('Latence surveillee')
            ->assertSee('Un chemin clair du cadrage au live');
    }

    public function test_product_page_renders_related_blog_guides_when_seeded(): void
    {
        $this->seed(BlogContentSeeder::class);

        $this->get('/fr/products/sms-a2p')
            ->assertOk()
            ->assertSee('Approfondir ce sujet')
            ->assertSee('SMS A2P et OTP en Afrique francophone');
    }
}
