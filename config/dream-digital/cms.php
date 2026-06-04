<?php

return [
    'rich_text' => [
        'allowed_html' => [
            'p',
            'br',
            'strong',
            'b',
            'em',
            'i',
            'u',
            's',
            'blockquote',
            'pre',
            'code',
            'ul',
            'ol',
            'li',
            'h2',
            'h3',
            'h4',
            'a[href|target|rel|title]',
            'img[src|alt|title|width|height]',
        ],
        'allowed_schemes' => [
            'http' => true,
            'https' => true,
            'mailto' => true,
            'tel' => true,
        ],
        'allowed_image_prefixes' => [
            '/img/cms/pages/',
        ],
    ],

    'schemas' => [
        'home' => [
            'label' => 'Page accueil',
            'fields' => ['eyebrow', 'lead', 'sections'],
            'sections_example' => [
                ['heading' => 'Bloc accueil', 'body' => 'Texte riche affiche sous le hero avant les services.'],
            ],
        ],
        'product' => [
            'label' => 'Page produit',
            'fields' => ['seo_title', 'meta_description', 'eyebrow', 'lead', 'faq', 'sections'],
            'sections_example' => [
                ['heading' => 'Positionnement', 'body' => 'Expliquez le probleme client et la promesse du produit.'],
                ['heading' => 'Cas d usage', 'body' => "Listez les usages prioritaires et les criteres de qualification."],
                ['heading' => 'Mise en route', 'body' => 'Detaillez les prochaines etapes: qualification, test, go-live.'],
            ],
        ],
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
