<?php

/*
|--------------------------------------------------------------------------
| Dream Digital — Partenaires stratégiques
|--------------------------------------------------------------------------
|
| 5 partenaires stratégiques avec descriptions neutralisées (validées par
| PO 2026-05-08 draft 2). Logos à fournir par PO sous /public/img/partners/
| avant activation de la section (show_section: true).
|
| Note importante : il s'agit ici de partenaires opérateurs/intégrateurs
| identifiables, PAS de logos clients. Distinction critique avec
| trust-signals.php qui n'affiche AUCUN logo client (cf. arbitrage Q2
| ANALYZE_SPRINT_1_5.md).
|
*/

return [
    'items' => [
        [
            'id'          => 'telna',
            'name'        => 'Telna',
            'description' => [
                'fr' => 'Connectivité internationale',
                'en' => 'International connectivity',
            ],
            'logo_path'   => '/img/partners/telna.svg',
            'logo_alt'    => 'Telna',
            'order'       => 1,
            'active'      => true,
        ],
        [
            'id'          => 'inextrix',
            'name'        => 'Inextrix',
            'description' => [
                'fr' => 'Solutions infrastructure télécom',
                'en' => 'Telecom infrastructure solutions',
            ],
            'logo_path'   => '/img/partners/inextrix.svg',
            'logo_alt'    => 'Inextrix',
            'order'       => 2,
            'active'      => true,
        ],
        [
            'id'          => 'odoo',
            'name'        => 'Odoo',
            'description' => [
                'fr' => 'Plateforme de gestion d\'entreprise',
                'en' => 'Business management platform',
            ],
            'logo_path'   => '/img/partners/odoo.svg',
            'logo_alt'    => 'Odoo',
            'order'       => 3,
            'active'      => true,
        ],
        [
            'id'          => 'hodu',
            'name'        => 'HODU',
            'description' => [
                'fr' => 'Solutions centre de contact',
                'en' => 'Contact center solutions',
            ],
            'logo_path'   => '/img/partners/hodu.svg',
            'logo_alt'    => 'HODU',
            'order'       => 4,
            'active'      => true,
        ],
        [
            'id'          => 'concentrix',
            'name'        => 'Concentrix',
            'description' => [
                'fr' => 'Expertise opérationnelle',
                'en' => 'Operational expertise',
            ],
            'logo_path'   => '/img/partners/concentrix.svg',
            'logo_alt'    => 'Concentrix',
            'order'       => 5,
            'active'      => true,
        ],
    ],

    // Bascule globale : false par défaut tant que les logos SVG ne sont
    // pas fournis par PO sous /public/img/partners/. Mettre à true pour
    // activer la section dans la home.
    'show_section' => false,

    'section_title' => [
        'fr' => 'Des partenariats stratégiques',
        'en' => 'Strategic partnerships',
    ],
    'section_subtitle' => [
        'fr' => 'Pour livrer une qualité carrier-grade et une couverture vraiment globale.',
        'en' => 'To deliver carrier-grade quality and truly global coverage.',
    ],
];
