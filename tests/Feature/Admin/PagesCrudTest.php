<?php

namespace Tests\Feature\Admin;

use App\Models\Country;
use App\Models\Page;
use App\Models\User;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('Editeur riche des sections')
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

        $this->put(route('admin.pages.update', $page), $payload)
            ->assertRedirect(route('admin.pages.index'));

        $page->refresh();
        $this->assertSame('Mentions modifiees', $page->title);
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
            'sections_json' => json_encode([
                ['heading' => 'Section 1', 'body' => "Body 1 first paragraph.\n\nBody 1 second paragraph."],
                ['heading' => 'Section 2', 'body' => 'Body 2.'],
            ]),
            'is_published' => '1',
        ];
    }
}
