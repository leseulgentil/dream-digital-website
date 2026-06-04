<?php

/*
|--------------------------------------------------------------------------
| Dream Digital — Services
|--------------------------------------------------------------------------
|
| Liste des 6 services Dream Digital affichés sur la home corporate.
| Source : copy validé PO 2026-05-08 (draft 2 page d'attente, réutilisé
| pour Sprint 1.5).
|
| Lecture côté Blade : config('dream-digital.services.items.0.name.fr')
| OU iteration : @foreach(config('dream-digital.services.items') as $service)
|
| Architecture CMS-ready : ce contenu sera migré vers une table
| `services` (Eloquent Model) en Sprint CMS futur, sans changement
| frontend.
|
*/

return [
    'home_cards' => [
        [
            'id' => 'sms-wholesale',
            'title' => ['fr' => 'SMS Wholesale', 'en' => 'SMS Wholesale'],
            'excerpt' => [
                'fr' => 'Routes SMS A2P grossiste pour operateurs, aggregateurs et plateformes CPaaS avec supervision DLR.',
                'en' => 'Wholesale A2P SMS routes for operators, aggregators and CPaaS platforms with DLR supervision.',
            ],
            'badges' => [
                ['fr' => 'SMPP/API', 'en' => 'SMPP/API'],
                ['fr' => 'DLR temps reel', 'en' => 'Real-time DLR'],
            ],
            'proof_points' => [
                ['fr' => 'Routes premium et volumes negocies', 'en' => 'Premium routes and negotiated volume'],
                ['fr' => 'Monitoring qualite par destination', 'en' => 'Quality monitoring by destination'],
            ],
            'illustration' => [
                'src' => '/img/services/sms-wholesale.webp',
                'alt' => ['fr' => 'Illustration SMS Wholesale Dream Digital', 'en' => 'Dream Digital SMS Wholesale illustration'],
                'width' => 960,
                'height' => 640,
            ],
            'icon' => 'bx-message-detail',
            'cta' => [
                'url' => '/{locale}/products/sms-a2p',
                'label' => ['fr' => 'Voir SMS Wholesale', 'en' => 'View SMS Wholesale'],
                'external' => false,
            ],
            'order' => 1,
            'active' => true,
        ],
        [
            'id' => 'sms-retail',
            'title' => ['fr' => 'SMS Retail', 'en' => 'SMS Retail'],
            'excerpt' => [
                'fr' => 'Campagnes SMS retail, OTP et notifications client pour equipes marketing, fintech, retail et support.',
                'en' => 'Retail SMS campaigns, OTP flows and customer notifications for marketing, fintech, retail and support teams.',
            ],
            'badges' => [
                ['fr' => 'OTP', 'en' => 'OTP'],
                ['fr' => 'Marketing', 'en' => 'Marketing'],
            ],
            'proof_points' => [
                ['fr' => 'Sender IDs, unicode et concatenation', 'en' => 'Sender IDs, unicode and concatenation'],
                ['fr' => 'Parcours simples pour demarrer vite', 'en' => 'Simple paths to start quickly'],
            ],
            'illustration' => [
                'src' => '/img/services/sms-retail.webp',
                'alt' => ['fr' => 'Illustration SMS Retail Dream Digital', 'en' => 'Dream Digital SMS Retail illustration'],
                'width' => 960,
                'height' => 640,
            ],
            'icon' => 'bx-chat',
            'cta' => [
                'url' => '/{locale}/products/sms-a2p',
                'label' => ['fr' => 'Lancer SMS Retail', 'en' => 'Launch SMS Retail'],
                'external' => false,
            ],
            'order' => 2,
            'active' => true,
        ],
        [
            'id' => 'voice-wholesale-card',
            'title' => ['fr' => 'Voice Wholesale', 'en' => 'Voice Wholesale'],
            'excerpt' => [
                'fr' => 'Terminaison voix wholesale pour operateurs, integrateurs et plateformes avec qualite carrier-grade.',
                'en' => 'Wholesale voice routes for operators, integrators and platforms with carrier-grade quality.',
            ],
            'badges' => [
                ['fr' => 'SIP', 'en' => 'SIP'],
                ['fr' => 'ASR/ACD', 'en' => 'ASR/ACD'],
            ],
            'proof_points' => [
                ['fr' => 'Tarifs par destination et volume', 'en' => 'Destination and volume pricing'],
                ['fr' => 'Failover et supervision route', 'en' => 'Failover and route supervision'],
            ],
            'illustration' => [
                'src' => '/img/services/voice-wholesale.webp',
                'alt' => ['fr' => 'Illustration Voice Wholesale Dream Digital', 'en' => 'Dream Digital Voice Wholesale illustration'],
                'width' => 960,
                'height' => 640,
            ],
            'icon' => 'bx-phone-call',
            'cta' => [
                'url' => '/{locale}/products/voice',
                'label' => ['fr' => 'Voir Voice Wholesale', 'en' => 'View Voice Wholesale'],
                'external' => false,
            ],
            'order' => 3,
            'active' => true,
        ],
        [
            'id' => 'voice-retail',
            'title' => ['fr' => 'Voice Retail', 'en' => 'Voice Retail'],
            'excerpt' => [
                'fr' => 'Offres voix retail pour entreprises, centres de contact, numerotation et appels sortants fiables.',
                'en' => 'Retail voice offers for enterprises, contact centers, numbering and reliable outbound calling.',
            ],
            'badges' => [
                ['fr' => 'Entreprises', 'en' => 'Enterprise'],
                ['fr' => 'Support', 'en' => 'Support'],
            ],
            'proof_points' => [
                ['fr' => 'DID, SIP et appels sortants', 'en' => 'DID, SIP and outbound calls'],
                ['fr' => 'Mise en service accompagnee', 'en' => 'Guided onboarding'],
            ],
            'illustration' => [
                'src' => '/img/services/voice-retail.webp',
                'alt' => ['fr' => 'Illustration Voice Retail Dream Digital', 'en' => 'Dream Digital Voice Retail illustration'],
                'width' => 960,
                'height' => 640,
            ],
            'icon' => 'bx-phone',
            'cta' => [
                'url' => '/{locale}/products/voice',
                'label' => ['fr' => 'Decouvrir Voice Retail', 'en' => 'Explore Voice Retail'],
                'external' => false,
            ],
            'order' => 4,
            'active' => true,
        ],
        [
            'id' => 'esimzone',
            'title' => ['fr' => 'eSIMZone', 'en' => 'eSIMZone'],
            'excerpt' => [
                'fr' => 'Plateforme eSIM data pour voyageurs et entreprises : forfaits locaux, regionaux, globaux et activation rapide.',
                'en' => 'Data eSIM platform for travelers and companies: local, regional and global plans with fast activation.',
            ],
            'badges' => [
                ['fr' => 'eSIM data', 'en' => 'Data eSIM'],
                ['fr' => 'QR activation', 'en' => 'QR activation'],
            ],
            'proof_points' => [
                ['fr' => 'Plans par destination et region', 'en' => 'Destination and regional plans'],
                ['fr' => 'Experience autonome sur esimzone.fr', 'en' => 'Self-service on esimzone.fr'],
            ],
            'illustration' => [
                'src' => '/img/services/esimzone.webp',
                'alt' => ['fr' => 'Illustration eSIMZone Dream Digital', 'en' => 'Dream Digital eSIMZone illustration'],
                'width' => 960,
                'height' => 640,
            ],
            'icon' => 'bx-mobile-alt',
            'cta' => [
                'url' => 'https://esimzone.fr/',
                'label' => ['fr' => 'Visiter eSIMZone', 'en' => 'Visit eSIMZone'],
                'external' => true,
            ],
            'order' => 5,
            'active' => true,
        ],
        [
            'id' => 'dialo-platform',
            'title' => ['fr' => 'DIALO', 'en' => 'DIALO'],
            'excerpt' => [
                'fr' => 'Plateforme call center omnicanale pour voix, WhatsApp, email, chat et SMS avec supervision agents.',
                'en' => 'Omnichannel call center platform for voice, WhatsApp, email, chat and SMS with agent supervision.',
            ],
            'badges' => [
                ['fr' => 'Omnicanal', 'en' => 'Omnichannel'],
                ['fr' => 'Supervision', 'en' => 'Supervision'],
            ],
            'proof_points' => [
                ['fr' => 'Routage, IVR, enregistrement et analytics', 'en' => 'Routing, IVR, recording and analytics'],
                ['fr' => 'Interface unifiee agents et superviseurs', 'en' => 'Unified agent and supervisor workspace'],
            ],
            'illustration' => [
                'src' => '/img/services/dialo-platform.webp',
                'alt' => ['fr' => 'Illustration DIALO Dream Digital', 'en' => 'Dream Digital DIALO illustration'],
                'width' => 960,
                'height' => 640,
            ],
            'icon' => 'bx-headphone',
            'cta' => [
                'url' => '/{locale}/products/dialo',
                'label' => ['fr' => 'Demander une demo DIALO', 'en' => 'Request a DIALO demo'],
                'external' => false,
            ],
            'order' => 6,
            'active' => true,
        ],
    ],

    'items' => [
        [
            'id'          => 'sms-a2p',
            'slug'        => 'sms-a2p',
            'name'        => ['fr' => 'SMS A2P', 'en' => 'SMS A2P'],
            'tagline'     => [
                'fr' => 'Notifications, OTP, marketing — 200+ pays.',
                'en' => 'Notifications, OTP, marketing — 200+ countries.',
            ],
            'description' => [
                'fr' => 'Messagerie applicatif-à-personne pour notifications transactionnelles, codes OTP, campagnes marketing et alertes critiques. Routes premium vers plus de 200 pays, monitoring en temps réel, support du concaténé et de l\'unicode.',
                'en' => 'Application-to-person messaging for transactional notifications, OTP codes, marketing campaigns and critical alerts. Premium routes to 200+ countries, real-time monitoring, support for concatenated and unicode messages.',
            ],
            'icon'        => 'bx-message-detail',
            'cta_label'   => ['fr' => 'Voir la doc', 'en' => 'See docs'],
            'cta_url'     => '#sms-a2p',
            'order'       => 1,
            'active'      => true,
        ],
        [
            'id'          => 'voice-wholesale',
            'slug'        => 'voice',
            'name'        => ['fr' => 'Voice Wholesale', 'en' => 'Voice Wholesale'],
            'tagline'     => [
                'fr' => 'Terminaisons voix grossiste, qualité carrier-grade.',
                'en' => 'Wholesale voice termination, carrier-grade quality.',
            ],
            'description' => [
                'fr' => 'Terminaisons voix grossiste pour intégrateurs, opérateurs et revendeurs. Qualité carrier-grade, ASR et ACD optimisés, tarification par destination et par volume. Support des codecs G.711, G.729, OPUS.',
                'en' => 'Wholesale voice termination for integrators, carriers and resellers. Carrier-grade quality, optimized ASR and ACD, per-destination and per-volume pricing. Codec support: G.711, G.729, OPUS.',
            ],
            'icon'        => 'bx-phone',
            'cta_label'   => ['fr' => 'Voir tarifs', 'en' => 'See pricing'],
            'cta_url'     => '#voice',
            'order'       => 2,
            'active'      => true,
        ],
        [
            'id'          => 'did-numbers',
            'slug'        => 'did',
            'name'        => ['fr' => 'DID Numbers', 'en' => 'DID Numbers'],
            'tagline'     => [
                'fr' => 'Numéros géographiques, mobiles, toll-free dans 80+ pays.',
                'en' => 'Geographic, mobile, toll-free numbers in 80+ countries.',
            ],
            'description' => [
                'fr' => 'Numéros géographiques, nationaux, mobiles et toll-free dans plus de 80 pays. Provisionnement en quelques minutes, portabilité supportée, intégration directe à votre IPBX ou plateforme cloud.',
                'en' => 'Geographic, national, mobile and toll-free numbers in 80+ countries. Provisioning in minutes, number portability supported, direct integration with your IPBX or cloud platform.',
            ],
            'icon'        => 'bx-hash',
            'cta_label'   => ['fr' => 'Acheter', 'en' => 'Buy now'],
            'cta_url'     => '#did',
            'order'       => 3,
            'active'      => true,
        ],
        [
            'id'          => 'sip-trunking',
            'slug'        => 'sip',
            'name'        => ['fr' => 'SIP Trunking', 'en' => 'SIP Trunking'],
            'tagline'     => [
                'fr' => 'Trunks haute disponibilité, encryption TLS/SRTP.',
                'en' => 'High-availability trunks, TLS/SRTP encryption.',
            ],
            'description' => [
                'fr' => 'Trunks SIP haute disponibilité pour entreprises et call centers. Encryption TLS/SRTP, failover automatique, capacité illimitée configurable, compatibilité Asterisk, FreeSWITCH, 3CX et Cisco.',
                'en' => 'High-availability SIP trunks for enterprises and call centers. TLS/SRTP encryption, automatic failover, configurable unlimited capacity, compatible with Asterisk, FreeSWITCH, 3CX and Cisco.',
            ],
            'icon'        => 'bx-network-chart',
            'cta_label'   => ['fr' => 'Configurer', 'en' => 'Configure'],
            'cta_url'     => '#sip',
            'order'       => 4,
            'active'      => true,
        ],
        [
            'id'          => 'dialo-cc',
            'slug'        => 'dialo',
            'name'        => ['fr' => 'Dialo Contact Center', 'en' => 'Dialo Contact Center'],
            'tagline'     => [
                'fr' => 'Plateforme omnicanale : voix, WhatsApp, email, chat, SMS.',
                'en' => 'Omnichannel platform: voice, WhatsApp, email, chat, SMS.',
            ],
            'description' => [
                'fr' => 'Plateforme omnicanale de centre de contact : voix, WhatsApp, email, chat web, SMS — sous une interface unifiée pour superviseurs et agents. Routage intelligent, IVR, enregistrement, analytics avancés.',
                'en' => 'Omnichannel contact center platform: voice, WhatsApp, email, web chat, SMS — under a unified interface for supervisors and agents. Smart routing, IVR, call recording, advanced analytics.',
            ],
            'icon'        => 'bx-headphone',
            'cta_label'   => ['fr' => 'Demander une démo', 'en' => 'Request a demo'],
            'cta_url'     => '#dialo',
            'order'       => 5,
            'active'      => true,
        ],
        [
            'id'          => 'esim-zone',
            'slug'        => 'esim',
            'name'        => ['fr' => 'eSIM Zone', 'en' => 'eSIM Zone'],
            'tagline'     => [
                'fr' => 'eSIM data pour voyageurs et entreprises, marque blanche.',
                'en' => 'eSIM data for travelers and businesses, white-label.',
            ],
            'description' => [
                'fr' => 'Solution eSIM data pour voyageurs et entreprises. Forfaits par pays ou régionaux, activation instantanée par QR code, gestion multi-profils, marque blanche disponible pour distributeurs.',
                'en' => 'Data eSIM solution for travelers and businesses. Per-country and regional plans, instant QR-code activation, multi-profile management, white-label option for distributors.',
            ],
            'icon'        => 'bx-mobile-alt',
            'cta_label'   => ['fr' => 'Visiter eSIM Zone', 'en' => 'Visit eSIM Zone'],
            'cta_url'     => '#esim',
            'order'       => 6,
            'active'      => true,
        ],
    ],
];
