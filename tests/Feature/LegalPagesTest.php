<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider legalCombinations
     */
    public function test_legal_pages_render_in_both_locales(string $locale, string $slug, string $expectedTitle): void
    {
        $this->get("/{$locale}/legal/{$slug}")
            ->assertOk()
            ->assertSee($expectedTitle)
            ->assertSeeText('Dream Digital');
    }

    public static function legalCombinations(): array
    {
        return [
            'fr mentions' => ['fr', 'mentions', 'Mentions legales'],
            'fr cgu'      => ['fr', 'cgu', 'Conditions generales'],
            'fr rgpd'     => ['fr', 'rgpd', 'confidentialite'],
            'en mentions' => ['en', 'mentions', 'Legal notice'],
            'en cgu'      => ['en', 'cgu', 'Terms of use'],
            'en rgpd'     => ['en', 'rgpd', 'Privacy policy'],
        ];
    }

    public function test_unknown_legal_slug_returns_404(): void
    {
        $this->get('/fr/legal/inconnu')->assertNotFound();
    }

    public function test_unknown_legal_locale_returns_404(): void
    {
        $this->get('/de/legal/mentions')->assertNotFound();
    }

    public function test_footer_contains_legal_links(): void
    {
        $response = $this->get('/fr/company');
        $response->assertOk();
        $response->assertSee('/fr/legal/mentions', false);
        $response->assertSee('/fr/legal/cgu', false);
        $response->assertSee('/fr/legal/rgpd', false);
    }
}
