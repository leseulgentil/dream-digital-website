<?php

namespace Tests\Feature;

use App\Models\AiKnowledgeChunk;
use App\Models\AiKnowledgeSource;
use App\Models\AiKnowledgeWebSource;
use App\Models\User;
use App\Services\Ai\AiWebKnowledgeImporter;
use Database\Seeders\AiWebSourceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
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
            'auth_token' => 'secret-token',
            'sync_now' => '1',
        ])->assertRedirect(route('admin.ai.knowledge.index'));

        Http::assertSent(fn ($request): bool => $request->hasHeader('Authorization', 'Bearer secret-token'));

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

    public function test_endpoint_json_source_follows_pagination_and_keeps_esimzone_metadata(): void
    {
        Http::fake([
            'https://esimzone.test/api/v1/ai-knowledge/export?locale=fr&page=1&per_page=50' => Http::response([
                'items' => [[
                    'external_id' => 'offer-france-1gb-fr',
                    'title' => 'Forfait eSIM France - 1 Go - 7 jours',
                    'locale' => 'fr',
                    'country' => 'FRA',
                    'destination_country' => 'FRA',
                    'audience_country' => 'global',
                    'category' => 'offer',
                    'canonical_url' => 'https://esimzone.test/fr/packages?country=FRA',
                    'updated_at' => '2026-06-04T10:15:00+00:00',
                    'status' => 'active',
                    'deleted_at' => null,
                    'expires_at' => '2026-12-31T23:59:59+00:00',
                    'content_hash' => 'sha256:'.str_repeat('a', 64),
                    'content_markdown' => 'Offre France 1 Go valable sept jours.',
                ]],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 2,
                    'per_page' => 50,
                    'total' => 2,
                ],
                'links' => [
                    'next' => 'https://esimzone.test/api/v1/ai-knowledge/export?locale=fr&page=2&per_page=50',
                ],
            ], 200),
            'https://esimzone.test/api/v1/ai-knowledge/export?locale=fr&page=2&per_page=50' => Http::response([
                'items' => [[
                    'external_id' => 'destination-france-fr',
                    'title' => 'Destination France',
                    'locale' => 'fr',
                    'country' => 'FRA',
                    'destination_country' => 'FRA',
                    'audience_country' => 'global',
                    'category' => 'destination',
                    'canonical_url' => 'https://esimzone.test/fr/destinations/france',
                    'updated_at' => '2026-06-04T11:15:00+00:00',
                    'status' => 'active',
                    'deleted_at' => null,
                    'expires_at' => null,
                    'content_hash' => str_repeat('b', 64),
                    'content_markdown' => 'Resume destination France pour les voyageurs.',
                ]],
                'meta' => [
                    'current_page' => 2,
                    'last_page' => 2,
                    'per_page' => 50,
                    'total' => 2,
                ],
                'links' => [
                    'next' => null,
                ],
            ], 200),
        ]);

        $webSource = AiKnowledgeWebSource::create([
            'title' => 'eSIMZone API',
            'type' => AiKnowledgeWebSource::TYPE_ENDPOINT_JSON,
            'url' => 'https://esimzone.test/api/v1/ai-knowledge/export?locale=fr&page=1&per_page=50',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'esim',
            'frequency' => 'manual',
            'import_status' => 'published',
            'status' => AiKnowledgeWebSource::STATUS_ACTIVE,
        ]);

        app(AiWebKnowledgeImporter::class)->sync($webSource);

        Http::assertSentCount(2);
        $this->assertSame(2, AiKnowledgeChunk::query()->count());

        $source = AiKnowledgeSource::query()
            ->where('source_url', 'https://esimzone.test/fr/packages?country=FRA')
            ->firstOrFail();

        $this->assertSame(str_repeat('a', 64), $source->content_hash);
        $this->assertSame('offer-france-1gb-fr', $source->metadata['external_id']);
        $this->assertSame('FRA', $source->metadata['destination_country']);
        $this->assertSame('global', $source->metadata['audience_country']);
        $this->assertSame('active', $source->metadata['status']);

        $chunk = $source->chunks()->firstOrFail();
        $this->assertSame('global', $chunk->country_code);
        $this->assertSame('offer', $chunk->category);
        $this->assertNotNull($chunk->expires_at);
    }

    public function test_endpoint_json_source_deletes_stale_item_when_export_marks_it_deleted(): void
    {
        $webSource = AiKnowledgeWebSource::create([
            'title' => 'eSIMZone API',
            'type' => AiKnowledgeWebSource::TYPE_ENDPOINT_JSON,
            'url' => 'https://esimzone.test/api/v1/ai-knowledge/export',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'esim',
            'frequency' => 'manual',
            'import_status' => 'published',
            'status' => AiKnowledgeWebSource::STATUS_ACTIVE,
        ]);

        $source = AiKnowledgeSource::create([
            'ai_knowledge_web_source_id' => $webSource->id,
            'type' => AiKnowledgeSource::TYPE_WEB_ENDPOINT,
            'title' => 'Old offer',
            'source_url' => 'https://esimzone.test/fr/packages?country=FRA',
            'content_hash' => str_repeat('c', 64),
            'locale' => 'fr',
            'country_code' => 'global',
            'status' => 'published',
        ]);
        $source->chunks()->create([
            'title' => 'Old offer',
            'content' => 'Ancienne offre a supprimer.',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'offer',
            'status' => 'published',
        ]);

        Http::fake([
            'https://esimzone.test/api/v1/ai-knowledge/export' => Http::response([
                'items' => [[
                    'external_id' => 'offer-france-1gb-fr',
                    'title' => 'Forfait eSIM France - 1 Go - 7 jours',
                    'locale' => 'fr',
                    'category' => 'offer',
                    'canonical_url' => 'https://esimzone.test/fr/packages?country=FRA',
                    'status' => 'deleted',
                    'deleted_at' => '2026-06-04T10:15:00+00:00',
                ]],
                'links' => ['next' => null],
            ], 200),
        ]);

        app(AiWebKnowledgeImporter::class)->sync($webSource);

        $this->assertDatabaseMissing('ai_knowledge_sources', [
            'id' => $source->id,
        ]);
        $this->assertSame(0, AiKnowledgeChunk::query()->count());
    }

    public function test_endpoint_json_source_uses_seeded_destination_metadata_when_item_omits_it(): void
    {
        Http::fake([
            'https://esimzone.test/api/v1/ai-knowledge/export?locale=fr&category=offer&country=COD&page=1&per_page=50' => Http::response([
                'items' => [[
                    'external_id' => 'offer-cod-1gb-fr',
                    'title' => 'Forfait eSIM RDC - 1 Go',
                    'locale' => 'fr',
                    'canonical_url' => 'https://esimzone.test/fr/packages/cod-1gb',
                    'status' => 'active',
                    'content_hash' => 'sha256:'.str_repeat('d', 64),
                    'content_markdown' => 'Offre RDC 1 Go pour validation metadata fallback.',
                ]],
                'links' => ['next' => null],
            ], 200),
        ]);

        $webSource = AiKnowledgeWebSource::create([
            'title' => 'eSIMZone API FR offer COD',
            'type' => AiKnowledgeWebSource::TYPE_ENDPOINT_JSON,
            'url' => 'https://esimzone.test/api/v1/ai-knowledge/export?locale=fr&category=offer&country=COD&page=1&per_page=50',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'esim',
            'frequency' => 'manual',
            'import_status' => 'published',
            'status' => AiKnowledgeWebSource::STATUS_ACTIVE,
            'metadata' => [
                'destination_country' => 'COD',
                'audience_country' => 'global',
                'endpoint_category' => 'offer',
            ],
        ]);

        app(AiWebKnowledgeImporter::class)->sync($webSource);

        $source = AiKnowledgeSource::query()
            ->where('source_url', 'https://esimzone.test/fr/packages/cod-1gb')
            ->firstOrFail();

        $this->assertSame('COD', $source->metadata['destination_country']);
        $this->assertSame('global', $source->metadata['audience_country']);
        $this->assertSame('offer', $source->metadata['category']);

        $chunk = $source->chunks()->firstOrFail();
        $this->assertSame('offer', $chunk->category);
        $this->assertSame('global', $chunk->country_code);
    }

    public function test_esimzone_endpoint_source_can_be_seeded_from_configuration(): void
    {
        config([
            'dream-digital.ai.web_sources.esimzone.enabled' => true,
            'dream-digital.ai.web_sources.esimzone.title' => 'eSIMZone API',
            'dream-digital.ai.web_sources.esimzone.url' => 'https://esimzone.fr/api/ai-knowledge/export',
            'dream-digital.ai.web_sources.esimzone.auth_token' => 'esimzone-secret',
            'dream-digital.ai.web_sources.esimzone.locale' => 'fr',
            'dream-digital.ai.web_sources.esimzone.country_code' => 'global',
            'dream-digital.ai.web_sources.esimzone.category' => 'esim',
            'dream-digital.ai.web_sources.esimzone.frequency' => 'weekly',
            'dream-digital.ai.web_sources.esimzone.import_status' => 'draft',
        ]);

        $this->seed(AiWebSourceSeeder::class);
        $this->seed(AiWebSourceSeeder::class);

        $source = AiKnowledgeWebSource::query()
            ->where('url', 'https://esimzone.fr/api/ai-knowledge/export')
            ->firstOrFail();

        $this->assertSame(1, AiKnowledgeWebSource::query()->count());
        $this->assertSame('eSIMZone API', $source->title);
        $this->assertSame(AiKnowledgeWebSource::TYPE_ENDPOINT_JSON, $source->type);
        $this->assertSame(AiKnowledgeWebSource::FREQUENCY_WEEKLY, $source->frequency);
        $this->assertSame('draft', $source->import_status);
        $this->assertSame('esim', $source->category);
        $this->assertNotNull($source->next_sync_at);
        $this->assertSame('esimzone-secret', Crypt::decryptString($source->metadata['auth_token']));
    }

    public function test_esimzone_endpoint_sources_can_be_seeded_for_configured_destination_countries(): void
    {
        config([
            'dream-digital.ai.web_sources.esimzone.enabled' => true,
            'dream-digital.ai.web_sources.esimzone.title' => 'eSIMZone API',
            'dream-digital.ai.web_sources.esimzone.url' => 'https://staging.esimzone.fr/api/v1/ai-knowledge/export?locale=fr&page=1&per_page=50',
            'dream-digital.ai.web_sources.esimzone.auth_token' => 'esimzone-secret',
            'dream-digital.ai.web_sources.esimzone.locales' => ['fr', 'en'],
            'dream-digital.ai.web_sources.esimzone.categories' => ['offer', 'destination'],
            'dream-digital.ai.web_sources.esimzone.destination_countries' => ['COD', 'FRA'],
            'dream-digital.ai.web_sources.esimzone.per_page' => 200,
            'dream-digital.ai.web_sources.esimzone.country_code' => 'global',
            'dream-digital.ai.web_sources.esimzone.frequency' => 'weekly',
            'dream-digital.ai.web_sources.esimzone.import_status' => 'published',
        ]);

        $this->seed(AiWebSourceSeeder::class);
        $this->seed(AiWebSourceSeeder::class);

        $this->assertSame(8, AiKnowledgeWebSource::query()->count());

        $source = AiKnowledgeWebSource::query()
            ->where('url', 'https://staging.esimzone.fr/api/v1/ai-knowledge/export?locale=fr&page=1&per_page=200&category=offer&country=COD')
            ->firstOrFail();

        $this->assertSame('eSIMZone API FR offer COD', $source->title);
        $this->assertSame('global', $source->country_code);
        $this->assertSame('offer', $source->category);
        $this->assertSame('published', $source->import_status);
        $this->assertSame('COD', $source->metadata['destination_country']);
        $this->assertSame('global', $source->metadata['audience_country']);
        $this->assertSame('esimzone-secret', Crypt::decryptString($source->metadata['auth_token']));
    }

    public function test_esimzone_seeder_pauses_obsolete_generated_sources(): void
    {
        config([
            'dream-digital.ai.web_sources.esimzone.enabled' => true,
            'dream-digital.ai.web_sources.esimzone.title' => 'eSIMZone API',
            'dream-digital.ai.web_sources.esimzone.url' => 'https://staging.esimzone.fr/api/v1/ai-knowledge/export?locale=fr&page=1&per_page=50',
            'dream-digital.ai.web_sources.esimzone.auth_token' => 'esimzone-secret',
            'dream-digital.ai.web_sources.esimzone.locales' => ['fr', 'en'],
            'dream-digital.ai.web_sources.esimzone.categories' => ['offer', 'destination'],
            'dream-digital.ai.web_sources.esimzone.destination_countries' => ['COD'],
            'dream-digital.ai.web_sources.esimzone.per_page' => 200,
            'dream-digital.ai.web_sources.esimzone.country_code' => 'global',
            'dream-digital.ai.web_sources.esimzone.frequency' => 'weekly',
            'dream-digital.ai.web_sources.esimzone.import_status' => 'draft',
        ]);

        $obsolete = AiKnowledgeWebSource::create([
            'title' => 'eSIMZone API',
            'type' => AiKnowledgeWebSource::TYPE_ENDPOINT_JSON,
            'url' => 'https://staging.esimzone.fr/api/v1/ai-knowledge/export?locale=fr&page=1&per_page=50',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'esim',
            'frequency' => AiKnowledgeWebSource::FREQUENCY_WEEKLY,
            'import_status' => 'draft',
            'status' => AiKnowledgeWebSource::STATUS_ACTIVE,
            'next_sync_at' => now()->subMinute(),
            'last_error' => 'Previous failure',
        ]);

        $this->seed(AiWebSourceSeeder::class);

        $this->assertSame(AiKnowledgeWebSource::STATUS_PAUSED, $obsolete->refresh()->status);
        $this->assertNull($obsolete->next_sync_at);
        $this->assertNull($obsolete->last_error);
        $this->assertSame(4, AiKnowledgeWebSource::query()->where('status', AiKnowledgeWebSource::STATUS_ACTIVE)->count());

        $this->assertDatabaseHas('ai_knowledge_web_sources', [
            'title' => 'eSIMZone API FR offer COD',
            'url' => 'https://staging.esimzone.fr/api/v1/ai-knowledge/export?locale=fr&page=1&per_page=200&category=offer&country=COD',
            'status' => AiKnowledgeWebSource::STATUS_ACTIVE,
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
