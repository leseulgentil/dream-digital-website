<?php

/*
|--------------------------------------------------------------------------
| Dream Digital — Trust Signals (impersonnels, pas de logos clients)
|--------------------------------------------------------------------------
|
| Trust signals impersonnels destinés à remplacer la stratégie "logos
| clients" du brief Sprint 1.5 original. Décision PO arbitrage 2026-05-08
| (Q2 ANALYZE_SPRINT_1_5.md) : refus catégorique d'afficher des logos
| clients placeholders pour risque juridique d'usurpation d'identité
| commerciale.
|
| Approche alignée avec le playbook Telnyx (cf. DESIGN_REFERENCES.md
| Section 2.2) — preuve par chiffres + carrier credentials + compliance,
| pas par logos.
|
*/

return [
    'items' => [
        [
            'id'    => 'coverage',
            'value' => '200+',
            'label' => [
                'fr' => 'Pays couverts SMS',
                'en' => 'Countries covered SMS',
            ],
            'icon'  => 'bx-globe',
            'order' => 1,
        ],
        [
            'id'    => 'sla',
            'value' => '99.9%',
            'label' => [
                'fr' => 'SLA visé',
                'en' => 'SLA target',
            ],
            'icon'  => 'bx-shield-quarter',
            'order' => 2,
        ],
        [
            'id'    => 'datacenters',
            'value' => 'Multi-DC',
            'label' => [
                'fr' => 'Architecture redondée',
                'en' => 'Redundant architecture',
            ],
            'icon'  => 'bx-server',
            'order' => 3,
        ],
        [
            'id'    => 'gdpr',
            'value' => 'GDPR',
            'label' => [
                'fr' => 'Conforme',
                'en' => 'Compliant',
            ],
            'icon'  => 'bx-check-shield',
            'order' => 4,
        ],
    ],

    // Certifications visées (mention dédiée plus bas dans la home, séparée
    // des trust signals primaires pour ne pas mélanger "actuel" et "en cours")
    'certifications_targeted' => [
        [
            'name'   => 'ISO 27001',
            'status' => 'in_progress',
            'label'  => [
                'fr' => 'ISO 27001 — en cours',
                'en' => 'ISO 27001 — in progress',
            ],
        ],
        [
            'name'   => 'SOC 2 Type II',
            'status' => 'targeted',
            'label'  => [
                'fr' => 'SOC 2 Type II — visé',
                'en' => 'SOC 2 Type II — targeted',
            ],
        ],
    ],

    'section_title' => [
        'fr' => 'Une infrastructure conçue pour la confiance',
        'en' => 'Infrastructure built for trust',
    ],
];
