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
            'locales' => env('ESIMZONE_AI_KNOWLEDGE_LOCALES'),
            'country_code' => env('ESIMZONE_AI_KNOWLEDGE_COUNTRY', 'global'),
            'category' => env('ESIMZONE_AI_KNOWLEDGE_CATEGORY', 'esim'),
            'categories' => env('ESIMZONE_AI_KNOWLEDGE_CATEGORIES'),
            'destination_countries' => env('ESIMZONE_AI_KNOWLEDGE_DESTINATION_COUNTRIES', env('ESIMZONE_AI_KNOWLEDGE_COUNTRIES')),
            'per_page' => env('ESIMZONE_AI_KNOWLEDGE_PER_PAGE', 50),
            'frequency' => env('ESIMZONE_AI_KNOWLEDGE_FREQUENCY', 'weekly'),
            'import_status' => env('ESIMZONE_AI_KNOWLEDGE_IMPORT_STATUS', 'draft'),
        ],
    ],
];
