<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class I18nContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_en_home_meta_description_is_english(): void
    {
        $response = $this->get('/en');
        $response->assertOk();
        $response->assertSee('The telecom operator connecting modern enterprises', false);
        $response->assertDontSee('L\'opérateur télécom qui connecte les entreprises modernes', false);
    }

    public function test_fr_home_meta_description_is_french(): void
    {
        $response = $this->get('/fr');
        $response->assertOk();
        $response->assertSee('L&#039;opérateur télécom qui connecte les entreprises modernes', false);
    }

    public function test_en_product_page_renders_english_description(): void
    {
        $this->get('/en/products/sms-a2p')
            ->assertOk()
            ->assertSee('Application-to-person messaging', false)
            ->assertDontSee('Messagerie applicatif-à-personne', false);
    }

    public function test_en_coverage_page_renders_english_lead(): void
    {
        $this->get('/en/coverage')
            ->assertOk()
            ->assertSee('Our infrastructure covers 200+ destinations worldwide', false);
    }

    public function test_en_solutions_page_renders_english_industries(): void
    {
        $this->get('/en/solutions')
            ->assertOk()
            ->assertSee('Transactional OTP, fraud alerts', false)
            ->assertSee('Order confirmations, delivery notifications', false);
    }

    public function test_fr_product_page_keeps_french_description(): void
    {
        $this->get('/fr/products/voice')
            ->assertOk()
            ->assertSee('Terminaisons voix grossiste', false);
    }
}
