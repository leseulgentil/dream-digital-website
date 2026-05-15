<?php

namespace Tests\Feature;

use App\Models\AiKnowledgeChunk;
use App\Models\AiKnowledgeSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAiKnowledgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_manual_knowledge_entry(): void
    {
        $this->actingAs(User::factory()->create([
            'role' => User::ROLE_OWNER,
            'is_active' => true,
        ]));

        $response = $this->post('/admin/ai/knowledge', [
            'title' => 'WhatsApp Support',
            'content' => 'Dream Digital accompagne les besoins WhatsApp Business.',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'support',
            'status' => 'published',
            'priority' => 20,
        ]);

        $response->assertRedirect(route('admin.ai.knowledge.index'));
        $this->assertDatabaseHas('ai_knowledge_chunks', [
            'title' => 'WhatsApp Support',
            'status' => 'published',
            'locale' => 'fr',
        ]);
    }

    public function test_owner_can_import_markdown_file(): void
    {
        Storage::fake('local');

        $this->actingAs(User::factory()->create([
            'role' => User::ROLE_OWNER,
            'is_active' => true,
        ]));

        $response = $this->post('/admin/ai/import', [
            'title' => 'FAQ Import',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'faq',
            'file' => UploadedFile::fake()->createWithContent(
                'faq.md',
                'Dream Digital couvre les besoins CPaaS.',
            ),
        ]);

        $response->assertRedirect(route('admin.ai.knowledge.index'));
        $this->assertDatabaseHas('ai_knowledge_sources', [
            'title' => 'FAQ Import',
            'type' => AiKnowledgeSource::TYPE_MARKDOWN,
        ]);
    }

    public function test_corrupt_pdf_import_redirects_with_file_error(): void
    {
        Storage::fake('local');

        $this->actingAs(User::factory()->create([
            'role' => User::ROLE_OWNER,
            'is_active' => true,
        ]));

        $response = $this->from(route('admin.ai.import.create'))->post('/admin/ai/import', [
            'title' => 'Broken PDF',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'faq',
            'file' => UploadedFile::fake()->createWithContent('broken.pdf', 'not a valid pdf'),
        ]);

        $response
            ->assertRedirect(route('admin.ai.import.create'))
            ->assertSessionHasErrors('file');
        $this->assertDatabaseCount('ai_knowledge_sources', 0);
        $this->assertDatabaseCount('ai_knowledge_chunks', 0);
    }

    public function test_owner_can_publish_and_update_chunk(): void
    {
        $this->actingAs(User::factory()->create([
            'role' => User::ROLE_OWNER,
            'is_active' => true,
        ]));

        $source = AiKnowledgeSource::create([
            'type' => AiKnowledgeSource::TYPE_MANUAL,
            'title' => 'Manual Draft',
            'status' => 'draft',
            'locale' => 'fr',
            'country_code' => 'global',
        ]);
        $chunk = AiKnowledgeChunk::create([
            'ai_knowledge_source_id' => $source->id,
            'title' => 'Draft Chunk',
            'content' => 'Texte brouillon',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'faq',
            'status' => 'draft',
            'priority' => 0,
        ]);

        $response = $this->put("/admin/ai/knowledge/{$chunk->id}", [
            'title' => 'Published Chunk',
            'content' => 'Texte publie',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'faq',
            'status' => 'published',
            'priority' => 5,
        ]);

        $response->assertRedirect(route('admin.ai.knowledge.index'));
        $this->assertSame('published', $chunk->fresh()->status);
    }

    public function test_deleting_last_imported_chunk_removes_stored_source_file(): void
    {
        Storage::fake('local');

        $this->actingAs(User::factory()->create([
            'role' => User::ROLE_OWNER,
            'is_active' => true,
        ]));

        $this->post('/admin/ai/import', [
            'title' => 'FAQ Cleanup',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'faq',
            'file' => UploadedFile::fake()->createWithContent('faq.md', 'Dream Digital CPaaS.'),
        ]);

        $source = AiKnowledgeSource::query()->where('title', 'FAQ Cleanup')->firstOrFail();
        $chunk = $source->chunks()->firstOrFail();
        $storedPath = $source->stored_path;

        Storage::disk('local')->assertExists($storedPath);

        $this->delete("/admin/ai/knowledge/{$chunk->id}")
            ->assertRedirect(route('admin.ai.knowledge.index'));

        Storage::disk('local')->assertMissing($storedPath);
    }

    public function test_viewer_without_manage_permission_cannot_post_import_or_store(): void
    {
        Storage::fake('local');

        $this->actingAs(User::factory()->create([
            'role' => User::ROLE_VIEWER,
            'is_active' => true,
        ]));

        $this->post('/admin/ai/knowledge', [
            'title' => 'Blocked',
            'content' => 'Viewer cannot create this.',
            'locale' => 'fr',
            'country_code' => 'global',
            'status' => 'draft',
        ])->assertForbidden();

        $this->post('/admin/ai/import', [
            'title' => 'Blocked Import',
            'locale' => 'fr',
            'country_code' => 'global',
            'file' => UploadedFile::fake()->createWithContent('faq.md', 'Blocked.'),
        ])->assertForbidden();
    }

    public function test_viewer_can_view_index_without_manage_controls(): void
    {
        $source = AiKnowledgeSource::create([
            'type' => AiKnowledgeSource::TYPE_MANUAL,
            'title' => 'Viewer Source',
            'status' => 'published',
            'locale' => 'fr',
            'country_code' => 'global',
        ]);
        $chunk = AiKnowledgeChunk::create([
            'ai_knowledge_source_id' => $source->id,
            'title' => 'Viewer Chunk',
            'content' => 'Visible to viewer.',
            'locale' => 'fr',
            'country_code' => 'global',
            'status' => 'published',
            'priority' => 0,
        ]);

        $this->actingAs(User::factory()->create([
            'role' => User::ROLE_VIEWER,
            'is_active' => true,
        ]));

        $this->get(route('admin.ai.knowledge.index'))
            ->assertOk()
            ->assertSee('Viewer Chunk')
            ->assertDontSee('Importer')
            ->assertDontSee('Nouvelle entree')
            ->assertDontSee(route('admin.ai.knowledge.edit', $chunk))
            ->assertDontSee(route('admin.ai.knowledge.destroy', $chunk))
            ->assertDontSee('Actions');
    }
}
