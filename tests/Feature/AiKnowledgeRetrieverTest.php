<?php

namespace Tests\Feature;

use App\Services\Ai\AiKnowledgeRetriever;
use Tests\TestCase;

class AiKnowledgeRetrieverTest extends TestCase
{
    public function test_significant_terms_ignore_question_fillers_without_allowing_empty_searches(): void
    {
        $retriever = new AiKnowledgeRetriever();

        $this->assertSame(['pays', 'couvrez'], $retriever->significantTerms('Quels pays couvrez-vous ?'));
        $this->assertSame([], $retriever->significantTerms('?? -- et vous ?'));
    }

    public function test_postgres_websearch_query_uses_or_between_significant_terms(): void
    {
        $retriever = new AiKnowledgeRetriever();

        $this->assertSame('pays OR couvrez', $retriever->postgresWebsearchQuery('Quels pays couvrez-vous ?'));
        $this->assertSame('', $retriever->postgresWebsearchQuery('?? -- et vous ?'));
    }
}
