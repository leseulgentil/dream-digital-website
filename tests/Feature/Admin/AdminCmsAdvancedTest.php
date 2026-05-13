<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminCmsAdvancedTest extends TestCase
{
    use RefreshDatabase;

    private User $editor;

    protected function setUp(): void
    {
        parent::setUp();

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
}
