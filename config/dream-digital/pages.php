<?php

return [
    'stats' => [
        [
            'id' => 'countries',
            'value' => 200,
            'suffix' => '+',
            'label' => ['fr' => 'Pays couverts SMS', 'en' => 'SMS countries covered'],
            'caption' => ['fr' => 'Routes internationales et corridors premium.', 'en' => 'International routes and premium corridors.'],
        ],
        [
            'id' => 'uptime',
            'value' => 99,
            'suffix' => '.95%',
            'label' => ['fr' => 'SLA cible', 'en' => 'Target SLA'],
            'caption' => ['fr' => 'Architecture multi-DC et supervision continue.', 'en' => 'Multi-DC architecture and continuous monitoring.'],
        ],
        [
            'id' => 'latency',
            'value' => 2,
            'prefix' => '~',
            'suffix' => 's',
            'label' => ['fr' => 'Latence moyenne visee', 'en' => 'Target average latency'],
            'caption' => ['fr' => 'Optimisee pour OTP et flux transactionnels.', 'en' => 'Optimized for OTP and transactional flows.'],
        ],
        [
            'id' => 'presence',
            'value' => 5,
            'label' => ['fr' => 'Presences operationnelles', 'en' => 'Operating presences'],
            'caption' => ['fr' => 'Kinshasa, Abidjan, Brazzaville, Nairobi, Gentilly.', 'en' => 'Kinshasa, Abidjan, Brazzaville, Nairobi, Gentilly.'],
        ],
    ],

    'pages' => [
        'products' => [
            'eyebrow' => ['fr' => 'Produits', 'en' => 'Products'],
            'title' => ['fr' => 'Le catalogue telecom Dream Digital', 'en' => 'The Dream Digital telecom catalogue'],
            'lead' => [
                'fr' => 'SMS A2P, Voice Wholesale, DID, SIP Trunking, Dialo Contact Center et eSIM Zone, relies par une meme logique d integration.',
                'en' => 'A2P SMS, Wholesale Voice, DID, SIP Trunking, Dialo Contact Center and eSIM Zone, connected by one integration logic.',
            ],
        ],
        'developers' => [
            'eyebrow' => ['fr' => 'Developers', 'en' => 'Developers'],
            'title' => ['fr' => 'Des APIs telecom faites pour aller vite', 'en' => 'Telecom APIs built for speed'],
            'lead' => [
                'fr' => 'Un onboarding clair, des webhooks DLR, une sandbox isolee et des exemples copiables pour tester sans friction.',
                'en' => 'Clear onboarding, DLR webhooks, an isolated sandbox and copyable examples for low-friction testing.',
            ],
        ],
        'solutions' => [
            'eyebrow' => ['fr' => 'Solutions', 'en' => 'Solutions'],
            'title' => ['fr' => 'Des flux telecom adaptes aux metiers critiques', 'en' => 'Telecom workflows for critical industries'],
            'lead' => [
                'fr' => 'Banques, fintechs, retail, logistique, hotellerie et plateformes SaaS: chaque usage a ses exigences de delivrabilite.',
                'en' => 'Banks, fintechs, retail, logistics, hospitality and SaaS platforms: each workflow has its delivery constraints.',
            ],
        ],
        'coverage' => [
            'eyebrow' => ['fr' => 'Coverage', 'en' => 'Coverage'],
            'title' => ['fr' => 'Couverture globale, operations proches du terrain', 'en' => 'Global coverage, local operating presence'],
            'lead' => [
                'fr' => 'Plus de 200 destinations, avec des presences operationnelles en Afrique francophone et une representation Europe.',
                'en' => 'More than 200 destinations, with operating presences in Francophone Africa and a European representation.',
            ],
        ],
        'pricing' => [
            'eyebrow' => ['fr' => 'Pricing', 'en' => 'Pricing'],
            'title' => ['fr' => 'Tarifs indicatifs et routes negociees', 'en' => 'Indicative prices and negotiated routes'],
            'lead' => [
                'fr' => 'Commencez par un test, puis passez sur une tarification adaptee a vos volumes, pays et exigences SLA.',
                'en' => 'Start with a test, then move to pricing adapted to your volumes, countries and SLA requirements.',
            ],
        ],
        'company' => [
            'eyebrow' => ['fr' => 'Societe', 'en' => 'Company'],
            'title' => ['fr' => 'Un operateur CPaaS global, ancre en Afrique francophone', 'en' => 'A global CPaaS operator rooted in Francophone Africa'],
            'lead' => [
                'fr' => 'Dream Digital combine ingenierie telecom, partenariats operateurs et presence terrain pour connecter les entreprises modernes.',
                'en' => 'Dream Digital combines telecom engineering, carrier partnerships and field presence to connect modern businesses.',
            ],
        ],
        'contact' => [
            'eyebrow' => ['fr' => 'Contact', 'en' => 'Contact'],
            'title' => ['fr' => 'Parlons de votre route, volume ou integration', 'en' => 'Let us discuss your route, volume or integration'],
            'lead' => [
                'fr' => 'Envoyez un contexte court: pays, canal, volume mensuel, niveau de support attendu. Nous revenons avec une approche concrete.',
                'en' => 'Send a short context: country, channel, monthly volume, expected support level. We will come back with a concrete approach.',
            ],
        ],
    ],

    'features' => [
        'developers' => [
            ['icon' => 'bx-key', 'title' => ['fr' => 'API keys', 'en' => 'API keys'], 'body' => ['fr' => 'Separation test/live, rotation et bonnes pratiques de securite.', 'en' => 'Test/live separation, rotation and security best practices.']],
            ['icon' => 'bx-transfer', 'title' => ['fr' => 'Webhooks DLR', 'en' => 'DLR webhooks'], 'body' => ['fr' => 'Statuts de livraison signes pour vos systemes internes.', 'en' => 'Signed delivery statuses for your internal systems.']],
            ['icon' => 'bx-code-alt', 'title' => ['fr' => 'Exemples copiables', 'en' => 'Copyable examples'], 'body' => ['fr' => 'Curl, JSON et patterns d integration prepares pour les equipes techniques.', 'en' => 'Curl, JSON and integration patterns prepared for technical teams.']],
        ],
        'admin' => [
            ['icon' => 'bx-slider-alt', 'title' => ['fr' => 'Pricing multi-pays', 'en' => 'Multi-country pricing'], 'body' => ['fr' => 'Preparation du futur CRUD ServicePrice et publication par pays.', 'en' => 'Preparation for the future ServicePrice CRUD and country publishing.']],
            ['icon' => 'bx-shield-quarter', 'title' => ['fr' => 'RBAC', 'en' => 'RBAC'], 'body' => ['fr' => 'Roles et permissions gardes comme base du backoffice.', 'en' => 'Roles and permissions kept as the backoffice baseline.']],
            ['icon' => 'bx-user', 'title' => ['fr' => 'Utilisateurs', 'en' => 'Users'], 'body' => ['fr' => 'Gestion des comptes internes et futurs acces client.', 'en' => 'Internal account management and future client access.']],
        ],
    ],

    'corridors' => [
        ['from' => 'FR', 'to' => 'CD', 'title' => ['fr' => 'France vers RDC', 'en' => 'France to DRC'], 'label' => ['fr' => 'SMS premium', 'en' => 'Premium SMS'], 'quality' => 4, 'status' => ['fr' => 'Route prioritaire', 'en' => 'Priority route']],
        ['from' => 'CI', 'to' => 'FR', 'title' => ['fr' => 'Cote d Ivoire vers Europe', 'en' => 'Ivory Coast to Europe'], 'label' => ['fr' => 'Voice / SMS', 'en' => 'Voice / SMS'], 'quality' => 4, 'status' => ['fr' => 'Interconnexion active', 'en' => 'Active interconnect']],
        ['from' => 'CG', 'to' => 'CD', 'title' => ['fr' => 'Congo vers RDC', 'en' => 'Congo to DRC'], 'label' => ['fr' => 'Regional', 'en' => 'Regional'], 'quality' => 5, 'status' => ['fr' => 'Corridor suivi', 'en' => 'Monitored corridor']],
    ],

    'live_feed' => [
        ['time' => '09:42', 'label' => ['fr' => 'DLR confirme', 'en' => 'DLR confirmed'], 'detail' => ['fr' => 'OTP banking - Kinshasa', 'en' => 'Banking OTP - Kinshasa'], 'status' => 'success'],
        ['time' => '09:44', 'label' => ['fr' => 'Route optimisee', 'en' => 'Route optimized'], 'detail' => ['fr' => 'SMS retail - Abidjan', 'en' => 'Retail SMS - Abidjan'], 'status' => 'info'],
        ['time' => '09:47', 'label' => ['fr' => 'Webhook livre', 'en' => 'Webhook delivered'], 'detail' => ['fr' => 'SaaS alert - Europe', 'en' => 'SaaS alert - Europe'], 'status' => 'success'],
    ],
];
