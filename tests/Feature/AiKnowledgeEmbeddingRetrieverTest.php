<?php

namespace Tests\Feature;

use App\Models\AiKnowledgeChunk;
use App\Models\AiKnowledgeSource;
use App\Services\Ai\AiKnowledgeRetriever;
use App\Services\Ai\AiTextEmbedding;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiKnowledgeEmbeddingRetrieverTest extends TestCase
{
    use RefreshDatabase;

    public function test_embedding_search_can_retrieve_pre_embedded_chunks_when_lexical_search_cannot(): void
    {
        config([
            'database.default' => 'sqlite',
            'dream-digital.ai.rag.embedding_search_enabled' => true,
        ]);

        $embedding = app(AiTextEmbedding::class)->embed('activation qr esim corporate');

        $source = AiKnowledgeSource::create([
            'type' => AiKnowledgeSource::TYPE_MANUAL,
            'title' => 'Vector source',
            'locale' => 'en',
            'country_code' => 'global',
            'status' => 'published',
        ]);

        $chunk = AiKnowledgeChunk::create([
            'ai_knowledge_source_id' => $source->id,
            'title' => 'Hidden semantic match',
            'content' => 'This text intentionally has no searchable visitor terms.',
            'locale' => 'en',
            'country_code' => 'global',
            'category' => 'esim',
            'status' => 'published',
            'embedding' => $embedding,
            'embedding_model' => AiTextEmbedding::LOCAL_MODEL,
            'embedding_hash' => AiTextEmbedding::hash('activation qr esim corporate'),
            'embedded_at' => now(),
        ]);

        $results = app(AiKnowledgeRetriever::class)->retrieve('activation qr esim corporate', 'en', 'global', 3);

        $this->assertSame([$chunk->id], $results->pluck('id')->all());
    }

    public function test_lexical_match_is_preferred_before_embedding_search(): void
    {
        config([
            'database.default' => 'sqlite',
            'dream-digital.ai.rag.embedding_search_enabled' => true,
        ]);

        $source = AiKnowledgeSource::create([
            'type' => AiKnowledgeSource::TYPE_MANUAL,
            'title' => 'Hybrid source',
            'locale' => 'fr',
            'country_code' => 'global',
            'status' => 'published',
        ]);

        $exact = AiKnowledgeChunk::create([
            'ai_knowledge_source_id' => $source->id,
            'title' => 'eSIM FRA: offres disponibles',
            'content' => 'Dream Digital propose des offres eSIM pour la France via eSIMZone.',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'destination',
            'status' => 'published',
            'priority' => 0,
        ]);

        AiKnowledgeChunk::create([
            'ai_knowledge_source_id' => $source->id,
            'title' => 'Match vectoriel general',
            'content' => 'Ce contenu ne contient volontairement pas les mots visiteurs exacts.',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'faq',
            'status' => 'published',
            'priority' => 100,
            'embedding' => app(AiTextEmbedding::class)->embed('je cherche une eSIM pour la France'),
            'embedding_model' => AiTextEmbedding::LOCAL_MODEL,
            'embedding_hash' => AiTextEmbedding::hash('je cherche une eSIM pour la France'),
            'embedded_at' => now(),
        ]);

        $results = app(AiKnowledgeRetriever::class)->retrieve('je cherche une eSIM pour la France', 'fr', 'global', 3);

        $this->assertSame($exact->id, $results->first()?->id);
    }
}
