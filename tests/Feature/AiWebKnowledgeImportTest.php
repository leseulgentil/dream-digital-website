<?php

namespace Tests\Feature;

use App\Models\AiKnowledgeChunk;
use App\Models\AiKnowledgeSource;
use App\Models\AiKnowledgeWebSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiWebKnowledgeImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_register_sitemap_source_and_import_draft_chunks(): void
    {
        Http::fake([
            'https://esimzone.test/sitemap.xml' => Http::response($this->sitemap([
                'https://esimzone.test/fr/faq',
                'https://esimzone.test/fr/activation',
            ]), 200),
            'https://esimzone.test/fr/faq' => Http::response($this->html('FAQ eSIM', 'Les forfaits eSIM Zone couvrent 160 destinations.'), 200),
            'https://esimzone.test/fr/activation' => Http::response($this->html('Activation eSIM', 'Activation en trois minutes apres achat.'), 200),
        ]);

        $this->actingAs(User::factory()->create([
            'role' => User::ROLE_OWNER,
            'is_active' => true,
        ]));

        $response = $this->post(route('admin.ai.web-sources.store'), [
            'title' => 'eSIM Zone',
            'type' => 'sitemap',
            'url' => 'https://esimzone.test/sitemap.xml',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'esim',
            'frequency' => 'weekly',
            'import_status' => 'draft',
            'sync_now' => '1',
        ]);

        $response->assertRedirect(route('admin.ai.knowledge.index'));

        $source = AiKnowledgeWebSource::query()->where('title', 'eSIM Zone')->firstOrFail();

        $this->assertSame('sitemap', $source->type);
        $this->assertSame('weekly', $source->frequency);
        $this->assertNotNull($source->last_synced_at);
        $this->assertNotNull($source->next_sync_at);

        $this->assertDatabaseHas('ai_knowledge_sources', [
            'ai_knowledge_web_source_id' => $source->id,
            'source_url' => 'https://esimzone.test/fr/faq',
            'type' => AiKnowledgeSource::TYPE_WEB_SITEMAP,
            'status' => 'draft',
        ]);
        $this->assertDatabaseHas('ai_knowledge_chunks', [
            'title' => 'FAQ eSIM',
            'status' => 'draft',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'esim',
        ]);
        $this->assertSame(2, AiKnowledgeChunk::query()->count());
    }

    public function test_web_source_url_rejects_private_hosts(): void
    {
        $this->actingAs(User::factory()->create([
            'role' => User::ROLE_OWNER,
            'is_active' => true,
        ]));

        $response = $this->from(route('admin.ai.knowledge.index'))->post(route('admin.ai.web-sources.store'), [
            'title' => 'Private URL',
            'type' => 'url',
            'url' => 'http://127.0.0.1/admin',
            'locale' => 'fr',
            'country_code' => 'global',
            'frequency' => 'manual',
            'import_status' => 'draft',
        ]);

        $response
            ->assertRedirect(route('admin.ai.knowledge.index'))
            ->assertSessionHasErrors('url');

        $this->assertDatabaseCount('ai_knowledge_web_sources', 0);
    }

    public function test_due_web_sources_can_be_synced_from_artisan_command(): void
    {
        Http::fake([
            'https://docs.example.com/help' => Http::response($this->html('Help Center', 'Dream Digital source web importee automatiquement.'), 200),
        ]);

        AiKnowledgeWebSource::create([
            'title' => 'Docs',
            'type' => 'url',
            'url' => 'https://docs.example.com/help',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'docs',
            'frequency' => 'weekly',
            'import_status' => 'draft',
            'status' => 'active',
            'next_sync_at' => now()->subMinute(),
        ]);

        $this->artisan('dd:sync-ai-web-sources')
            ->expectsOutputToContain('Synced 1 web source')
            ->assertExitCode(0);

        $this->assertDatabaseHas('ai_knowledge_chunks', [
            'title' => 'Help Center',
            'content' => 'Help Center Dream Digital source web importee automatiquement.',
            'status' => 'draft',
        ]);
    }

    public function test_endpoint_json_source_imports_validated_items(): void
    {
        Http::fake([
            'https://esimzone.test/api/ai-knowledge/export' => Http::response([
                'source' => 'esimzone',
                'version' => '1.0',
                'items' => [
                    [
                        'external_id' => 'faq-activation-fr',
                        'title' => 'Activer une eSIM',
                        'locale' => 'fr',
                        'country' => 'global',
                        'category' => 'support',
                        'canonical_url' => 'https://esimzone.test/fr/faq/activation',
                        'updated_at' => '2026-05-15T00:00:00Z',
                        'content_hash' => 'hash-from-esimzone',
                        'content_markdown' => 'Installez le QR code puis activez les donnees mobiles.',
                    ],
                ],
            ], 200),
        ]);

        $this->actingAs(User::factory()->create([
            'role' => User::ROLE_OWNER,
            'is_active' => true,
        ]));

        $this->post(route('admin.ai.web-sources.store'), [
            'title' => 'eSIM Zone API',
            'type' => 'endpoint_json',
            'url' => 'https://esimzone.test/api/ai-knowledge/export',
            'locale' => 'fr',
            'country_code' => 'global',
            'frequency' => 'weekly',
            'import_status' => 'draft',
            'sync_now' => '1',
        ])->assertRedirect(route('admin.ai.knowledge.index'));

        $webSource = AiKnowledgeWebSource::query()->where('title', 'eSIM Zone API')->firstOrFail();

        $this->assertDatabaseHas('ai_knowledge_sources', [
            'ai_knowledge_web_source_id' => $webSource->id,
            'source_url' => 'https://esimzone.test/fr/faq/activation',
            'content_hash' => 'hash-from-esimzone',
        ]);
        $this->assertDatabaseHas('ai_knowledge_chunks', [
            'title' => 'Activer une eSIM',
            'content' => 'Installez le QR code puis activez les donnees mobiles.',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'support',
            'status' => 'draft',
        ]);
    }

    /**
     * @param  array<int, string>  $urls
     */
    private function sitemap(array $urls): string
    {
        $items = collect($urls)
            ->map(fn (string $url): string => "<url><loc>{$url}</loc></url>")
            ->implode('');

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">{$items}</urlset>";
    }

    private function html(string $title, string $body): string
    {
        return "<!doctype html><html><head><title>{$title}</title></head><body><nav>Menu</nav><h1>{$title}</h1><main><p>{$body}</p></main><footer>Footer</footer></body></html>";
    }
}
