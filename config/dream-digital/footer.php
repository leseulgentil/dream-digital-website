<?php

/*
|--------------------------------------------------------------------------
| Dream Digital — Footer (5 colonnes)
|--------------------------------------------------------------------------
|
| Structure du footer riche. Col 1 (Brand + tagline + bureaux) est rendue
| séparément par le component blade `footer.blade.php` — ce fichier décrit
| uniquement les colonnes 2-5 (Produits / Développeurs / Société / Légal).
|
| Liens marqués `'soon' => true` apparaissent avec un badge "(bientôt)"
| ou un état désactivé tant que la page cible n'existe pas.
|
*/

return [
    'columns' => [
        [
            'id'    => 'products',
            'title' => ['fr' => 'Produits', 'en' => 'Products'],
            'links' => [
                ['label' => ['fr' => 'SMS A2P',              'en' => 'SMS A2P'],              'url' => '#sms-a2p'],
                ['label' => ['fr' => 'Voice Wholesale',      'en' => 'Voice Wholesale'],      'url' => '#voice'],
                ['label' => ['fr' => 'DID Numbers',          'en' => 'DID Numbers'],          'url' => '#did'],
                ['label' => ['fr' => 'SIP Trunking',         'en' => 'SIP Trunking'],         'url' => '#sip'],
                ['label' => ['fr' => 'Dialo Contact Center', 'en' => 'Dialo Contact Center'], 'url' => '#dialo'],
                ['label' => ['fr' => 'eSIM Zone',            'en' => 'eSIM Zone'],            'url' => '#esim'],
            ],
            'order' => 2,
        ],
        [
            'id'    => 'developers',
            'title' => ['fr' => 'Développeurs', 'en' => 'Developers'],
            'links' => [
                ['label' => ['fr' => 'Documentation',   'en' => 'Documentation'], 'url' => '#docs',      'soon' => true],
                ['label' => ['fr' => 'Statut services', 'en' => 'Status'],        'url' => '#status',    'soon' => true],
                ['label' => ['fr' => 'Changelog',       'en' => 'Changelog'],     'url' => '#changelog', 'soon' => true],
            ],
            'order' => 3,
        ],
        [
            'id'    => 'company',
            'title' => ['fr' => 'Société', 'en' => 'Company'],
            'links' => [
                ['label' => ['fr' => 'À propos', 'en' => 'About'],   'url' => '#about'],
                ['label' => ['fr' => 'Contact',  'en' => 'Contact'], 'url' => 'mailto:sales@dream-digital.info'],
            ],
            'order' => 4,
        ],
        [
            'id'    => 'legal',
            'title' => ['fr' => 'Légal', 'en' => 'Legal'],
            'links' => [
                ['label' => ['fr' => 'Conditions générales', 'en' => 'Terms'],    'url' => '#terms',   'soon' => true],
                ['label' => ['fr' => 'Confidentialité',      'en' => 'Privacy'],  'url' => '#privacy', 'soon' => true],
            ],
            'order' => 5,
        ],
    ],

    'sub_footer' => [
        'copyright' => [
            'fr' => '© 2026 Dream Digital. Tous droits réservés.',
            'en' => '© 2026 Dream Digital. All rights reserved.',
        ],
        'offices_short' => 'Kinshasa · Abidjan · Brazzaville',
    ],
];
