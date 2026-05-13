<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontPerformanceAssetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_pages_do_not_load_home_only_landing_script(): void
    {
        $content = $this->get('/fr/products/sms-a2p')
            ->assertOk()
            ->getContent();

        $this->assertDoesNotMatchRegularExpression('/front-page-landing-[^"\']+\.js/', $content);
    }

    public function test_home_keeps_landing_script_and_front_layout_avoids_duplicate_font(): void
    {
        $content = $this->get('/fr')
            ->assertOk()
            ->getContent();

        $this->assertMatchesRegularExpression('/front-page-landing-[^"\']+\.js/', $content);
        $this->assertStringNotContainsString('Public+Sans', $content);
    }
}
