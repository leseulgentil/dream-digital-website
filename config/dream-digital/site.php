<?php

/*
|--------------------------------------------------------------------------
| Dream Digital — Site metadata (global)
|--------------------------------------------------------------------------
|
| Métadonnées globales du site corporate Dream Digital. Source : copy
| validé PO 2026-05-08 (draft 2 page d'attente, réutilisé pour Sprint 1.5
| hero + sections).
|
| Utilisation côté Blade :
|   {{ config('dream-digital.site.tagline.fr') }}
|   {{ config('dream-digital.site.contact.email_sales') }}
|
|--------------------------------------------------------------------------
| NULL VALUE CONVENTION (per paulrberg review MED-5, 2026-05-11)
|--------------------------------------------------------------------------
|
| Certaines clés ci-dessous sont volontairement à `null` (ex.
| `contact.email_support`, `contact.phone`, `social.*`,
| `company.legal_name`, `meta.og_image`). Elles attendent un input PO.
|
| RÈGLE BLADE — TOUJOURS utiliser le null-coalescing (`??`) ou un `@if`
| en garde lors du rendu, pour éviter du markup cassé silencieux.
|
| Exemples :
|   ✅ {{ config('dream-digital.site.contact.email_support') ?? '' }}
|   ✅ @if($url = config('dream-digital.site.social.linkedin'))
|        <a href="{{ $url }}">LinkedIn</a>
|      @endif
|   ❌ <a href="tel:{{ config('dream-digital.site.contact.phone') }}">
|        // produit <a href="tel:"> qui casse le protocole tel:
|
| À retirer cette convention quand toutes les clés `null` auront été
| renseignées et qu'il n'en restera plus dans le fichier.
|
*/

return [
    'tagline' => [
        'fr' => 'Voice. SMS. eSIM. And More.',
        'en' => 'Voice. SMS. eSIM. And More.',
    ],

    'sub_headline' => [
        'fr' => 'Dream Digital est l\'opérateur télécom CPaaS et ITSP qui connecte les entreprises modernes à plus de 200 pays. Voix, messagerie, data mobile, SIP trunking — sous une seule plateforme, avec une exigence carrier-grade.',
        'en' => 'Dream Digital est l\'opérateur télécom CPaaS et ITSP qui connecte les entreprises modernes à plus de 200 pays. Voix, messagerie, data mobile, SIP trunking — sous une seule plateforme, avec une exigence carrier-grade.',
    ],

    'pitch' => [
        'title' => [
            'fr' => 'Une infrastructure télécom globale, des équipes proches de vous',
            'en' => 'Une infrastructure télécom globale, des équipes proches de vous',
        ],
        'paragraphs' => [
            [
                'fr' => 'Depuis nos bureaux opérationnels à Kinshasa, Abidjan et Brazzaville, Dream Digital opère une plateforme de communications unifiées qui sert clients et partenaires sur tous les continents. 60% de notre clientèle et 80% de notre écosystème de partenaires se trouvent en dehors de l\'Afrique — preuve qu\'une expertise ancrée localement peut servir une ambition globale.',
                'en' => 'Depuis nos bureaux opérationnels à Kinshasa, Abidjan et Brazzaville, Dream Digital opère une plateforme de communications unifiées qui sert clients et partenaires sur tous les continents. 60% de notre clientèle et 80% de notre écosystème de partenaires se trouvent en dehors de l\'Afrique — preuve qu\'une expertise ancrée localement peut servir une ambition globale.',
            ],
            [
                'fr' => 'Notre approche : la fiabilité et la latence d\'un opérateur historique, l\'agilité d\'un acteur tech moderne, et des prix calibrés pour les marchés émergents comme pour les économies matures. Nous travaillons aux côtés de fintechs, opérateurs mobiles, BPO, intégrateurs et éditeurs SaaS qui ont besoin de canaux de communication réellement scalables.',
                'en' => 'Notre approche : la fiabilité et la latence d\'un opérateur historique, l\'agilité d\'un acteur tech moderne, et des prix calibrés pour les marchés émergents comme pour les économies matures. Nous travaillons aux côtés de fintechs, opérateurs mobiles, BPO, intégrateurs et éditeurs SaaS qui ont besoin de canaux de communication réellement scalables.',
            ],
        ],
    ],

    'transition_cta' => [
        'title' => [
            'fr' => 'Notre site complet arrive bientôt',
            'en' => 'Our full site is coming soon',
        ],
        'text' => [
            'fr' => 'Vous lisez actuellement une version courte de notre présentation. Notre nouveau site corporate, avec catalogue produit détaillé, tarifs en ligne, documentation API, portail self-service et préview eSIM, sera mis en ligne dans les prochaines semaines. D\'ici là, nous restons disponibles pour discuter de votre projet de communication. Que vous ayez besoin d\'un proof-of-concept, d\'une étude tarifaire pour une route précise, ou simplement d\'échanger sur vos enjeux télécom — n\'hésitez pas.',
            'en' => 'Vous lisez actuellement une version courte de notre présentation. Notre nouveau site corporate, avec catalogue produit détaillé, tarifs en ligne, documentation API, portail self-service et préview eSIM, sera mis en ligne dans les prochaines semaines. D\'ici là, nous restons disponibles pour discuter de votre projet de communication. Que vous ayez besoin d\'un proof-of-concept, d\'une étude tarifaire pour une route précise, ou simplement d\'échanger sur vos enjeux télécom — n\'hésitez pas.',
        ],
        'cta_primary' => [
            'fr' => 'Échanger avec notre équipe',
            'en' => 'Talk to our team',
        ],
        'cta_secondary' => [
            'fr' => 'Recevoir une étude tarifaire personnalisée',
            'en' => 'Get a custom quote',
        ],
    ],

    'contact' => [
        'email_sales'   => 'sales@dream-digital.info',
        'email_support' => null,  // à fournir par PO
        'phone'         => null,  // à fournir par PO
    ],

    'social' => [
        'linkedin' => null,  // URL à fournir par PO
        'twitter'  => null,
        'github'   => null,
    ],

    'company' => [
        'name'       => 'Dream Digital',
        'legal_name' => null,  // SARL / SAS / etc. à fournir par PO
        'offices'    => [
            'Kinshasa (RDC)',
            'Abidjan (CI)',
            'Brazzaville (CG)',
        ],
    ],

    'meta' => [
        'title_default'       => 'Dream Digital — CPaaS / ITSP carrier-grade',
        'description_default' => 'Voice. SMS. eSIM. And More. L\'opérateur télécom qui connecte les entreprises modernes à 200+ pays.',
        'og_image'            => '/img/og/dream-digital-default.png',  // à fournir par PO
    ],
];
