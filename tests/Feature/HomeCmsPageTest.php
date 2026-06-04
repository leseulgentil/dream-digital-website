<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeCmsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_home_cms_page_overrides_localized_home_hero_and_sections(): void
    {
        Page::create([
            'slug' => 'home',
            'section' => 'home',
            'locale' => 'fr',
            'title' => 'Accueil Dream Digital depuis le CMS',
            'meta_description' => 'Meta accueil CMS',
            'content_blocks' => [
                'eyebrow' => 'Accueil CMS',
                'lead' => 'Lead de la page accueil modifie depuis admin.',
                'sections' => [
                    [
                        'heading' => 'Bloc accueil admin',
                        'body' => 'Texte fallback',
                        'body_html' => '<p><strong>Contenu riche accueil</strong> publie depuis le CMS.</p>',
                    ],
                ],
            ],
            'is_published' => true,
            'editorial_status' => Page::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $this->get('/fr')
            ->assertOk()
            ->assertSee('Accueil Dream Digital depuis le CMS')
            ->assertSee('Lead de la page accueil modifie depuis admin.')
            ->assertSee('Accueil CMS')
            ->assertSee('Bloc accueil admin')
            ->assertSee('<strong>Contenu riche accueil</strong>', false);
    }

    public function test_home_cms_page_is_locale_scoped(): void
    {
        Page::create([
            'slug' => 'home',
            'section' => 'home',
            'locale' => 'fr',
            'title' => 'Titre accueil FR uniquement',
            'content_blocks' => ['lead' => 'Lead FR uniquement', 'sections' => []],
            'is_published' => true,
            'editorial_status' => Page::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $this->get('/en')
            ->assertOk()
            ->assertDontSee('Titre accueil FR uniquement')
            ->assertDontSee('Lead FR uniquement');
    }

    public function test_admin_page_form_supports_home_section(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));

        $this->get(route('admin.pages.create'))
            ->assertOk()
            ->assertSee('value="home"', false)
            ->assertSee('Page accueil');
    }
}
