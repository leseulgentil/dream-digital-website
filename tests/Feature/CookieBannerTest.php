<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CookieBannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cookie_banner_rendered_on_fr_home(): void
    {
        $this->get('/fr')
            ->assertOk()
            ->assertSee('dd-cookie-banner', false)
            ->assertSee('ddCookieBanner', false)
            ->assertSee('ddCookieAck', false);
    }

    public function test_cookie_banner_has_french_copy_on_fr_route(): void
    {
        $this->get('/fr')
            ->assertOk()
            ->assertSee('cookies necessaires a son fonctionnement', false)
            ->assertSee('Compris');
    }

    public function test_cookie_banner_has_english_copy_on_en_route(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('cookies strictly necessary', false)
            ->assertSee('Got it');
    }

    public function test_cookie_banner_links_to_legal_rgpd(): void
    {
        $this->get('/fr')
            ->assertOk()
            ->assertSee('/fr/legal/rgpd', false);

        $this->get('/en')
            ->assertOk()
            ->assertSee('/en/legal/rgpd', false);
    }

    public function test_cookie_banner_starts_hidden_for_js_to_unhide(): void
    {
        // Le banner a l'attribut `hidden` au render initial. C'est le JS
        // qui le revele si l'utilisateur n'a pas deja acknowledge.
        $this->get('/fr')
            ->assertOk()
            ->assertSee('id="ddCookieBanner"', false)
            ->assertSee('hidden>', false);
    }

    public function test_cookie_banner_loaded_via_vite_on_front_pages(): void
    {
        $this->get('/fr')
            ->assertOk()
            ->assertSee('dd-cookie-consent', false);

        $this->get('/fr/products')
            ->assertOk()
            ->assertSee('dd-cookie-consent', false);

        $this->get('/fr/legal/mentions')
            ->assertOk()
            ->assertSee('dd-cookie-consent', false);
    }
}
