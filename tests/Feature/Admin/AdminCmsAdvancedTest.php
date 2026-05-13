<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminCmsAdvancedTest extends TestCase
{
    use RefreshDatabase;

    private User $editor;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.openai.article_provider' => 'local']);

        $this->editor = User::factory()->create(['role' => User::ROLE_EDITOR]);
        $this->actingAs($this->editor);
    }

    public function test_page_update_records_revision_snapshot(): void
    {
        $page = Page::create([
            'slug' => 'revision-test',
            'section' => 'blog',
            'locale' => 'fr',
            'title' => 'Titre initial',
            'content_blocks' => ['sections' => []],
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->put(route('admin.pages.update', $page), [
            'slug' => 'revision-test',
            'section' => 'blog',
            'locale' => 'fr',
            'title' => 'Titre revise',
            'meta_description' => 'Description revisee',
            'seo_title' => 'SEO revise',
            'lead' => 'Lead revise',
            'tags' => 'SMS, CPaaS',
            'sections_json' => '[{"heading":"Bloc","body":"Texte"}]',
            'is_published' => '1',
        ])->assertRedirect(route('admin.pages.index'));

        $this->assertDatabaseHas('page_revisions', [
            'page_id' => $page->id,
            'user_id' => $this->editor->id,
            'action' => 'updated',
            'title' => 'Titre revise',
        ]);

        $this->get(route('admin.pages.edit', $page))
            ->assertOk()
            ->assertSee('Revisions recentes')
            ->assertSee('Titre revise');
    }

    public function test_media_library_lists_images_uploaded_from_pages(): void
    {
        $file = UploadedFile::fake()->image('cms-media.jpg', 800, 450);

        $this->post(route('admin.pages.store'), [
            'slug' => 'media-library-test',
            'section' => 'blog',
            'locale' => 'fr',
            'title' => 'Media library test',
            'meta_description' => 'Description',
            'lead' => 'Lead',
            'sections_json' => '[]',
            'image_file' => $file,
            'is_published' => '1',
        ])->assertRedirect(route('admin.pages.index'));

        $page = Page::where('slug', 'media-library-test')->firstOrFail();
        $this->assertNotNull($page->meta_image_path);
        $this->assertFileExists(public_path(ltrim($page->meta_image_path, '/')));

        $this->get(route('admin.media.index'))
            ->assertOk()
            ->assertSee(basename($page->meta_image_path))
            ->assertSee($page->meta_image_path);

        File::delete(public_path(ltrim($page->meta_image_path, '/')));
    }

    public function test_cms_form_shows_section_schema_guidance(): void
    {
        $page = Page::create([
            'slug' => 'schema-test',
            'section' => 'blog',
            'locale' => 'fr',
            'title' => 'Schema test',
            'content_blocks' => ['sections' => []],
            'is_published' => false,
        ]);

        $this->get(route('admin.pages.edit', $page))
            ->assertOk()
            ->assertSee('Article blog SEO')
            ->assertSee('seo_title, meta_description, author', false);
    }

    public function test_article_generator_returns_selectable_variants(): void
    {
        $this->postJson(route('admin.pages.generate-article'), [
            'idea' => 'SMS A2P pour banques africaines',
            'keywords' => 'SMS A2P, OTP, banking',
            'guidelines' => 'Inclure un angle conversion et support',
            'locale' => 'fr',
            'variants' => 3,
        ])
            ->assertOk()
            ->assertJsonCount(3, 'articles')
            ->assertJsonPath('articles.0.section', 'blog')
            ->assertJsonPath('articles.0.locale', 'fr')
            ->assertJsonStructure([
                'articles' => [[
                    'title',
                    'slug',
                    'seo_title',
                    'meta_description',
                    'meta_image_path',
                    'lead',
                    'tags',
                    'sections',
                ]],
            ]);
    }

    public function test_article_generator_can_use_openai_structured_outputs(): void
    {
        config([
            'services.openai.article_provider' => 'openai',
            'services.openai.api_key' => 'test-openai-key',
            'services.openai.base_url' => 'https://api.openai.test/v1',
            'services.openai.model' => 'gpt-5-mini',
        ]);

        Http::fake([
            'api.openai.test/v1/responses' => Http::response([
                'output_text' => json_encode([
                    'articles' => [[
                        'title' => 'SMS A2P bancaire: guide operationnel',
                        'slug' => 'sms-a2p-bancaire-guide-operationnel',
                        'section' => 'blog',
                        'locale' => 'fr',
                        'seo_title' => 'SMS A2P bancaire: guide operationnel',
                        'meta_description' => 'Un guide concret pour securiser les flux OTP, ameliorer la delivrabilite et suivre les marges des campagnes bancaires.',
                        'eyebrow' => 'Blog',
                        'lead' => 'Les banques ont besoin de flux OTP rapides, visibles et rentables pour proteger les parcours clients.',
                        'author' => 'Dream Digital',
                        'reading_time' => '7 min',
                        'tags' => ['SMS A2P', 'OTP', 'Banking'],
                        'meta_image_path' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1600&q=80',
                        'image_alt' => 'Equipe analysant des flux SMS bancaires',
                        'image_credit' => 'Photo Unsplash',
                        'image_source_url' => 'https://unsplash.com/',
                        'sections' => [
                            [
                                'heading' => 'Prioriser les flux critiques',
                                'body' => 'Commencez par les OTP de connexion, de paiement et de validation compte.',
                                'body_html' => '<p>Commencez par les OTP de connexion, de paiement et de validation compte.</p>',
                            ],
                            [
                                'heading' => 'Mesurer la delivrabilite',
                                'body' => 'Suivez les taux de livraison, les erreurs operateurs et les temps de reception.',
                                'body_html' => '<p>Suivez les taux de livraison, les erreurs operateurs et les temps de reception.</p>',
                            ],
                            [
                                'heading' => 'Optimiser les routes',
                                'body' => 'Comparez cout, qualite et fallback par destination.',
                                'body_html' => '<p>Comparez cout, qualite et fallback par destination.</p>',
                            ],
                            [
                                'heading' => 'Aligner business et operations',
                                'body' => 'Reliez marge, incidents et satisfaction client dans le meme tableau de bord.',
                                'body_html' => '<p>Reliez marge, incidents et satisfaction client dans le meme tableau de bord.</p>',
                            ],
                        ],
                    ]],
                ], JSON_THROW_ON_ERROR),
            ]),
        ]);

        $this->postJson(route('admin.pages.generate-article'), [
            'idea' => 'SMS A2P pour banques africaines',
            'keywords' => 'SMS A2P, OTP, banking',
            'guidelines' => 'Inclure un angle conversion et support',
            'locale' => 'fr',
            'variants' => 1,
        ])
            ->assertOk()
            ->assertJsonCount(1, 'articles')
            ->assertJsonPath('articles.0.title', 'SMS A2P bancaire: guide operationnel')
            ->assertJsonPath('articles.0.section', 'blog');

        Http::assertSent(fn ($request) => $request->url() === 'https://api.openai.test/v1/responses'
            && $request->hasHeader('Authorization', 'Bearer test-openai-key')
            && data_get($request->data(), 'text.format.type') === 'json_schema'
            && data_get($request->data(), 'text.format.strict') === true);
    }
}
