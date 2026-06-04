<?php

return [
    'rag' => [
        'embedding_search_enabled' => env('AI_RAG_EMBEDDING_SEARCH_ENABLED', false),
        'embedding_candidate_limit' => env('AI_RAG_EMBEDDING_CANDIDATE_LIMIT', 200),
    ],

    'web_sources' => [
        'esimzone' => [
            'enabled' => env('ESIMZONE_AI_KNOWLEDGE_ENABLED', false),
            'title' => env('ESIMZONE_AI_KNOWLEDGE_TITLE', 'eSIMZone API'),
            'url' => env('ESIMZONE_AI_KNOWLEDGE_URL'),
            'auth_token' => env('ESIMZONE_AI_KNOWLEDGE_TOKEN'),
            'locale' => env('ESIMZONE_AI_KNOWLEDGE_LOCALE', 'fr'),
            'country_code' => env('ESIMZONE_AI_KNOWLEDGE_COUNTRY', 'global'),
            'category' => env('ESIMZONE_AI_KNOWLEDGE_CATEGORY', 'esim'),
            'frequency' => env('ESIMZONE_AI_KNOWLEDGE_FREQUENCY', 'weekly'),
            'import_status' => env('ESIMZONE_AI_KNOWLEDGE_IMPORT_STATUS', 'draft'),
        ],
    ],
];
