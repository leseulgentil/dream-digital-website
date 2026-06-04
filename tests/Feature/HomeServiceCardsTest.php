<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeServiceCardsTest extends TestCase
{
    public function test_french_home_displays_illustrated_priority_service_cards(): void
    {
        $response = $this->get('/fr')->assertOk();

        foreach ([
            'SMS Wholesale',
            'SMS Retail',
            'Voice Wholesale',
            'Voice Retail',
            'eSIMZone',
            'DIALO',
        ] as $title) {
            $response->assertSee($title);
        }

        $response->assertSee('data-dd-home-service-card', false);
        $response->assertSee('/img/services/sms-wholesale.webp', false);
        $response->assertSee('alt="Illustration SMS Wholesale Dream Digital"', false);
        $response->assertSee('href="https://esimzone.fr/"', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener"', false);
    }

    public function test_english_home_displays_localized_service_card_copy(): void
    {
        $response = $this->get('/en')->assertOk();

        $response->assertSee('Retail SMS campaigns, OTP flows and customer notifications');
        $response->assertSee('Wholesale voice routes for operators, integrators and platforms');
        $response->assertSee('Omnichannel call center platform');
        $response->assertDontSee('Campagnes SMS retail, OTP et notifications client');
    }
}
