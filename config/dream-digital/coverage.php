<?php

/*
|--------------------------------------------------------------------------
| Dream Digital — Coverage map data
|--------------------------------------------------------------------------
|
| Données pour la section "Coverage" de la home (carte du monde stylisée
| façon constellation + stats par continent + 3 bureaux opérationnels).
|
| Stats globales conformes copy validé PO 2026-05-08 ("plus de 200
| destinations") + arbitrage Q4 ANALYZE_SPRINT_1_5.md (pas de liste
| détaillée par opérateur, reportée à Sprint 1).
|
*/

return [
    'global' => [
        'countries_count' => 200,
        'countries_label' => [
            'fr' => 'pays couverts SMS',
            'en' => 'countries covered for SMS',
        ],
        'description' => [
            'fr' => 'Notre infrastructure couvre plus de 200 destinations à travers le monde grâce à un réseau de partenariats opérateurs directs et d\'interconnexions stratégiques. Nos équipes humaines sont basées en Afrique francophone, mais nos services sont conçus pour des cas d\'usage globaux.',
            'en' => 'Our infrastructure covers 200+ destinations worldwide through a network of direct carrier partnerships and strategic interconnects. Our teams are based in Francophone Africa, but our services are designed for global use cases.',
        ],
    ],

    'continents' => [
        ['name' => ['fr' => 'Afrique',  'en' => 'Africa'],   'countries' => 54, 'order' => 1],
        ['name' => ['fr' => 'Europe',   'en' => 'Europe'],   'countries' => 44, 'order' => 2],
        ['name' => ['fr' => 'Amérique', 'en' => 'Americas'], 'countries' => 35, 'order' => 3],
        ['name' => ['fr' => 'Asie',     'en' => 'Asia'],     'countries' => 41, 'order' => 4],
    ],

    'offices' => [
        [
            'id'          => 'kinshasa',
            'city'        => 'Kinshasa',
            'country'     => ['fr' => 'RDC', 'en' => 'DRC'],
            'iso_alpha3'  => 'COD',
            'lat'         => -4.4419,
            'lng'         => 15.2663,
            'description' => [
                'fr' => 'Opérations centrales, ingénierie réseau, support clients',
                'en' => 'Central operations, network engineering, customer support',
            ],
            'is_hq'       => true,
            'coming_soon' => false,
            'order'       => 1,
        ],
        [
            'id'          => 'abidjan',
            'city'        => 'Abidjan',
            'country'     => ['fr' => 'Côte d\'Ivoire', 'en' => 'Ivory Coast'],
            'iso_alpha3'  => 'CIV',
            'lat'         => 5.3600,
            'lng'         => -4.0083,
            'description' => [
                'fr' => 'Développement commercial Afrique de l\'Ouest, intégrations régionales',
                'en' => 'West Africa business development, regional integrations',
            ],
            'is_hq'       => false,
            'coming_soon' => false,
            'order'       => 2,
        ],
        [
            'id'          => 'brazzaville',
            'city'        => 'Brazzaville',
            'country'     => ['fr' => 'Congo', 'en' => 'Congo'],
            'iso_alpha3'  => 'COG',
            'lat'         => -4.2634,
            'lng'         => 15.2429,
            'description' => [
                'fr' => 'Couverture marché local, partenariats institutionnels',
                'en' => 'Local market coverage, institutional partnerships',
            ],
            'is_hq'       => false,
            'coming_soon' => false,
            'order'       => 3,
        ],
        [
            'id'          => 'nairobi',
            'city'        => 'Nairobi',
            'country'     => ['fr' => 'Kenya', 'en' => 'Kenya'],
            'iso_alpha3'  => 'KEN',
            'lat'         => -1.2921,
            'lng'         => 36.8219,
            'description' => [
                'fr' => 'Hub Afrique de l\'Est, intégrations opérateurs régionaux',
                'en' => 'East Africa hub, regional carrier integrations',
            ],
            'is_hq'       => false,
            'coming_soon' => false,
            'order'       => 4,
        ],
        [
            'id'          => 'gentilly',
            'city'        => 'Gentilly',
            'country'     => ['fr' => 'France', 'en' => 'France'],
            'iso_alpha3'  => 'FRA',
            'lat'         => 48.8167,
            'lng'         => 2.3500,
            'description' => [
                'fr' => 'Représentation commerciale Europe, ouverture imminente',
                'en' => 'Europe commercial representation, opening soon',
            ],
            'is_hq'       => false,
            'coming_soon' => true,
            'order'       => 5,
        ],
    ],

    'section_title' => [
        'fr' => 'Une couverture vraiment globale',
        'en' => 'Truly global coverage',
    ],
];
