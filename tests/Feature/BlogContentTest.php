<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Database\Seeders\BlogContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_seeder_creates_ten_articles_per_locale(): void
    {
        $this->seed(BlogContentSeeder::class);

        $this->assertSame(10, Page::where('section', 'blog')->where('locale', 'fr')->count());
        $this->assertSame(10, Page::where('section', 'blog')->where('locale', 'en')->count());
    }

    public function test_blog_index_renders_seeded_articles(): void
    {
        $this->seed(BlogContentSeeder::class);

        $this->get('/fr/blog')
            ->assertOk()
            ->assertSee('Blog telecom B2B et CPaaS')
            ->assertSee('SMS A2P et OTP en Afrique francophone');
    }

    public function test_blog_article_renders_personalized_seo(): void
    {
        $this->seed(BlogContentSeeder::class);

        $response = $this->get('/fr/blog/sms-a2p-otp-afrique-francophone');

        $response
            ->assertOk()
            ->assertSee('SMS A2P et OTP en Afrique francophone')
            ->assertSee('Guide SEO Dream Digital sur la delivrabilite SMS A2P', false)
            ->assertSee('<meta property="og:type" content="article"', false)
            ->assertSee('Photo Unsplash / Markus Stickling');
    }

    public function test_seeded_blog_articles_have_finalized_seo_images_and_rich_sections(): void
    {
        $this->seed(BlogContentSeeder::class);

        Page::where('section', 'blog')->each(function (Page $page) {
            $blocks = $page->content_blocks ?? [];

            $this->assertNotEmpty($page->meta_description);
            $this->assertStringStartsWith('https://images.unsplash.com/', $page->meta_image_path);
            $this->assertGreaterThanOrEqual(5, count($blocks['sections'] ?? []));
            $this->assertNotEmpty($blocks['seo_focus_keywords'] ?? []);

            foreach ($blocks['sections'] ?? [] as $section) {
                $this->assertNotEmpty($section['heading'] ?? null);
                $this->assertNotEmpty($section['body_html'] ?? null);
            }
        });
    }

    public function test_seeded_blog_article_is_editable_from_admin_pages(): void
    {
        $this->seed(BlogContentSeeder::class);
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));

        $page = Page::where('section', 'blog')
            ->where('locale', 'fr')
            ->where('slug', 'sms-a2p-otp-afrique-francophone')
            ->firstOrFail();

        $this->get(route('admin.pages.edit', $page))
            ->assertOk()
            ->assertSee('Titre SEO personnalise')
            ->assertSee('Tags');

        $payload = $this->formPayload($page);
        $payload['title'] = 'Article SEO modifie';
        $payload['seo_title'] = 'SEO Blog modifie Dream Digital';
        $payload['tags'] = 'SMS A2P, Test CMS';

        $this->put(route('admin.pages.update', $page), $payload)
            ->assertRedirect(route('admin.pages.index'));

        $page->refresh();
        $this->assertSame('Article SEO modifie', $page->title);
        $this->assertSame('SEO Blog modifie Dream Digital', $page->content_blocks['seo_title']);
        $this->assertSame(['SMS A2P', 'Test CMS'], $page->content_blocks['tags']);
    }

    public function test_blog_article_renders_rich_section_html(): void
    {
        Page::create([
            'slug' => 'rich-body',
            'section' => 'blog',
            'locale' => 'fr',
            'title' => 'Article riche',
            'meta_description' => 'Description riche',
            'content_blocks' => [
                'lead' => 'Lead riche',
                'sections' => [
                    [
                        'heading' => 'Bloc riche',
                        'body' => 'Texte riche',
                        'body_html' => '<p><strong>Texte riche</strong></p><ul><li>Point SEO</li></ul>',
                    ],
                ],
            ],
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->get('/fr/blog/rich-body')
            ->assertOk()
            ->assertSee('<strong>Texte riche</strong>', false)
            ->assertSee('<li>Point SEO</li>', false);
    }

    private function formPayload(Page $page): array
    {
        $blocks = $page->content_blocks ?? [];

        return [
            'slug' => $page->slug,
            'section' => $page->section,
            'locale' => $page->locale,
            'country_id' => '',
            'title' => $page->title,
            'seo_title' => $blocks['seo_title'] ?? '',
            'meta_description' => $page->meta_description,
            'meta_image_path' => $page->meta_image_path,
            'eyebrow' => $blocks['eyebrow'] ?? '',
            'author' => $blocks['author'] ?? '',
            'reading_time' => $blocks['reading_time'] ?? '',
            'tags' => implode(', ', $blocks['tags'] ?? []),
            'image_alt' => $blocks['image_alt'] ?? '',
            'image_credit' => $blocks['image_credit'] ?? '',
            'image_source_url' => $blocks['image_source_url'] ?? '',
            'lead' => $blocks['lead'] ?? '',
            'last_updated' => $blocks['last_updated'] ?? '',
            'sections_json' => json_encode($blocks['sections'] ?? []),
            'is_published' => '1',
        ];
    }
}
