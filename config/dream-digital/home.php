<?php

return [
    'hero' => [
        'headline' => [
            'fr' => 'L\'infrastructure telecom qui connecte les entreprises modernes a 200+ pays',
            'en' => 'Telecom infrastructure connecting modern businesses to 200+ countries',
        ],
        'cta_secondary' => [
            'fr' => 'Voir la doc API',
            'en' => 'View API docs',
        ],
        'bullets' => [
            ['fr' => 'Couverture 200+ pays', 'en' => '200+ countries covered'],
            ['fr' => 'Bureaux RDC, CI, CG, Kenya et France', 'en' => 'Offices in DRC, CI, CG, Kenya and France'],
        ],
    ],

    'trust_context' => [
        'fr' => 'Infrastructure CPaaS pour banques, fintechs, retail, logistique et plateformes SaaS',
        'en' => 'CPaaS infrastructure for banks, fintechs, retail, logistics and SaaS platforms',
    ],

    'developer' => [
        'eyebrow' => ['fr' => 'Developer experience', 'en' => 'Developer experience'],
        'title' => ['fr' => 'Onboardez en 10 minutes. Vraiment.', 'en' => 'Onboard in 10 minutes. Really.'],
        'body' => [
            'fr' => 'Inscription, API key, credit de test, webhooks DLR et premier SMS depuis une seule plateforme. Les equipes techniques gardent le controle, les equipes business gardent la vitesse.',
            'en' => 'Signup, API key, test credit, DLR webhooks and first SMS from one platform. Technical teams keep control, business teams keep speed.',
        ],
        'features' => [
            ['fr' => 'Credit de test', 'en' => 'Test credit'],
            ['fr' => 'Webhooks signes', 'en' => 'Signed webhooks'],
            ['fr' => 'Sandbox isolee', 'en' => 'Isolated sandbox'],
            ['fr' => 'Support FR/EN', 'en' => 'FR/EN support'],
        ],
    ],

    'pricing' => [
        [
            'name' => ['fr' => 'Decouverte', 'en' => 'Starter'],
            'price' => ['fr' => '$0 / inscription', 'en' => '$0 / signup'],
            'description' => ['fr' => 'Pour tester les APIs et valider un premier flux.', 'en' => 'For testing APIs and validating a first flow.'],
            'features' => [
                ['fr' => 'Credit de test offert', 'en' => 'Free test credit'],
                ['fr' => 'Sender ID test', 'en' => 'Test sender ID'],
                ['fr' => 'API SMS complete', 'en' => 'Full SMS API'],
                ['fr' => 'Support email', 'en' => 'Email support'],
            ],
            'cta' => ['fr' => 'Commencer', 'en' => 'Start'],
            'highlight' => false,
        ],
        [
            'name' => ['fr' => 'Pay-as-you-go', 'en' => 'Pay-as-you-go'],
            'price' => ['fr' => '$0.0089 / SMS RDC', 'en' => '$0.0089 / DRC SMS'],
            'description' => ['fr' => 'Tarif indicatif, routes premium et webhooks DLR.', 'en' => 'Indicative pricing, premium routes and DLR webhooks.'],
            'features' => [
                ['fr' => 'Sender ID dedie', 'en' => 'Dedicated sender ID'],
                ['fr' => 'Recharge en ligne', 'en' => 'Online top-up'],
                ['fr' => 'Support FR/EN', 'en' => 'FR/EN support'],
                ['fr' => 'SLA vise 99.9%', 'en' => '99.9% target SLA'],
            ],
            'cta' => ['fr' => 'Demarrer', 'en' => 'Launch'],
            'highlight' => true,
        ],
        [
            'name' => ['fr' => 'Entreprise', 'en' => 'Enterprise'],
            'price' => ['fr' => 'Sur-mesure', 'en' => 'Custom'],
            'description' => ['fr' => 'Volumes, SIP/Voice, SMPP, account management et SLA dedie.', 'en' => 'Volumes, SIP/Voice, SMPP, account management and dedicated SLA.'],
            'features' => [
                ['fr' => 'Tarification negociee', 'en' => 'Negotiated pricing'],
                ['fr' => 'Account manager', 'en' => 'Account manager'],
                ['fr' => 'Support prioritaire', 'en' => 'Priority support'],
                ['fr' => 'SMPP / SIP / Voice', 'en' => 'SMPP / SIP / Voice'],
            ],
            'cta' => ['fr' => 'Nous contacter', 'en' => 'Contact us'],
            'highlight' => false,
        ],
    ],

    'faq' => [
        [
            'question' => ['fr' => 'Dream Digital est-il uniquement africain ?', 'en' => 'Is Dream Digital Africa-only?'],
            'answer' => [
                'fr' => 'Non. Nos equipes sont basees en Afrique francophone, mais la plateforme sert des clients et partenaires sur plusieurs continents.',
                'en' => 'No. Our teams are based in Francophone Africa, but the platform serves customers and partners across several continents.',
            ],
        ],
        [
            'question' => ['fr' => 'Quels canaux sont prioritaires ?', 'en' => 'Which channels are prioritized?'],
            'answer' => [
                'fr' => 'SMS A2P, Voice Wholesale, DID, SIP Trunking, Dialo Contact Center et eSIM Zone structurent le catalogue initial.',
                'en' => 'A2P SMS, Wholesale Voice, DID, SIP Trunking, Dialo Contact Center and eSIM Zone structure the initial catalogue.',
            ],
        ],
        [
            'question' => ['fr' => 'Peut-on demarrer sans gros volume ?', 'en' => 'Can we start without high volume?'],
            'answer' => [
                'fr' => 'Oui. Le parcours Decouverte permet de tester les APIs avant une negociation volume ou un contrat entreprise.',
                'en' => 'Yes. The Starter path lets teams test APIs before volume negotiation or an enterprise contract.',
            ],
        ],
    ],
];
