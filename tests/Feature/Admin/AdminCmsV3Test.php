<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminCmsV3Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));
    }

    public function test_admin_can_upload_local_page_image(): void
    {
        $response = $this->post(route('admin.pages.store'), array_merge($this->payload(), [
            'image_file' => UploadedFile::fake()->image('cover.jpg', 1200, 630),
        ]));

        $response->assertRedirect(route('admin.pages.index'));

        $page = Page::firstOrFail();
        $this->assertStringStartsWith('/img/cms/pages/', $page->meta_image_path);
        $this->assertFileExists(public_path(ltrim($page->meta_image_path, '/')));

        File::delete(public_path(ltrim($page->meta_image_path, '/')));
    }

    public function test_admin_can_duplicate_page_to_other_locale_as_draft(): void
    {
        $page = Page::create($this->modelPayload());

        $this->post(route('admin.pages.duplicate-locale', $page), ['target_locale' => 'en'])
            ->assertRedirect();

        $duplicate = Page::where('slug', $page->slug)
            ->where('section', $page->section)
            ->where('locale', 'en')
            ->firstOrFail();

        $this->assertFalse($duplicate->is_published);
        $this->assertNull($duplicate->published_at);
        $this->assertSame('[EN] ' . $page->title, $duplicate->title);
        $this->assertSame($page->content_blocks['sections'], $duplicate->content_blocks['sections']);
    }

    public function test_admin_can_preview_unpublished_blog_article(): void
    {
        $page = Page::create(array_merge($this->modelPayload(), [
            'section' => 'blog',
            'slug' => 'draft-blog',
            'title' => 'Brouillon Blog',
            'is_published' => false,
            'published_at' => null,
        ]));

        $this->get(route('admin.pages.preview', $page))
            ->assertOk()
            ->assertSee('Brouillon Blog')
            ->assertSee('Lead test');
    }

    private function payload(): array
    {
        return [
            'slug' => 'draft-blog',
            'section' => 'blog',
            'locale' => 'fr',
            'country_id' => '',
            'title' => 'Brouillon Blog',
            'seo_title' => 'Preview SEO',
            'meta_description' => 'Description SEO du brouillon blog.',
            'meta_image_path' => '',
            'eyebrow' => 'Blog',
            'author' => 'Dream Digital',
            'reading_time' => '3 min',
            'tags' => 'CMS, Preview',
            'image_alt' => 'Image blog',
            'image_credit' => '',
            'image_source_url' => '',
            'lead' => 'Lead du brouillon blog.',
            'last_updated' => '',
            'sections_json' => json_encode([
                ['heading' => 'Section test', 'body' => 'Contenu test.'],
            ]),
            'is_published' => '1',
        ];
    }

    private function modelPayload(): array
    {
        return [
            'slug' => 'page-test',
            'section' => 'blog',
            'country_id' => null,
            'locale' => 'fr',
            'title' => 'Page test',
            'meta_description' => 'Description SEO',
            'meta_image_path' => null,
            'content_blocks' => [
                'eyebrow' => 'Blog',
                'lead' => 'Lead test',
                'sections' => [
                    ['heading' => 'A', 'body' => 'B'],
                ],
            ],
            'is_published' => true,
            'published_at' => now(),
        ];
    }
}
