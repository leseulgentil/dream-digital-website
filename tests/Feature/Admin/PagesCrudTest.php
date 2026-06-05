<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PagesCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CountrySeeder::class);
        $this->actingAs(User::factory()->create());
    }

    public function test_guest_cannot_access_pages_admin(): void
    {
        auth()->logout();
        $this->get(route('admin.pages.index'))->assertRedirect(route('login'));
    }

    public function test_index_renders(): void
    {
        Page::create($this->validPayload());

        $this->get(route('admin.pages.index'))
            ->assertOk()
            ->assertSee('CMS Eloquent');
    }

    public function test_create_form_renders(): void
    {
        $this->get(route('admin.pages.create'))
            ->assertOk()
            ->assertSee('Nouvelle page')
            ->assertSee('Generate Article')
            ->assertSee('Generer aussi la version EN/FR')
            ->assertSee('Editeur riche des sections')
            ->assertSee('FAQ SEO')
            ->assertSee('Ajouter une question')
            ->assertSee('Sections (JSON avance)');
    }

    public function test_store_creates_page_with_content_blocks(): void
    {
        $payload = $this->formPayload();

        $this->post(route('admin.pages.store'), $payload)
            ->assertRedirect(route('admin.pages.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('pages', [
            'slug' => 'mentions',
            'section' => 'legal',
            'locale' => 'fr',
            'title' => 'Mentions test',
        ]);

        $page = Page::firstWhere('slug', 'mentions');
        $this->assertNotNull($page);
        $this->assertSame('Eyebrow test', $page->content_blocks['eyebrow']);
        $this->assertSame('Lead test paragraph.', $page->content_blocks['lead']);
        $this->assertCount(2, $page->content_blocks['sections']);
        $this->assertSame('Section 1', $page->content_blocks['sections'][0]['heading']);
        $this->assertSame('published', $page->editorial_status);
    }

    public function test_store_can_generate_translated_counterpart_as_draft(): void
    {
        config([
            'services.openai.article_provider' => 'openai',
            'services.openai.api_key' => 'test-openai-key',
            'services.openai.base_url' => 'https://api.openai.test/v1',
        ]);

        Http::fake([
            'https://api.openai.test/v1/responses' => Http::response([
                'output_text' => json_encode([
                    'page' => [
                        'title' => 'English blog title',
                        'seo_title' => 'English SEO title',
                        'meta_description' => 'English SEO description.',
                        'eyebrow' => 'Blog',
                        'lead' => 'English lead paragraph.',
                        'author' => 'Dream Digital',
                        'reading_time' => '4 min read',
                        'tags' => ['CPaaS', 'A2P SMS'],
                        'image_alt' => 'English image alt',
                        'faq' => [
                            ['question' => 'English question?', 'answer' => 'English answer.'],
                        ],
                        'sections' => [
                            [
                                'heading' => 'English section',
                                'body' => 'English body.',
                                'body_html' => '<p>English body.</p>',
                            ],
                        ],
                        'product_detail' => [
                            'proofs' => [],
                            'workflow' => [],
                        ],
                    ],
                ]),
            ], 200),
        ]);

        $payload = $this->formPayload();
        $payload['section'] = 'blog';
        $payload['slug'] = 'article-fr';
        $payload['title'] = 'Article FR';
        $payload['locale'] = 'fr';
        $payload['generate_translation'] = '1';

        $this->post(route('admin.pages.store'), $payload)
            ->assertRedirect(route('admin.pages.index'))
            ->assertSessionHas('status');

        $source = Page::where('slug', 'article-fr')->where('locale', 'fr')->firstOrFail();
        $translation = Page::where('slug', 'article-fr')->where('locale', 'en')->firstOrFail();

        $this->assertTrue($source->is_published);
        $this->assertFalse($translation->is_published);
        $this->assertNull($translation->published_at);
        $this->assertSame(Page::STATUS_DRAFT, $translation->editorial_status);
        $this->assertSame('English blog title', $translation->title);
        $this->assertSame('English SEO description.', $translation->meta_description);
        $this->assertSame('English lead paragraph.', $translation->content_blocks['lead']);
        $this->assertSame('English section', $translation->content_blocks['sections'][0]['heading']);
        $this->assertStringContainsString('Traduction IA', $translation->review_notes);

        Http::assertSent(fn ($request): bool => $request->hasHeader('Authorization', 'Bearer test-openai-key'));
    }

    public function test_store_can_generate_translated_product_detail_blocks_as_draft(): void
    {
        config([
            'services.openai.article_provider' => 'openai',
            'services.openai.api_key' => 'test-openai-key',
            'services.openai.base_url' => 'https://api.openai.test/v1',
        ]);

        Http::fake([
            'https://api.openai.test/v1/responses' => Http::response([
                'output_text' => json_encode([
                    'page' => [
                        'title' => 'Voice product page',
                        'seo_title' => 'Voice product SEO',
                        'meta_description' => 'English product description.',
                        'eyebrow' => 'Voice',
                        'lead' => 'English product lead.',
                        'author' => 'Dream Digital',
                        'reading_time' => 'Draft',
                        'tags' => ['Voice'],
                        'image_alt' => 'Voice product image',
                        'faq' => [],
                        'sections' => [],
                        'product_detail' => [
                            'proofs' => [
                                ['icon' => 'bx-shield', 'title' => 'Carrier-grade routing', 'body' => 'Quality routes with supervision.'],
                            ],
                            'workflow' => [
                                ['label' => 'Qualification', 'body' => 'We validate countries, volume and SLA.'],
                            ],
                        ],
                    ],
                ]),
            ], 200),
        ]);

        $payload = $this->formPayload();
        $payload['section'] = 'product';
        $payload['slug'] = 'voice';
        $payload['title'] = 'Page produit voix';
        $payload['locale'] = 'fr';
        $payload['generate_translation'] = '1';
        $payload['product_detail_json'] = json_encode([
            'proofs' => [
                ['icon' => 'bx-shield', 'title' => 'Routage carrier-grade', 'body' => 'Routes qualite avec supervision.'],
            ],
            'workflow' => [
                ['label' => 'Qualification', 'body' => 'Nous validons les pays, volumes et SLA.'],
            ],
        ]);

        $this->post(route('admin.pages.store'), $payload)
            ->assertRedirect(route('admin.pages.index'));

        $translation = Page::where('slug', 'voice')->where('locale', 'en')->firstOrFail();

        $this->assertFalse($translation->is_published);
        $this->assertSame('Carrier-grade routing', $translation->content_blocks['product_detail']['proofs'][0]['title']);
        $this->assertSame('Quality routes with supervision.', $translation->content_blocks['product_detail']['proofs'][0]['body']);
        $this->assertSame('Qualification', $translation->content_blocks['product_detail']['workflow'][0]['label']);
        $this->assertSame('We validate countries, volume and SLA.', $translation->content_blocks['product_detail']['workflow'][0]['body']);

        Http::assertSent(fn ($request): bool => str_contains((string) data_get($request->data(), 'input.0.content.0.text'), 'Routage carrier-grade'));
    }

    public function test_store_with_default_checked_translation_can_create_local_fallback_draft(): void
    {
        config([
            'services.openai.article_provider' => 'local',
            'services.openai.api_key' => null,
        ]);

        $payload = $this->formPayload();
        $payload['slug'] = 'local-fallback';
        $payload['title'] = 'Article local';
        $payload['generate_translation'] = '1';

        $this->post(route('admin.pages.store'), $payload)
            ->assertRedirect(route('admin.pages.index'))
            ->assertSessionHas('status');

        $translation = Page::where('slug', 'local-fallback')->where('locale', 'en')->firstOrFail();

        $this->assertFalse($translation->is_published);
        $this->assertSame(Page::STATUS_DRAFT, $translation->editorial_status);
        $this->assertSame('[EN draft] Article local', $translation->title);
        $this->assertStringContainsString('Fallback', $translation->review_notes);
    }

    public function test_store_preserves_rich_section_html(): void
    {
        $payload = $this->formPayload();
        $payload['slug'] = 'rich-html';
        $payload['sections_json'] = json_encode([
            [
                'heading' => 'Section riche',
                'body' => 'Texte riche',
                'body_html' => '<p><strong>Texte riche</strong></p><ul><li>Point SEO</li></ul>',
            ],
        ]);

        $this->post(route('admin.pages.store'), $payload)
            ->assertRedirect(route('admin.pages.index'));

        $page = Page::firstWhere('slug', 'rich-html');
        $this->assertSame('<p><strong>Texte riche</strong></p><ul><li>Point SEO</li></ul>', $page->content_blocks['sections'][0]['body_html']);
    }

    public function test_store_preserves_faq_items(): void
    {
        $payload = $this->formPayload();
        $payload['slug'] = 'faq-items';
        $payload['faq_json'] = json_encode([
            ['question' => 'Quelle route choisir ?', 'answer' => 'La route depend du volume et du pays cible.'],
            ['question' => 'WhatsApp est-il supporte ?', 'answer' => 'Oui, via les parcours commerciaux Dream Digital.'],
        ]);

        $this->post(route('admin.pages.store'), $payload)
            ->assertRedirect(route('admin.pages.index'));

        $page = Page::firstWhere('slug', 'faq-items');
        $this->assertCount(2, $page->content_blocks['faq']);
        $this->assertSame('Quelle route choisir ?', $page->content_blocks['faq'][0]['question']);
        $this->assertSame('Oui, via les parcours commerciaux Dream Digital.', $page->content_blocks['faq'][1]['answer']);
    }

    public function test_store_rejects_invalid_slug(): void
    {
        $payload = $this->formPayload();
        $payload['slug'] = 'INVALID Slug!';

        $this->from(route('admin.pages.create'))
            ->post(route('admin.pages.store'), $payload)
            ->assertRedirect(route('admin.pages.create'))
            ->assertSessionHasErrors('slug');
    }

    public function test_store_rejects_invalid_section(): void
    {
        $payload = $this->formPayload();
        $payload['section'] = 'inconnue';

        $this->from(route('admin.pages.create'))
            ->post(route('admin.pages.store'), $payload)
            ->assertRedirect(route('admin.pages.create'))
            ->assertSessionHasErrors('section');
    }

    public function test_store_rejects_invalid_sections_json(): void
    {
        $payload = $this->formPayload();
        $payload['sections_json'] = '{ not valid json';

        $this->from(route('admin.pages.create'))
            ->post(route('admin.pages.store'), $payload)
            ->assertRedirect(route('admin.pages.create'))
            ->assertSessionHasErrors('sections_json');
    }

    public function test_update_modifies_page(): void
    {
        $page = Page::create($this->validPayload());

        $payload = $this->formPayload();
        $payload['title'] = 'Mentions modifiees';
        $payload['editorial_status'] = 'in_review';
        $payload['review_notes'] = 'A relire avant publication.';
        $payload['is_published'] = '0';

        $this->put(route('admin.pages.update', $page), $payload)
            ->assertRedirect(route('admin.pages.index'));

        $page->refresh();
        $this->assertSame('Mentions modifiees', $page->title);
        $this->assertSame('in_review', $page->editorial_status);
        $this->assertSame('A relire avant publication.', $page->review_notes);
        $this->assertSame(auth()->id(), $page->updated_by_id);
    }

    public function test_destroy_removes_page(): void
    {
        $page = Page::create($this->validPayload());

        $this->delete(route('admin.pages.destroy', $page))
            ->assertRedirect(route('admin.pages.index'));

        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }

    public function test_publish_toggle_sets_published_at(): void
    {
        $payload = $this->formPayload();
        $payload['is_published'] = '0';

        $this->post(route('admin.pages.store'), $payload)->assertRedirect();
        $page = Page::firstWhere('slug', 'mentions');
        $this->assertFalse($page->is_published);
        $this->assertNull($page->published_at);

        $payload['is_published'] = '1';
        $this->put(route('admin.pages.update', $page), $payload)->assertRedirect();
        $page->refresh();
        $this->assertTrue($page->is_published);
        $this->assertNotNull($page->published_at);
    }

    private function validPayload(): array
    {
        return [
            'slug' => 'mentions',
            'section' => 'legal',
            'locale' => 'fr',
            'country_id' => null,
            'title' => 'Mentions test',
            'meta_description' => 'desc',
            'content_blocks' => ['sections' => []],
            'is_published' => true,
            'published_at' => now(),
        ];
    }

    private function formPayload(): array
    {
        return [
            'slug' => 'mentions',
            'section' => 'legal',
            'locale' => 'fr',
            'country_id' => '',
            'title' => 'Mentions test',
            'meta_description' => 'desc seo courte',
            'eyebrow' => 'Eyebrow test',
            'lead' => 'Lead test paragraph.',
            'last_updated' => '2026-05-12',
            'editorial_status' => 'draft',
            'review_notes' => '',
            'sections_json' => json_encode([
                ['heading' => 'Section 1', 'body' => "Body 1 first paragraph.\n\nBody 1 second paragraph."],
                ['heading' => 'Section 2', 'body' => 'Body 2.'],
            ]),
            'is_published' => '1',
        ];
    }
}
