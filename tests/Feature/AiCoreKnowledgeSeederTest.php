<?php

namespace Tests\Feature;

use App\Models\AiChatSetting;
use App\Models\AiKnowledgeChunk;
use Database\Seeders\AiCoreKnowledgeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiCoreKnowledgeSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_core_knowledge_seeder_creates_published_bilingual_chunks(): void
    {
        $this->seed(AiCoreKnowledgeSeeder::class);
        $this->seed(AiCoreKnowledgeSeeder::class);

        $this->assertSame(6, AiKnowledgeChunk::query()->where('status', 'published')->count());

        $this->assertDatabaseHas('ai_knowledge_chunks', [
            'title' => 'Services Dream Digital',
            'locale' => 'fr',
            'status' => 'published',
        ]);
        $this->assertDatabaseHas('ai_knowledge_chunks', [
            'title' => 'Dream Digital services',
            'locale' => 'en',
            'status' => 'published',
        ]);
    }

    public function test_core_knowledge_answers_services_quick_question(): void
    {
        config([
            'services.openai.api_key' => 'test-key',
            'services.openai.base_url' => 'https://api.openai.com/v1',
        ]);

        $this->seed(AiCoreKnowledgeSeeder::class);
        AiChatSetting::current()->update(['enabled' => true]);

        Http::fake([
            'api.openai.com/*' => Http::response([
                'output_text' => 'Dream Digital propose SMS Wholesale et Retail, Voice Wholesale et Retail, eSIMZone et DIALO.',
            ], 200),
        ]);

        $response = $this->postJson(route('front.ai-chat.message'), [
            'message' => 'Quels services proposez-vous ?',
            'locale' => 'fr',
            'country_code' => 'global',
            'page_url' => 'https://dreamdigital.example/fr',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('answered', true)
            ->assertJsonPath('sources.0.title', 'Services Dream Digital');
    }
}
