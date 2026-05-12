<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\LegalPageSeeder;
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

    public function test_legal_seeder_populates_pages_table(): void
    {
        $this->seed(LegalPageSeeder::class);

        $this->assertSame(6, Page::where('section', 'legal')->count());
        $this->assertSame(3, Page::where('section', 'legal')->where('locale', 'fr')->count());
        $this->assertSame(3, Page::where('section', 'legal')->where('locale', 'en')->count());

        $mentionsFr = Page::where('section', 'legal')->where('slug', 'mentions')->where('locale', 'fr')->first();
        $this->assertNotNull($mentionsFr);
        $this->assertSame('Mentions legales', $mentionsFr->title);
        $this->assertIsArray($mentionsFr->content_blocks);
        $this->assertArrayHasKey('sections', $mentionsFr->content_blocks);
        $this->assertNotEmpty($mentionsFr->content_blocks['sections']);
    }

    public function test_legal_controller_prefers_db_over_config(): void
    {
        Page::create([
            'slug' => 'mentions',
            'section' => 'legal',
            'country_id' => null,
            'locale' => 'fr',
            'title' => 'DB-DRIVEN-MENTIONS',
            'content_blocks' => [
                'eyebrow' => 'DB-EYEBROW',
                'lead' => 'DB-LEAD',
                'last_updated' => '2099-01-01',
                'sections' => [
                    ['heading' => 'DB-HEADING', 'body' => 'DB-BODY paragraph 1.'],
                ],
            ],
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->get('/fr/legal/mentions')
            ->assertOk()
            ->assertSee('DB-DRIVEN-MENTIONS')
            ->assertSee('DB-EYEBROW')
            ->assertSee('DB-HEADING')
            ->assertSee('DB-BODY paragraph 1.')
            ->assertDontSee('Editeur du site'); // Section heading config absente quand DB sert
    }

    public function test_legal_controller_falls_back_to_config_when_db_empty(): void
    {
        // DB vide pour section=legal => fallback config
        $this->assertSame(0, Page::where('section', 'legal')->count());

        $this->get('/fr/legal/mentions')
            ->assertOk()
            ->assertSee('Mentions legales')
            ->assertSee('Editeur du site');
    }

    public function test_unpublished_db_page_falls_back_to_config(): void
    {
        Page::create([
            'slug' => 'cgu',
            'section' => 'legal',
            'country_id' => null,
            'locale' => 'fr',
            'title' => 'CGU-DRAFT-NOT-PUBLISHED',
            'content_blocks' => ['sections' => []],
            'is_published' => false,
        ]);

        $this->get('/fr/legal/cgu')
            ->assertOk()
            ->assertDontSee('CGU-DRAFT-NOT-PUBLISHED')
            ->assertSee('Conditions generales');
    }
}
