<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MarketingPageSeeder extends Seeder
{
    /**
     * Peuple la table `pages` (section='marketing') a partir du config
     * dream-digital.pages.pages. 7 slugs (products, developers, solutions,
     * coverage, pricing, company, contact) x 2 locales = 14 rows.
     *
     * Idempotent via updateOrCreate sur la cle composite. Les pages produit
     * detail (/{locale}/products/{service}) ne sont PAS migrees ici --
     * elles utilisent le service Eloquent comme source de verite. Seules
     * les pages section landing/hub le sont.
     */
    public function run(): void
    {
        $pages = config('dream-digital.pages.pages', []);

        foreach ($pages as $slug => $cfg) {
            foreach (['fr', 'en'] as $locale) {
                $title = $cfg['title'][$locale] ?? $cfg['title']['fr'] ?? $slug;
                $lead = $cfg['lead'][$locale] ?? $cfg['lead']['fr'] ?? null;
                $image = $this->imageFor($slug);

                Page::updateOrCreate(
                    [
                        'slug' => $slug,
                        'section' => 'marketing',
                        'country_id' => null,
                        'locale' => $locale,
                    ],
                    [
                        'title' => $title,
                        'meta_description' => $this->extractMetaDescription($cfg, $locale),
                        'meta_image_path' => $image['url'],
                        'content_blocks' => [
                            'seo_title' => $this->seoTitle($title, $locale),
                            'eyebrow' => $cfg['eyebrow'][$locale] ?? $cfg['eyebrow']['fr'] ?? null,
                            'lead' => $lead,
                            'image_alt' => $image['alt'][$locale] ?? $image['alt']['fr'],
                            'image_credit' => $image['credit'],
                            'image_source_url' => $image['source_url'],
                            'seo_focus_keywords' => $this->focusKeywords($slug, $locale),
                            'faq' => $this->faqFor($slug, $locale),
                            'internal_links' => $this->internalLinksFor($slug, $locale),
                            'sections' => $this->sectionsFor($slug, $locale, $title, (string) $lead),
                        ],
                        'is_published' => true,
                        'published_at' => now(),
                    ],
                );
            }
        }

        $this->command?->info('Marketing pages seeded: ' . Page::where('section', 'marketing')->count() . ' entries.');
    }

    private function extractMetaDescription(array $cfg, string $locale): ?string
    {
        $lead = $cfg['lead'][$locale] ?? $cfg['lead']['fr'] ?? null;
        if (!$lead) {
            return null;
        }
        return mb_substr($lead, 0, 280);
    }

    private function seoTitle(string $title, string $locale): string
    {
        $suffix = $locale === 'fr' ? ' - CPaaS telecom B2B' : ' - B2B telecom CPaaS';

        return Str::limit($title . $suffix, 68, '');
    }

    private function imageFor(string $slug): array
    {
        $images = [
            'products' => [
                'url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1600&q=80',
                'source_url' => 'https://unsplash.com/photos/person-using-macbook-pro-on-white-wooden-table-f06f85e504b3',
                'credit' => 'Photo Unsplash / John Schnobrich',
                'alt' => ['fr' => 'Catalogue de services telecom sur ordinateur', 'en' => 'Telecom services catalogue on a laptop'],
            ],
            'developers' => [
                'url' => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=1600&q=80',
                'source_url' => 'https://unsplash.com/photos/turned-on-monitor-displaying-code-dccba630e2f6',
                'credit' => 'Photo Unsplash / Ilya Pavlov',
                'alt' => ['fr' => 'Code API telecom pour developpeurs', 'en' => 'Telecom API code for developers'],
            ],
            'solutions' => [
                'url' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1600&q=80',
                'source_url' => 'https://unsplash.com/photos/people-sitting-down-near-table-with-assorted-laptop-computers-d307ca884978',
                'credit' => 'Photo Unsplash / Campaign Creators',
                'alt' => ['fr' => 'Equipe comparant des flux telecom metier', 'en' => 'Team comparing business telecom workflows'],
            ],
            'coverage' => [
                'url' => 'https://images.unsplash.com/photo-1524661135-423995f22d0b?auto=format&fit=crop&w=1600&q=80',
                'source_url' => 'https://unsplash.com/photos/aerial-photography-of-road-beside-mountain-during-daytime-423995f22d0b',
                'credit' => 'Photo Unsplash / CHUTTERSNAP',
                'alt' => ['fr' => 'Carte et routes internationales', 'en' => 'Map and international routes'],
            ],
            'pricing' => [
                'url' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=1600&q=80',
                'source_url' => 'https://unsplash.com/photos/person-writing-on-white-paper-6726b3ff858f',
                'credit' => 'Photo Unsplash / Kelly Sikkema',
                'alt' => ['fr' => 'Analyse tarifaire telecom et finance', 'en' => 'Telecom pricing and finance analysis'],
            ],
            'company' => [
                'url' => 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1600&q=80',
                'source_url' => 'https://unsplash.com/photos/people-sitting-near-table-with-laptop-computer-f200968a6e72',
                'credit' => 'Photo Unsplash / Annie Spratt',
                'alt' => ['fr' => 'Equipe Dream Digital en environnement de travail', 'en' => 'Dream Digital team in a workspace'],
            ],
            'contact' => [
                'url' => 'https://images.unsplash.com/photo-1560264280-88b68371db39?auto=format&fit=crop&w=1600&q=80',
                'source_url' => 'https://unsplash.com/photos/woman-in-white-long-sleeve-shirt-sitting-beside-man-in-gray-suit-jacket-88b68371db39',
                'credit' => 'Photo Unsplash / Arlington Research',
                'alt' => ['fr' => 'Equipe support discutant avec un client telecom', 'en' => 'Support team discussing with a telecom customer'],
            ],
        ];

        return $images[$slug] ?? $images['products'];
    }

    private function focusKeywords(string $slug, string $locale): array
    {
        $keywords = [
            'products' => ['CPaaS', 'SMS A2P', 'Voice API', 'DID', 'eSIM'],
            'developers' => ['API telecom', 'webhooks DLR', 'sandbox', 'integration CPaaS'],
            'solutions' => ['fintech', 'retail', 'logistique', 'notifications client'],
            'coverage' => ['couverture SMS', 'routes internationales', 'corridors telecom'],
            'pricing' => ['pricing telecom', 'tarifs SMS', 'tarifs voix', 'SLA'],
            'company' => ['operateur CPaaS', 'Afrique francophone', 'partenariats operateurs'],
            'contact' => ['contact telecom', 'devis CPaaS', 'support integration'],
        ];

        $english = [
            'API telecom' => 'telecom API',
            'webhooks DLR' => 'DLR webhooks',
            'integration CPaaS' => 'CPaaS integration',
            'fintech' => 'fintech',
            'retail' => 'retail',
            'logistique' => 'logistics',
            'notifications client' => 'customer notifications',
            'couverture SMS' => 'SMS coverage',
            'routes internationales' => 'international routes',
            'corridors telecom' => 'telecom corridors',
            'pricing telecom' => 'telecom pricing',
            'tarifs SMS' => 'SMS rates',
            'tarifs voix' => 'voice rates',
            'operateur CPaaS' => 'CPaaS operator',
            'Afrique francophone' => 'Francophone Africa',
            'partenariats operateurs' => 'carrier partnerships',
            'contact telecom' => 'telecom contact',
            'devis CPaaS' => 'CPaaS quote',
            'support integration' => 'integration support',
        ];

        return collect($keywords[$slug] ?? ['Dream Digital'])
            ->map(fn (string $keyword) => $locale === 'en' ? ($english[$keyword] ?? $keyword) : $keyword)
            ->values()
            ->all();
    }

    private function sectionsFor(string $slug, string $locale, string $title, string $lead): array
    {
        $keywords = implode(', ', $this->focusKeywords($slug, $locale));

        if ($locale === 'en') {
            return [
                [
                    'heading' => 'What this page helps clarify',
                    'body' => "{$title} gives buyers and technical teams a clear view of the Dream Digital approach. {$lead}",
                    'body_html' => '<p>' . e($title) . ' gives buyers and technical teams a clear view of the Dream Digital approach.</p><p>' . e($lead) . '</p>',
                ],
                [
                    'heading' => 'Useful SEO and business focus',
                    'body' => "The page is optimized around the following business terms: {$keywords}. The goal is to attract qualified traffic without hiding the practical operational details.",
                    'body_html' => '<p>The page is optimized around the following business terms: <strong>' . e($keywords) . '</strong>.</p><p>The goal is to attract qualified traffic without hiding the practical operational details.</p>',
                ],
                [
                    'heading' => 'Suggested conversion path',
                    'body' => 'Visitors should be able to move from discovery to action: compare the service, inspect coverage or pricing, then contact Dream Digital with route, volume and SLA context.',
                    'body_html' => '<p>Visitors should be able to move from discovery to action:</p><ul><li>Compare the service</li><li>Inspect coverage or pricing</li><li>Contact Dream Digital with route, volume and SLA context</li></ul>',
                ],
            ];
        }

        return [
            [
                'heading' => 'Ce que cette page clarifie',
                'body' => "{$title} donne aux acheteurs et aux equipes techniques une lecture claire de l approche Dream Digital. {$lead}",
                'body_html' => '<p>' . e($title) . ' donne aux acheteurs et aux equipes techniques une lecture claire de l approche Dream Digital.</p><p>' . e($lead) . '</p>',
            ],
            [
                'heading' => 'Focus SEO et business utile',
                'body' => "La page est optimisee autour des termes metier suivants: {$keywords}. L objectif est d attirer un trafic qualifie sans cacher les details operationnels importants.",
                'body_html' => '<p>La page est optimisee autour des termes metier suivants: <strong>' . e($keywords) . '</strong>.</p><p>L objectif est d attirer un trafic qualifie sans cacher les details operationnels importants.</p>',
            ],
            [
                'heading' => 'Parcours de conversion recommande',
                'body' => 'Le visiteur doit pouvoir passer de la decouverte a l action: comparer le service, consulter la couverture ou le pricing, puis contacter Dream Digital avec pays, volume et SLA.',
                'body_html' => '<p>Le visiteur doit pouvoir passer de la decouverte a l action:</p><ul><li>Comparer le service</li><li>Consulter la couverture ou le pricing</li><li>Contacter Dream Digital avec pays, volume et SLA</li></ul>',
            ],
        ];
    }

    private function faqFor(string $slug, string $locale): array
    {
        $keywords = implode(', ', $this->focusKeywords($slug, $locale));
        $topic = [
            'products' => ['fr' => 'le catalogue CPaaS', 'en' => 'the CPaaS catalogue'],
            'developers' => ['fr' => 'les APIs telecom', 'en' => 'telecom APIs'],
            'solutions' => ['fr' => 'les solutions metier', 'en' => 'business solutions'],
            'coverage' => ['fr' => 'la couverture internationale', 'en' => 'international coverage'],
            'pricing' => ['fr' => 'le pricing telecom', 'en' => 'telecom pricing'],
            'company' => ['fr' => 'Dream Digital', 'en' => 'Dream Digital'],
            'contact' => ['fr' => 'la prise de contact', 'en' => 'the contact process'],
        ][$slug][$locale] ?? 'Dream Digital';

        if ($locale === 'en') {
            return [
                [
                    'question' => "What should teams check before choosing {$topic}?",
                    'answer' => "They should clarify target countries, channels, monthly volumes, quality expectations and operational ownership. This makes the discussion concrete and keeps the recommendation aligned with {$keywords}.",
                ],
                [
                    'question' => 'Can Dream Digital support both technical and business teams?',
                    'answer' => 'Yes. The project can be framed for technical integration, routing quality, pricing visibility and commercial decision-making so that product, support and sales teams work from the same facts.',
                ],
            ];
        }

        return [
            [
                'question' => "Que verifier avant de choisir {$topic} ?",
                'answer' => "Il faut clarifier les pays cibles, les canaux, les volumes mensuels, les attentes qualite et les responsables operationnels. Cela rend l echange concret et aligne la recommandation avec {$keywords}.",
            ],
            [
                'question' => 'Dream Digital peut-il accompagner les equipes techniques et business ?',
                'answer' => 'Oui. Le projet peut etre cadre autour de l integration technique, de la qualite de routage, de la visibilite pricing et de la decision commerciale pour aligner produit, support et sales.',
            ],
        ];
    }

    private function internalLinksFor(string $slug, string $locale): array
    {
        $prefix = "/{$locale}";

        return match ($slug) {
            'products' => [
                ['label' => 'SMS A2P', 'url' => "{$prefix}/products/sms-a2p"],
                ['label' => 'Voice API', 'url' => "{$prefix}/products/voice"],
                ['label' => 'Pricing', 'url' => "{$prefix}/pricing"],
            ],
            'developers' => [
                ['label' => 'API guides', 'url' => "{$prefix}/blog/webhooks-dlr-monitoring-temps-reel"],
                ['label' => 'Contact integration', 'url' => "{$prefix}/contact"],
            ],
            'solutions' => [
                ['label' => 'Fintech CPaaS', 'url' => "{$prefix}/blog/cpaas-fintech-banques-otp"],
                ['label' => 'Coverage', 'url' => "{$prefix}/coverage"],
            ],
            'coverage' => [
                ['label' => 'Pricing corridors', 'url' => "{$prefix}/pricing"],
                ['label' => 'Contact routes', 'url' => "{$prefix}/contact"],
            ],
            'pricing' => [
                ['label' => 'Coverage', 'url' => "{$prefix}/coverage"],
                ['label' => 'Contact sales', 'url' => "{$prefix}/contact"],
            ],
            default => [
                ['label' => 'Products', 'url' => "{$prefix}/products"],
                ['label' => 'Contact', 'url' => "{$prefix}/contact"],
            ],
        };
    }
}
