<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Database\Seeders\ProductPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCmsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_product_cms_page_overrides_localized_product_hero_and_sections(): void
    {
        Page::create([
            'slug' => 'voice',
            'section' => 'product',
            'locale' => 'en',
            'title' => 'Editable Voice Product Page',
            'meta_description' => 'Editable voice product meta description.',
            'content_blocks' => [
                'seo_title' => 'Editable Voice SEO Title',
                'eyebrow' => 'Voice CMS',
                'lead' => 'Voice page lead written from the WYSIWYG editor.',
                'sections' => [
                    [
                        'heading' => 'Voice routing controlled from admin',
                        'body' => 'Fallback body',
                        'body_html' => '<p><strong>Carrier voice content</strong> managed in CMS.</p>',
                    ],
                ],
            ],
            'is_published' => true,
            'editorial_status' => Page::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $this->get('/en/products/voice')
            ->assertOk()
            ->assertSee('Editable Voice Product Page')
            ->assertSee('Voice CMS')
            ->assertSee('Voice page lead written from the WYSIWYG editor.')
            ->assertSee('Voice routing controlled from admin')
            ->assertSee('<strong>Carrier voice content</strong>', false)
            ->assertSee('Editable Voice SEO Title', false);
    }

    public function test_product_cms_page_is_locale_scoped_and_falls_back_to_config(): void
    {
        Page::create([
            'slug' => 'voice',
            'section' => 'product',
            'locale' => 'fr',
            'title' => 'Titre produit voix FR seulement',
            'content_blocks' => [
                'lead' => 'Lead produit FR seulement',
                'sections' => [],
            ],
            'is_published' => true,
            'editorial_status' => Page::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $this->get('/en/products/voice')
            ->assertOk()
            ->assertDontSee('Titre produit voix FR seulement')
            ->assertDontSee('Lead produit FR seulement')
            ->assertSee('Wholesale voice termination', false);
    }

    public function test_unpublished_product_cms_page_falls_back_to_config(): void
    {
        Page::create([
            'slug' => 'sms-a2p',
            'section' => 'product',
            'locale' => 'fr',
            'title' => 'Produit SMS brouillon',
            'content_blocks' => ['lead' => 'Lead brouillon', 'sections' => []],
            'is_published' => false,
            'editorial_status' => Page::STATUS_DRAFT,
        ]);

        $this->get('/fr/products/sms-a2p')
            ->assertOk()
            ->assertDontSee('Produit SMS brouillon')
            ->assertSee('Latence surveillee');
    }

    public function test_admin_page_form_supports_product_section(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));

        $this->get(route('admin.pages.create'))
            ->assertOk()
            ->assertSee('value="product"', false)
            ->assertSee('Page produit');
    }

    public function test_admin_product_page_edit_view_links_to_public_product_url(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));

        $page = Page::create([
            'slug' => 'voice',
            'section' => 'product',
            'locale' => 'en',
            'title' => 'Voice CMS Admin Link',
            'content_blocks' => ['lead' => 'Editable product page.'],
            'is_published' => true,
            'editorial_status' => Page::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $this->get(route('admin.pages.edit', $page))
            ->assertOk()
            ->assertSee('/en/products/voice', false)
            ->assertSee('Voir page publique');
    }

    public function test_product_detail_blocks_from_cms_override_config_proofs_and_workflow(): void
    {
        Page::create([
            'slug' => 'voice',
            'section' => 'product',
            'locale' => 'en',
            'title' => 'Voice With Structured Blocks',
            'meta_description' => 'Voice structured block meta.',
            'content_blocks' => [
                'lead' => 'Voice structured lead.',
                'sections' => [],
                'product_detail' => [
                    'proofs' => [
                        [
                            'icon' => 'bx-broadcast',
                            'title' => 'Custom SLA proof',
                            'body' => 'Proof edited from the product CMS.',
                        ],
                    ],
                    'workflow' => [
                        [
                            'label' => 'Validate routes',
                            'body' => 'Workflow step edited from Admin.',
                        ],
                    ],
                ],
            ],
            'is_published' => true,
            'editorial_status' => Page::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $this->get('/en/products/voice')
            ->assertOk()
            ->assertSee('Custom SLA proof')
            ->assertSee('Proof edited from the product CMS.')
            ->assertSee('Validate routes')
            ->assertSee('Workflow step edited from Admin.')
            ->assertDontSee('ASR / ACD tracked');
    }

    public function test_admin_can_update_product_detail_json(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));

        $page = Page::create([
            'slug' => 'voice',
            'section' => 'product',
            'locale' => 'en',
            'title' => 'Voice CMS Product Detail',
            'content_blocks' => [
                'lead' => 'Initial lead.',
                'sections' => [],
            ],
            'is_published' => true,
            'editorial_status' => Page::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $this->put(route('admin.pages.update', $page), [
            'slug' => 'voice',
            'section' => 'product',
            'locale' => 'en',
            'country_id' => '',
            'title' => 'Voice CMS Product Detail',
            'editorial_status' => Page::STATUS_PUBLISHED,
            'is_published' => '1',
            'lead' => 'Updated lead.',
            'sections_json' => '[]',
            'faq_json' => '[]',
            'product_detail_json' => json_encode([
                'proofs' => [
                    [
                        'icon' => 'bx-broadcast',
                        'title' => 'Admin custom proof',
                        'body' => 'Admin custom proof body.',
                    ],
                ],
                'workflow' => [
                    [
                        'label' => 'Admin custom workflow',
                        'body' => 'Admin custom workflow body.',
                    ],
                ],
            ]),
        ])->assertRedirect(route('admin.pages.index'));

        $blocks = $page->fresh()->content_blocks;

        $this->assertSame('Admin custom proof', $blocks['product_detail']['proofs'][0]['title']);
        $this->assertSame('Admin custom workflow', $blocks['product_detail']['workflow'][0]['label']);
    }

    public function test_product_page_seeder_creates_editable_bilingual_rows_for_active_services(): void
    {
        $this->seed(ProductPageSeeder::class);

        $this->assertSame(12, Page::where('section', 'product')->count());

        foreach (['sms-a2p', 'voice', 'did', 'sip', 'dialo', 'esim'] as $slug) {
            $this->assertDatabaseHas('pages', [
                'section' => 'product',
                'slug' => $slug,
                'locale' => 'fr',
                'is_published' => true,
            ]);
            $this->assertDatabaseHas('pages', [
                'section' => 'product',
                'slug' => $slug,
                'locale' => 'en',
                'is_published' => true,
            ]);
        }
    }

    public function test_admin_ai_generator_can_generate_product_page_content(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));

        $this->postJson(route('admin.pages.generate-article'), [
            'idea' => 'Voice Wholesale for operators',
            'keywords' => 'Voice Wholesale, SIP, ASR, ACD',
            'guidelines' => 'Write for telecom buyers.',
            'locale' => 'en',
            'variants' => 1,
            'target_section' => 'product',
            'target_slug' => 'voice',
        ])
            ->assertOk()
            ->assertJsonPath('articles.0.section', 'product')
            ->assertJsonPath('articles.0.slug', 'voice')
            ->assertJsonPath('articles.0.locale', 'en')
            ->assertJsonCount(1, 'articles')
            ->assertJsonStructure([
                'articles' => [
                    [
                        'title',
                        'seo_title',
                        'meta_description',
                        'eyebrow',
                        'lead',
                        'faq',
                        'sections',
                    ],
                ],
            ]);
    }
}
