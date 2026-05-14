<?php

return [
    'schemas' => [
        'marketing' => [
            'label' => 'Page marketing',
            'fields' => ['eyebrow', 'lead', 'sections'],
            'sections_example' => [
                ['heading' => 'Titre du bloc', 'body' => "Paragraphe court.\n\nSecond paragraphe si necessaire."],
            ],
        ],
        'blog' => [
            'label' => 'Article blog SEO',
            'fields' => ['seo_title', 'meta_description', 'author', 'reading_time', 'tags', 'image_alt', 'sections'],
            'sections_example' => [
                ['heading' => 'Contexte', 'body' => "Introduire le probleme et les enjeux SEO."],
                ['heading' => 'Approche Dream Digital', 'body' => "Expliquer la solution et les benefices."],
                ['heading' => 'Prochaine etape', 'body' => "Orienter vers le contact ou une page produit."],
            ],
        ],
        'legal' => [
            'label' => 'Page legale',
            'fields' => ['lead', 'last_updated', 'sections'],
            'sections_example' => [
                ['heading' => 'Responsable', 'body' => 'Texte valide par le PO ou le conseil juridique.'],
            ],
        ],
        'help' => [
            'label' => 'Aide / support',
            'fields' => ['lead', 'sections'],
            'sections_example' => [
                ['heading' => 'Question frequente', 'body' => 'Reponse claire et actionnable.'],
            ],
        ],
    ],
];
