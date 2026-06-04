<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageRichTextSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));
    }

    public function test_store_sanitizes_rich_section_html(): void
    {
        $this->post(route('admin.pages.store'), $this->payload([
            [
                'heading' => 'Section riche',
                'body' => 'Texte riche',
                'body_html' => '<p><strong>Texte riche</strong></p><img src="/img/cms/pages/safe.webp" alt="Safe" onerror="alert(1)"><a href="javascript:alert(1)">Lien piege</a><script>alert(1)</script><ul><li>Point SEO</li></ul>',
            ],
        ]))->assertRedirect(route('admin.pages.index'));

        $page = Page::firstWhere('slug', 'xss-rich');
        $html = $page->content_blocks['sections'][0]['body_html'];

        $this->assertStringContainsString('<strong>Texte riche</strong>', $html);
        $this->assertStringContainsString('<ul><li>Point SEO</li></ul>', $html);
        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringNotContainsString('onerror', $html);
        $this->assertStringNotContainsString('javascript:', $html);
    }

    public function test_public_blog_show_renders_sanitized_html_only(): void
    {
        $this->post(route('admin.pages.store'), $this->payload([
            [
                'heading' => 'Bloc public',
                'body' => 'Contenu public',
                'body_html' => '<p>Contenu public <em>visible</em></p><iframe src="https://evil.test/embed"></iframe><img src=x onerror="alert(1)">',
            ],
        ]))->assertRedirect(route('admin.pages.index'));

        $response = $this->get('/fr/blog/xss-rich')->assertOk();

        $response->assertSee('Contenu public <em>visible</em>', false);
        $response->assertDontSee('<iframe', false);
        $response->assertDontSee('onerror', false);
    }

    public function test_revision_snapshot_contains_sanitized_content(): void
    {
        $this->post(route('admin.pages.store'), $this->payload([
            [
                'heading' => 'Revision',
                'body' => 'Revision',
                'body_html' => '<p>Revision <strong>OK</strong></p><script>alert(1)</script><a href="javascript:alert(1)">Bad</a>',
            ],
        ]))->assertRedirect(route('admin.pages.index'));

        $page = Page::firstWhere('slug', 'xss-rich');
        $revisionHtml = $page->revisions()->firstOrFail()->content_blocks['sections'][0]['body_html'];

        $this->assertStringContainsString('<strong>OK</strong>', $revisionHtml);
        $this->assertStringNotContainsString('<script', $revisionHtml);
        $this->assertStringNotContainsString('javascript:', $revisionHtml);
    }

    private function payload(array $sections): array
    {
        return [
            'slug' => 'xss-rich',
            'section' => 'blog',
            'locale' => 'fr',
            'country_id' => '',
            'title' => 'Article riche securise',
            'meta_description' => 'Description SEO',
            'eyebrow' => 'Blog',
            'lead' => 'Lead test',
            'author' => 'Dream Digital',
            'reading_time' => '3 min',
            'tags' => 'CMS, Security',
            'sections_json' => json_encode($sections),
            'editorial_status' => 'published',
            'is_published' => '1',
        ];
    }
}
