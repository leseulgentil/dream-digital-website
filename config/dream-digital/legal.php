<?php

/*
|--------------------------------------------------------------------------
| Dream Digital -- Pages legales
|--------------------------------------------------------------------------
|
| Trois pages legales obligatoires avant DD_PUBLIC_INDEXABLE=true :
| Mentions legales, Conditions Generales d'Utilisation, Politique RGPD
| et cookies.
|
| Contenu en draft fr/en, generique-credible, **a faire valider par
| juriste/avocat** avant ouverture publique (cf. last_updated par slug).
|
| Architecture CMS-ready : ce contenu sera migre vers la table `pages`
| (Eloquent Model App\Models\Page) en Sprint CMS futur sans changement
| frontend.
|
| Acces Blade :
|   config('dream-digital.legal.pages.mentions.title.fr')
|   config('dream-digital.legal.pages.cgu.sections')
|
*/

return [
    'pages' => [

        'mentions' => [
            'slug'         => 'mentions',
            'title'        => ['fr' => 'Mentions legales', 'en' => 'Legal notice'],
            'eyebrow'      => ['fr' => 'Information legale', 'en' => 'Legal information'],
            'lead'         => [
                'fr' => 'Informations relatives a l\'editeur du site dream-digital.info, conformement a la loi pour la confiance dans l\'economie numerique (LCEN) et aux exigences equivalentes en zone OHADA / UEMOA.',
                'en' => 'Publisher information for dream-digital.info, in compliance with applicable laws (LCEN, GDPR, OHADA/UEMOA equivalents).',
            ],
            'last_updated' => '2026-05-12',
            'sections'     => [
                [
                    'heading' => ['fr' => 'Editeur du site', 'en' => 'Publisher'],
                    'body'    => [
                        'fr' => "Dream Digital -- operateur telecom CPaaS / ITSP.\nSiege social principal : Kinshasa, Republique Democratique du Congo.\nBureaux operationnels : Abidjan (Cote d'Ivoire), Brazzaville (Republique du Congo).\nContact commercial : sales@dream-digital.info\n\nForme juridique et numero d'immatriculation a completer apres constitution juridique formelle de la societe.",
                        'en' => "Dream Digital -- CPaaS / ITSP telecom operator.\nMain registered office: Kinshasa, Democratic Republic of the Congo.\nOperational offices: Abidjan (Cote d'Ivoire), Brazzaville (Republic of the Congo).\nCommercial contact: sales@dream-digital.info\n\nLegal form and company registration number to be added after formal incorporation.",
                    ],
                ],
                [
                    'heading' => ['fr' => 'Directeur de la publication', 'en' => 'Publication director'],
                    'body'    => [
                        'fr' => "MAPENDO Gentil, fondateur de Dream Digital.\nContact : sales@dream-digital.info",
                        'en' => "MAPENDO Gentil, Dream Digital founder.\nContact: sales@dream-digital.info",
                    ],
                ],
                [
                    'heading' => ['fr' => 'Hebergeur', 'en' => 'Hosting provider'],
                    'body'    => [
                        'fr' => "OVH SAS\n2 rue Kellermann, 59100 Roubaix, France\nSiret : 424 761 419 00045\nTelephone : +33 9 72 10 10 07\nSite : https://www.ovhcloud.com",
                        'en' => "OVH SAS\n2 rue Kellermann, 59100 Roubaix, France\nSiret: 424 761 419 00045\nPhone: +33 9 72 10 10 07\nWebsite: https://www.ovhcloud.com",
                    ],
                ],
                [
                    'heading' => ['fr' => 'Propriete intellectuelle', 'en' => 'Intellectual property'],
                    'body'    => [
                        'fr' => "L'ensemble des contenus presents sur dream-digital.info (textes, logos, illustrations, code source des composants frontend public) sont la propriete exclusive de Dream Digital ou de leurs auteurs respectifs, sauf mention contraire explicite. Toute reproduction ou diffusion sans autorisation prealable est interdite.",
                        'en' => "All content on dream-digital.info (text, logos, illustrations, public frontend component source code) is the exclusive property of Dream Digital or their respective authors unless otherwise stated. Any reproduction or distribution without prior written authorization is prohibited.",
                    ],
                ],
                [
                    'heading' => ['fr' => 'Credits', 'en' => 'Credits'],
                    'body'    => [
                        'fr' => "Identite visuelle, brand kit et copy : Dream Digital interne.\nIconographie : Boxicons (https://boxicons.com).\nTypographies : Inter + JetBrains Mono via Google Fonts.",
                        'en' => "Visual identity, brand kit and copy: Dream Digital internal.\nIconography: Boxicons (https://boxicons.com).\nTypography: Inter + JetBrains Mono via Google Fonts.",
                    ],
                ],
            ],
        ],

        'cgu' => [
            'slug'         => 'cgu',
            'title'        => ['fr' => 'Conditions generales d\'utilisation', 'en' => 'Terms of use'],
            'eyebrow'      => ['fr' => 'CGU', 'en' => 'Terms'],
            'lead'         => [
                'fr' => "Les presentes Conditions Generales d'Utilisation regissent l'acces et l'usage du site vitrine dream-digital.info. L'utilisation de nos APIs telecom (SMS A2P, Voice, DID, SIP Trunking, eSIM) fait l'objet d'un contrat commercial separe.",
                'en' => "These Terms of Use govern access to and use of the dream-digital.info corporate website. Use of our telecom APIs (SMS A2P, Voice, DID, SIP Trunking, eSIM) is subject to a separate commercial agreement.",
            ],
            'last_updated' => '2026-05-12',
            'sections'     => [
                [
                    'heading' => ['fr' => 'Objet', 'en' => 'Purpose'],
                    'body'    => [
                        'fr' => "Le site dream-digital.info presente l'offre commerciale Dream Digital (operateur CPaaS / ITSP : SMS A2P, Voice Wholesale, DID Numbers, SIP Trunking, Dialo Contact Center, eSIM Zone) ainsi que les informations corporate. Il n'est pas un portail transactionnel : aucun produit ne s'achete directement en ligne. Les contrats services se finalisent par echange avec l'equipe commerciale.",
                        'en' => "The dream-digital.info website presents Dream Digital's commercial offering (CPaaS / ITSP operator: SMS A2P, Voice Wholesale, DID Numbers, SIP Trunking, Dialo Contact Center, eSIM Zone) and corporate information. It is not a transactional portal: no product is purchased directly online. Service contracts are finalized through engagement with the commercial team.",
                    ],
                ],
                [
                    'heading' => ['fr' => 'Acces au site', 'en' => 'Site access'],
                    'body'    => [
                        'fr' => "L'acces au site est libre et gratuit. L'utilisateur s'engage a ne pas perturber le fonctionnement normal du site (scraping massif non autorise, tentatives d'intrusion, deni de service). Dream Digital se reserve le droit de bloquer l'acces a toute IP ou agent presentant un comportement abusif.",
                        'en' => "Access to the website is free and unrestricted. Users agree not to disrupt normal site operation (unauthorized mass scraping, intrusion attempts, denial-of-service). Dream Digital reserves the right to block access from any IP or user agent showing abusive behavior.",
                    ],
                ],
                [
                    'heading' => ['fr' => 'Liens externes', 'en' => 'External links'],
                    'body'    => [
                        'fr' => "Le site peut comporter des liens vers des ressources externes (operateurs partenaires, frameworks open-source, references techniques). Dream Digital ne controle pas le contenu de ces sites tiers et decline toute responsabilite quant a leur disponibilite ou exactitude.",
                        'en' => "The site may include links to external resources (partner carriers, open-source frameworks, technical references). Dream Digital does not control the content of these third-party sites and assumes no responsibility for their availability or accuracy.",
                    ],
                ],
                [
                    'heading' => ['fr' => 'Limitation de responsabilite', 'en' => 'Limitation of liability'],
                    'body'    => [
                        'fr' => "Les informations publiees (chiffres de couverture, SLA cible, tarifs indicatifs, descriptions produits) sont fournies a titre informatif et peuvent evoluer. Dream Digital met tout en oeuvre pour garantir leur exactitude mais ne peut etre tenu responsable d'eventuelles imprecisions, erreurs ou omissions. Les engagements contractuels effectifs sont definis dans les contrats commerciaux signes avec chaque client.",
                        'en' => "Published information (coverage figures, SLA targets, indicative pricing, product descriptions) is provided for informational purposes and may change. Dream Digital strives to ensure accuracy but cannot be held liable for any inaccuracies, errors or omissions. Effective contractual commitments are defined in signed commercial agreements with each client.",
                    ],
                ],
                [
                    'heading' => ['fr' => 'Modification des CGU', 'en' => 'Terms updates'],
                    'body'    => [
                        'fr' => "Dream Digital se reserve le droit de modifier les presentes CGU a tout moment. La version applicable est celle en vigueur au moment de l'acces au site, datee de la mention `last_updated` ci-dessus.",
                        'en' => "Dream Digital reserves the right to amend these Terms at any time. The applicable version is the one in force at the time of site access, dated `last_updated` above.",
                    ],
                ],
                [
                    'heading' => ['fr' => 'Droit applicable et juridictions', 'en' => 'Applicable law and jurisdiction'],
                    'body'    => [
                        'fr' => "Les presentes CGU sont regies par le droit congolais (RDC). Tout litige relatif a leur interpretation ou execution relevera de la competence des tribunaux de Kinshasa, sous reserve des regles de competence d'ordre public applicables (notamment dispositions internationales et conventions OHADA).",
                        'en' => "These Terms are governed by the laws of the Democratic Republic of the Congo. Any dispute regarding their interpretation or execution falls under the jurisdiction of the courts of Kinshasa, subject to applicable public-order rules (including international provisions and OHADA conventions).",
                    ],
                ],
            ],
        ],

        'rgpd' => [
            'slug'         => 'rgpd',
            'title'        => ['fr' => 'Politique de confidentialite et cookies', 'en' => 'Privacy policy and cookies'],
            'eyebrow'      => ['fr' => 'RGPD / Confidentialite', 'en' => 'GDPR / Privacy'],
            'lead'         => [
                'fr' => "Dream Digital traite vos donnees personnelles dans le respect du Reglement General sur la Protection des Donnees (RGPD, UE 2016/679) et des reglementations equivalentes en RDC, Cote d'Ivoire et Republique du Congo. Cette page detaille les finalites, les bases legales et vos droits.",
                'en' => "Dream Digital processes your personal data in accordance with the General Data Protection Regulation (GDPR, EU 2016/679) and equivalent regulations in DRC, Cote d'Ivoire and Republic of the Congo. This page details purposes, legal bases and your rights.",
            ],
            'last_updated' => '2026-05-12',
            'sections'     => [
                [
                    'heading' => ['fr' => 'Donnees collectees', 'en' => 'Data collected'],
                    'body'    => [
                        'fr' => "Sur le site vitrine : aucune donnee personnelle n'est collectee passivement au-dela des logs serveur techniques (IP, user-agent, page visitee, horodatage) conserves 12 mois maximum a des fins de securite et de diagnostic.\n\nLorsque vous nous contactez (sales@dream-digital.info) : les donnees fournies (nom, email, entreprise, message) sont conservees pour gerer votre demande et le suivi commercial. Conservation : 3 ans apres dernier contact.\n\nSi vous etes client : le contrat commercial signe definit les donnees techniques et operationnelles traitees (numeros, flux trafic, journaux DLR, etc.) en sa qualite d'operateur telecom.",
                        'en' => "On the corporate website: no personal data is passively collected beyond technical server logs (IP, user-agent, visited page, timestamp) retained for a maximum of 12 months for security and diagnostic purposes.\n\nWhen you contact us (sales@dream-digital.info): the data provided (name, email, company, message) is retained to handle your request and commercial follow-up. Retention: 3 years after last contact.\n\nIf you are a client: the signed commercial agreement defines the technical and operational data processed (numbers, traffic flows, DLR logs, etc.) in our role as a telecom operator.",
                    ],
                ],
                [
                    'heading' => ['fr' => 'Bases legales', 'en' => 'Legal bases'],
                    'body'    => [
                        'fr' => "- Interet legitime pour les logs techniques (securite, diagnostic, lutte contre l'abus).\n- Consentement et execution de mesures precontractuelles pour vos demandes commerciales.\n- Execution du contrat pour les traitements lies aux services telecom souscrits.",
                        'en' => "- Legitimate interest for technical logs (security, diagnostics, abuse prevention).\n- Consent and pre-contractual measures for your commercial inquiries.\n- Contract performance for processing related to subscribed telecom services.",
                    ],
                ],
                [
                    'heading' => ['fr' => 'Cookies', 'en' => 'Cookies'],
                    'body'    => [
                        'fr' => "Le site utilise des cookies strictement necessaires a son fonctionnement (session Laravel, preference de theme dark/light via la cle `dd-theme`, langue selectionnee, pays detecte via cookie `dd_country_pref`). Aucun cookie tiers de tracking publicitaire n'est depose. Aucun consentement explicite n'est requis pour ces cookies fonctionnels au sens RGPD (article 82 LIL).",
                        'en' => "The site uses cookies strictly necessary for its operation (Laravel session, dark/light theme preference via the `dd-theme` key, selected language, country detected via `dd_country_pref` cookie). No third-party advertising tracking cookies are dropped. No explicit consent is required for these functional cookies under GDPR (Article 82 LIL).",
                    ],
                ],
                [
                    'heading' => ['fr' => 'Vos droits', 'en' => 'Your rights'],
                    'body'    => [
                        'fr' => "Conformement au RGPD, vous disposez d'un droit d'acces, de rectification, d'effacement, de limitation, d'opposition et de portabilite sur vos donnees personnelles. Vous pouvez exercer ces droits en ecrivant a sales@dream-digital.info en precisant l'objet de votre demande et en justifiant de votre identite.\n\nVous disposez egalement du droit d'introduire une reclamation aupres de l'autorite de protection des donnees competente.",
                        'en' => "Under GDPR, you have rights of access, rectification, erasure, restriction, objection and portability over your personal data. You can exercise these rights by writing to sales@dream-digital.info, stating the purpose of your request and providing proof of identity.\n\nYou also have the right to lodge a complaint with the competent data protection authority.",
                    ],
                ],
                [
                    'heading' => ['fr' => 'Transferts hors zone d\'origine', 'en' => 'Cross-border data transfers'],
                    'body'    => [
                        'fr' => "Le site est heberge en France (OVH, Roubaix) -- un transfert intra-UE soumis aux memes garanties RGPD que la collecte. En cas de necessite operationnelle (support, monitoring), les donnees peuvent etre traitees par des prestataires localises dans des juridictions equivalentes encadrees par des clauses contractuelles types.",
                        'en' => "The site is hosted in France (OVH, Roubaix) -- an intra-EU transfer subject to the same GDPR safeguards as collection. Where operationally necessary (support, monitoring), data may be processed by service providers in equivalent jurisdictions covered by standard contractual clauses.",
                    ],
                ],
                [
                    'heading' => ['fr' => 'Contact DPO', 'en' => 'DPO contact'],
                    'body'    => [
                        'fr' => "En l'absence d'un delegue a la protection des donnees nomme formellement, toute demande RGPD doit etre adressee a : sales@dream-digital.info. Un DPO sera designe et notifie sur cette page des que les obligations legales le rendent necessaire (volume de traitement, sensibilite des donnees clients).",
                        'en' => "Pending the formal appointment of a Data Protection Officer, all GDPR requests must be sent to: sales@dream-digital.info. A DPO will be appointed and announced here as soon as legal obligations require it (processing volume, client data sensitivity).",
                    ],
                ],
            ],
        ],

    ],
];
