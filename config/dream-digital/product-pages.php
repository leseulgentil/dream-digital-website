<?php

return [
    'items' => [
        'sms-a2p' => [
            'proofs' => [
                ['icon' => 'bx-timer', 'title' => ['fr' => 'Latence surveillee', 'en' => 'Monitored latency'], 'body' => ['fr' => 'Routes suivies pour OTP, alertes et messages transactionnels.', 'en' => 'Routes monitored for OTP, alerts and transactional messages.']],
                ['icon' => 'bx-check-shield', 'title' => ['fr' => 'DLR exploitables', 'en' => 'Usable DLR'], 'body' => ['fr' => 'Statuts de livraison pensés pour support, BI et retry.', 'en' => 'Delivery statuses designed for support, BI and retry logic.']],
                ['icon' => 'bx-world', 'title' => ['fr' => 'Couverture multi-pays', 'en' => 'Multi-country coverage'], 'body' => ['fr' => 'Un routage adapte aux corridors critiques Afrique, Europe et global.', 'en' => 'Routing adapted to critical Africa, Europe and global corridors.']],
            ],
            'workflow' => [
                ['label' => ['fr' => 'Qualifier', 'en' => 'Qualify'], 'body' => ['fr' => 'Pays, volumes, sender IDs, usages OTP ou marketing.', 'en' => 'Countries, volumes, sender IDs, OTP or marketing use cases.']],
                ['label' => ['fr' => 'Tester', 'en' => 'Test'], 'body' => ['fr' => 'Sandbox, webhooks DLR et routes prioritaires.', 'en' => 'Sandbox, DLR webhooks and priority routes.']],
                ['label' => ['fr' => 'Scaler', 'en' => 'Scale'], 'body' => ['fr' => 'Monitoring par destination et optimisation continue.', 'en' => 'Destination monitoring and continuous optimization.']],
            ],
        ],
        'voice' => [
            'proofs' => [
                ['icon' => 'bx-line-chart', 'title' => ['fr' => 'ASR / ACD suivis', 'en' => 'ASR / ACD tracked'], 'body' => ['fr' => 'Qualite voix mesuree par destination et par partenaire.', 'en' => 'Voice quality measured by destination and partner.']],
                ['icon' => 'bx-headphone', 'title' => ['fr' => 'Qualite carrier-grade', 'en' => 'Carrier-grade quality'], 'body' => ['fr' => 'Routes adaptees aux centres d appels et integrateurs.', 'en' => 'Routes suited for contact centers and integrators.']],
                ['icon' => 'bx-cog', 'title' => ['fr' => 'Codecs documentes', 'en' => 'Documented codecs'], 'body' => ['fr' => 'G.711, G.729, OPUS selon le besoin de compatibilite.', 'en' => 'G.711, G.729, OPUS depending on compatibility needs.']],
            ],
            'workflow' => [
                ['label' => ['fr' => 'Mesurer', 'en' => 'Measure'], 'body' => ['fr' => 'Destinations, pics, ASR, ACD et PDD attendus.', 'en' => 'Destinations, peaks, expected ASR, ACD and PDD.']],
                ['label' => ['fr' => 'Router', 'en' => 'Route'], 'body' => ['fr' => 'Mix premium/economique selon SLA et volume.', 'en' => 'Premium/cost mix based on SLA and volume.']],
                ['label' => ['fr' => 'Surveiller', 'en' => 'Monitor'], 'body' => ['fr' => 'Alertes qualite et ajustements par corridor.', 'en' => 'Quality alerts and corridor adjustments.']],
            ],
        ],
        'did' => [
            'proofs' => [
                ['icon' => 'bx-map-pin', 'title' => ['fr' => 'Presence locale', 'en' => 'Local presence'], 'body' => ['fr' => 'Numeros locaux pour ventes, support et campagnes pays.', 'en' => 'Local numbers for sales, support and country campaigns.']],
                ['icon' => 'bx-transfer', 'title' => ['fr' => 'Routage flexible', 'en' => 'Flexible routing'], 'body' => ['fr' => 'Connexion vers IPBX, centre de contact ou plateforme cloud.', 'en' => 'Connection to PBX, contact center or cloud platform.']],
                ['icon' => 'bx-list-check', 'title' => ['fr' => 'Inventaire pilote', 'en' => 'Managed inventory'], 'body' => ['fr' => 'Suivi des pays, types de numeros et affectations.', 'en' => 'Tracking countries, number types and assignments.']],
            ],
            'workflow' => [
                ['label' => ['fr' => 'Choisir', 'en' => 'Choose'], 'body' => ['fr' => 'Pays, type de numero et contraintes locales.', 'en' => 'Country, number type and local constraints.']],
                ['label' => ['fr' => 'Connecter', 'en' => 'Connect'], 'body' => ['fr' => 'Routage vers SIP, IPBX ou file support.', 'en' => 'Routing to SIP, PBX or support queue.']],
                ['label' => ['fr' => 'Operer', 'en' => 'Operate'], 'body' => ['fr' => 'Mesure appels recus, manques et temps de reponse.', 'en' => 'Measure received calls, missed calls and response time.']],
            ],
        ],
        'sip' => [
            'proofs' => [
                ['icon' => 'bx-lock-alt', 'title' => ['fr' => 'Interconnexion securisee', 'en' => 'Secure interconnect'], 'body' => ['fr' => 'IP autorisees, TLS/SRTP et regles anti-fraude.', 'en' => 'Authorized IPs, TLS/SRTP and anti-fraud rules.']],
                ['icon' => 'bx-git-branch', 'title' => ['fr' => 'Failover', 'en' => 'Failover'], 'body' => ['fr' => 'Routes secondaires testables pour continuite voix.', 'en' => 'Testable backup routes for voice continuity.']],
                ['icon' => 'bx-server', 'title' => ['fr' => 'Compatibilite PBX', 'en' => 'PBX compatibility'], 'body' => ['fr' => 'Asterisk, FreeSWITCH, 3CX, Cisco et plateformes cloud.', 'en' => 'Asterisk, FreeSWITCH, 3CX, Cisco and cloud platforms.']],
            ],
            'workflow' => [
                ['label' => ['fr' => 'Auditer', 'en' => 'Audit'], 'body' => ['fr' => 'PBX, codecs, securite, capacite et heures de pointe.', 'en' => 'PBX, codecs, security, capacity and peak hours.']],
                ['label' => ['fr' => 'Configurer', 'en' => 'Configure'], 'body' => ['fr' => 'Trunks, IP, ACL, numerotation et routage.', 'en' => 'Trunks, IPs, ACLs, numbering and routing.']],
                ['label' => ['fr' => 'Tester', 'en' => 'Test'], 'body' => ['fr' => 'Appels entrants/sortants, failover et supervision.', 'en' => 'Inbound/outbound calls, failover and monitoring.']],
            ],
        ],
        'dialo' => [
            'proofs' => [
                ['icon' => 'bx-conversation', 'title' => ['fr' => 'Omnicanal', 'en' => 'Omnichannel'], 'body' => ['fr' => 'Voix, WhatsApp, SMS, email et chat dans un meme flux.', 'en' => 'Voice, WhatsApp, SMS, email and chat in one flow.']],
                ['icon' => 'bx-user-voice', 'title' => ['fr' => 'Supervision agents', 'en' => 'Agent supervision'], 'body' => ['fr' => 'Files, priorites, enregistrements et analytics.', 'en' => 'Queues, priorities, recordings and analytics.']],
                ['icon' => 'bx-bot', 'title' => ['fr' => 'Routage intelligent', 'en' => 'Smart routing'], 'body' => ['fr' => 'Distribution par competence, urgence et SLA.', 'en' => 'Distribution by skill, urgency and SLA.']],
            ],
            'workflow' => [
                ['label' => ['fr' => 'Cartographier', 'en' => 'Map'], 'body' => ['fr' => 'Canaux, files, horaires et roles superviseurs.', 'en' => 'Channels, queues, schedules and supervisor roles.']],
                ['label' => ['fr' => 'Integrer', 'en' => 'Integrate'], 'body' => ['fr' => 'Numeros, SIP, WhatsApp, SMS et CRM.', 'en' => 'Numbers, SIP, WhatsApp, SMS and CRM.']],
                ['label' => ['fr' => 'Piloter', 'en' => 'Operate'], 'body' => ['fr' => 'SLA, temps de reponse et qualite conversationnelle.', 'en' => 'SLA, response time and conversation quality.']],
            ],
        ],
        'esim' => [
            'proofs' => [
                ['icon' => 'bx-qr', 'title' => ['fr' => 'Activation QR', 'en' => 'QR activation'], 'body' => ['fr' => 'Profils eSIM activables avant le depart.', 'en' => 'eSIM profiles activatable before departure.']],
                ['icon' => 'bx-globe', 'title' => ['fr' => 'Forfaits pays/regions', 'en' => 'Country/region plans'], 'body' => ['fr' => 'Data mobile selon destination et duree de mission.', 'en' => 'Mobile data based on destination and mission length.']],
                ['icon' => 'bx-store', 'title' => ['fr' => 'Marque blanche', 'en' => 'White label'], 'body' => ['fr' => 'Distribution possible pour agences, travel et B2B.', 'en' => 'Distribution for agencies, travel and B2B channels.']],
            ],
            'workflow' => [
                ['label' => ['fr' => 'Selectionner', 'en' => 'Select'], 'body' => ['fr' => 'Pays, region, volume data et periode.', 'en' => 'Country, region, data volume and period.']],
                ['label' => ['fr' => 'Activer', 'en' => 'Activate'], 'body' => ['fr' => 'QR code et instructions simples pour l utilisateur.', 'en' => 'QR code and simple instructions for the user.']],
                ['label' => ['fr' => 'Suivre', 'en' => 'Track'], 'body' => ['fr' => 'Support, consommation et renouvellement.', 'en' => 'Support, usage and renewal.']],
            ],
        ],
    ],
];
