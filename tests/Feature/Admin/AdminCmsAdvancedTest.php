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
            ->assertSee($page->meta_image_path)
            ->assertSee('Media library test');

        File::delete(public_path(ltrim($page->meta_image_path, '/')));
    }

    public function test_media_metadata_can_be_updated_and_used_media_cannot_be_deleted(): void
    {
        $file = UploadedFile::fake()->image('cms-locked.jpg', 800, 450);

        $this->post(route('admin.pages.store'), [
            'slug' => 'media-locked',
            'section' => 'blog',
            'locale' => 'fr',
            'title' => 'Media locked',
            'meta_description' => 'Description',
            'lead' => 'Lead',
            'sections_json' => '[]',
            'image_file' => $file,
            'is_published' => '1',
        ])->assertRedirect(route('admin.pages.index'));

        $page = Page::where('slug', 'media-locked')->firstOrFail();
        $this->get(route('admin.media.index'))->assertOk();
        $asset = \App\Models\MediaAsset::where('path', $page->meta_image_path)->firstOrFail();

        $this->put(route('admin.media.update', $asset), [
            'alt_text' => 'Alt media',
            'credit' => 'Credit media',
            'source_url' => 'https://example.test/source',
        ])->assertRedirect(route('admin.media.index'));

        $asset->refresh();
        $this->assertSame('Alt media', $asset->alt_text);
        $this->assertSame('Credit media', $asset->credit);

        $this->delete(route('admin.media.destroy', $asset))
            ->assertRedirect(route('admin.media.index'))
            ->assertSessionHas('error');

        $this->assertFileExists(public_path(ltrim($asset->path, '/')));
        File::delete(public_path(ltrim($asset->path, '/')));
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
            ->assertJsonPath('provider', 'local')
            ->assertJsonPath('fallback_used', false)
            ->assertJsonStructure([
                'articles' => [[
                    'title',
                    'slug',
                    'seo_title',
                    'meta_description',
                    'meta_image_path',
                    'lead',
                    'tags',
                    'faq',
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
                        'faq' => [
                            [
                                'question' => 'Pourquoi surveiller les OTP bancaires ?',
                                'answer' => 'Parce que les OTP influencent directement conversion, support et securite.',
                            ],
                            [
                                'question' => 'Quels KPI suivre ?',
                                'answer' => 'Suivez delivrabilite, latence, erreurs operateur et taux de conversion.',
                            ],
                        ],
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
            ->assertJsonPath('articles.0.section', 'blog')
            ->assertJsonPath('articles.0.faq.0.question', 'Pourquoi surveiller les OTP bancaires ?')
            ->assertJsonPath('provider', 'openai')
            ->assertJsonPath('model', 'gpt-5-mini')
            ->assertJsonPath('fallback_used', false);

        Http::assertSent(fn ($request) => $request->url() === 'https://api.openai.test/v1/responses'
            && $request->hasHeader('Authorization', 'Bearer test-openai-key')
            && data_get($request->data(), 'text.format.type') === 'json_schema'
            && data_get($request->data(), 'text.format.strict') === true);
    }

    public function test_article_generator_reports_local_fallback_when_openai_fails(): void
    {
        config([
            'services.openai.article_provider' => 'openai',
            'services.openai.api_key' => 'test-openai-key',
            'services.openai.base_url' => 'https://api.openai.test/v1',
            'services.openai.fallback_on_failure' => true,
        ]);

        Http::fake([
            'api.openai.test/v1/responses' => Http::response(['error' => ['message' => 'rate limited']], 429),
        ]);

        $this->postJson(route('admin.pages.generate-article'), [
            'idea' => 'SMS A2P pour banques africaines',
            'keywords' => 'SMS A2P, OTP, banking',
            'locale' => 'fr',
            'variants' => 1,
        ])
            ->assertOk()
            ->assertJsonCount(1, 'articles')
            ->assertJsonPath('provider', 'local')
            ->assertJsonPath('fallback_used', true)
            ->assertJsonPath('fallback_reason', 'openai_error');
    }

    public function test_article_generator_can_fail_fast_when_openai_fallback_is_disabled(): void
    {
        config([
            'services.openai.article_provider' => 'openai',
            'services.openai.api_key' => 'test-openai-key',
            'services.openai.base_url' => 'https://api.openai.test/v1',
            'services.openai.fallback_on_failure' => false,
        ]);

        Http::fake([
            'api.openai.test/v1/responses' => Http::response(['error' => ['message' => 'rate limited']], 429),
        ]);

        $this->postJson(route('admin.pages.generate-article'), [
            'idea' => 'SMS A2P pour banques africaines',
            'keywords' => 'SMS A2P, OTP, banking',
            'locale' => 'fr',
            'variants' => 1,
        ])
            ->assertStatus(502)
            ->assertJsonPath('provider', 'openai')
            ->assertJsonPath('fallback_used', false);
    }
}
