<?php

/*
|--------------------------------------------------------------------------
| Dream Digital — Industries / Cas d'usage
|--------------------------------------------------------------------------
|
| 4 cas d'usage par industrie cibles. Remplace la section témoignages
| (refusée pour V1, voir ANALYZE_SPRINT_1_5.md Section 5 — arbitrage Q3
| 2026-05-08).
|
| Aucun client réel ou fictif nommé. Descriptions génériques sectorielles.
|
*/

return [
    'items' => [
        [
            'id'          => 'banking-fintech',
            'name'        => [
                'fr' => 'Banking & Fintech',
                'en' => 'Banking & Fintech',
            ],
            'description' => [
                'fr' => 'OTP transactionnels, alertes fraude, confirmations KYC, notifications compte.',
                'en' => 'OTP transactionnels, alertes fraude, confirmations KYC, notifications compte.',
            ],
            'icon'        => 'bx-bank',
            'order'       => 1,
            'active'      => true,
        ],
        [
            'id'          => 'retail-ecommerce',
            'name'        => [
                'fr' => 'Retail & E-commerce',
                'en' => 'Retail & E-commerce',
            ],
            'description' => [
                'fr' => 'Confirmations de commande, notifications de livraison, campagnes promotionnelles, programmes de fidélité.',
                'en' => 'Confirmations de commande, notifications de livraison, campagnes promotionnelles, programmes de fidélité.',
            ],
            'icon'        => 'bx-store',
            'order'       => 2,
            'active'      => true,
        ],
        [
            'id'          => 'logistics',
            'name'        => [
                'fr' => 'Logistics',
                'en' => 'Logistics',
            ],
            'description' => [
                'fr' => 'Tracking de livraison temps réel, notifications conducteurs, alertes opérationnelles, communication B2B.',
                'en' => 'Tracking de livraison temps réel, notifications conducteurs, alertes opérationnelles, communication B2B.',
            ],
            'icon'        => 'bx-package',
            'order'       => 3,
            'active'      => true,
        ],
        [
            'id'          => 'hospitality',
            'name'        => [
                'fr' => 'Hospitality',
                'en' => 'Hospitality',
            ],
            'description' => [
                'fr' => 'Confirmations de réservation, check-in numérique, communications conciergerie, programmes de fidélisation.',
                'en' => 'Confirmations de réservation, check-in numérique, communications conciergerie, programmes de fidélisation.',
            ],
            'icon'        => 'bx-hotel',
            'order'       => 4,
            'active'      => true,
        ],
    ],
    'section_title' => [
        'fr' => 'Des cas d\'usage par industrie',
        'en' => 'Industry use cases',
    ],
    'section_subtitle' => [
        'fr' => 'Comment nos clients utilisent Dream Digital, secteur par secteur.',
        'en' => 'How our clients use Dream Digital, sector by sector.',
    ],
];
