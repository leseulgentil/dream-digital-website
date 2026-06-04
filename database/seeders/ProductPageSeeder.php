<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductPageSeeder extends Seeder
{
    public function run(): void
    {
        $cards = Collection::make(config('dream-digital.services.home_cards', []));
        $details = config('dream-digital.product-pages.items', []);

        Collection::make(config('dream-digital.services.items', []))
            ->filter(fn (array $service): bool => (bool) ($service['active'] ?? true))
            ->each(function (array $service) use ($cards, $details): void {
                $slug = $service['slug'] ?? $service['id'] ?? null;

                if (! is_string($slug) || $slug === '') {
                    return;
                }

                foreach (['fr', 'en'] as $locale) {
                    $title = $this->localized($service['name'] ?? [], $locale, Str::headline($slug));
                    $lead = $this->localized($service['description'] ?? $service['tagline'] ?? [], $locale, '');
                    $card = $this->cardFor($cards, $slug);

                    Page::updateOrCreate(
                        [
                            'slug' => $slug,
                            'section' => 'product',
                            'country_id' => null,
                            'locale' => $locale,
                        ],
                        [
                            'title' => $title,
                            'meta_description' => Str::limit($lead, 280, ''),
                            'meta_image_path' => data_get($card, 'image.src'),
                            'content_blocks' => [
                                'seo_title' => Str::limit($title . ($locale === 'en' ? ' - Dream Digital product' : ' - Produit Dream Digital'), 68, ''),
                                'eyebrow' => $locale === 'en' ? 'Product' : 'Produit',
                                'lead' => $lead,
                                'image_alt' => $this->localized(data_get($card, 'image.alt', []), $locale, $title),
                                'seo_focus_keywords' => $this->keywordsFor($slug, $locale),
                                'faq' => $this->faqFor($title, $slug, $locale),
                                'internal_links' => $this->internalLinksFor($slug, $locale),
                                'sections' => $this->sectionsFor($title, $lead, $details[$slug] ?? [], $locale),
                            ],
                            'is_published' => true,
                            'editorial_status' => Page::STATUS_PUBLISHED,
                            'published_at' => now(),
                        ],
                    );
                }
            });

        $this->command?->info('Product pages seeded: ' . Page::where('section', 'product')->count() . ' entries.');
    }

    private function cardFor(Collection $cards, string $slug): ?array
    {
        return $cards->first(function (array $card) use ($slug): bool {
            $url = (string) data_get($card, 'cta.url', '');

            return str_contains($url, "/products/{$slug}");
        });
    }

    private function localized(mixed $value, string $locale, string $fallback): string
    {
        if (is_array($value)) {
            return (string) ($value[$locale] ?? $value['fr'] ?? $value['en'] ?? $fallback);
        }

        return (string) ($value ?: $fallback);
    }

    /**
     * @return array<int, string>
     */
    private function keywordsFor(string $slug, string $locale): array
    {
        $keywords = [
            'sms-a2p' => ['SMS A2P', 'OTP', 'DLR', 'CPaaS'],
            'voice' => ['Voice Wholesale', 'SIP', 'ASR', 'ACD'],
            'did' => ['DID Numbers', 'local numbers', 'inbound calls'],
            'sip' => ['SIP Trunking', 'PBX', 'TLS', 'failover'],
            'dialo' => ['Dialo', 'contact center', 'omnichannel'],
            'esim' => ['eSIM', 'mobile data', 'travel', 'white label'],
        ];

        $french = [
            'local numbers' => 'numeros locaux',
            'inbound calls' => 'appels entrants',
            'contact center' => 'centre de contact',
            'mobile data' => 'data mobile',
            'travel' => 'voyage',
            'white label' => 'marque blanche',
        ];

        return collect($keywords[$slug] ?? ['Dream Digital', 'CPaaS'])
            ->map(fn (string $keyword): string => $locale === 'fr' ? ($french[$keyword] ?? $keyword) : $keyword)
            ->values()
            ->all();
    }

    private function faqFor(string $title, string $slug, string $locale): array
    {
        if ($locale === 'en') {
            return [
                [
                    'question' => "Who is {$title} for?",
                    'answer' => "{$title} is designed for teams that need a reliable telecom service with clear routing, quality monitoring and commercial support.",
                ],
                [
                    'question' => 'What should we prepare before contacting Dream Digital?',
                    'answer' => 'Prepare target countries, expected volume, use case, quality expectations and launch timeline so the team can qualify the right setup.',
                ],
            ];
        }

        return [
            [
                'question' => "A qui s adresse {$title} ?",
                'answer' => "{$title} s adresse aux equipes qui ont besoin d un service telecom fiable avec routage clair, supervision qualite et accompagnement commercial.",
            ],
            [
                'question' => 'Que preparer avant de contacter Dream Digital ?',
                'answer' => 'Preparez les pays cibles, le volume attendu, le cas d usage, les attentes qualite et le calendrier de lancement pour qualifier la bonne configuration.',
            ],
        ];
    }

    private function internalLinksFor(string $slug, string $locale): array
    {
        $prefix = "/{$locale}";

        return [
            ['label' => $locale === 'en' ? 'Pricing' : 'Tarifs', 'url' => "{$prefix}/pricing"],
            ['label' => $locale === 'en' ? 'Coverage' : 'Couverture', 'url' => "{$prefix}/coverage"],
            ['label' => 'Contact', 'url' => "{$prefix}/contact?service={$slug}"],
        ];
    }

    private function sectionsFor(string $title, string $lead, array $detail, string $locale): array
    {
        $proofs = collect($detail['proofs'] ?? [])
            ->take(3)
            ->map(fn (array $proof): string => $this->localized($proof['title'] ?? [], $locale, 'Quality signal'))
            ->values();
        $workflow = collect($detail['workflow'] ?? [])
            ->take(3)
            ->map(fn (array $step): string => $this->localized($step['label'] ?? [], $locale, 'Step'))
            ->values();
        $proofsText = $proofs->implode(', ');
        $workflowText = $workflow->implode(' -> ');
        $proofsHtml = $proofs
            ->map(fn (string $proof): string => '<li>' . e($proof) . '</li>')
            ->implode('');

        if ($locale === 'en') {
            return [
                [
                    'heading' => 'Positioning',
                    'body' => "{$title} helps teams turn a telecom need into a controlled operational workflow. {$lead}",
                    'body_html' => '<p><strong>' . e($title) . '</strong> helps teams turn a telecom need into a controlled operational workflow.</p><p>' . e($lead) . '</p>',
                ],
                [
                    'heading' => 'Quality signals',
                    'body' => "The first quality checks to discuss are: {$proofsText}.",
                    'body_html' => '<p>The first quality checks to discuss are:</p><ul>' . $proofsHtml . '</ul>',
                ],
                [
                    'heading' => 'Launch workflow',
                    'body' => "Recommended workflow: {$workflowText}.",
                    'body_html' => '<p>Recommended workflow: <strong>' . e($workflowText) . '</strong>.</p>',
                ],
            ];
        }

        return [
            [
                'heading' => 'Positionnement',
                'body' => "{$title} aide les equipes a transformer un besoin telecom en workflow operationnel controle. {$lead}",
                'body_html' => '<p><strong>' . e($title) . '</strong> aide les equipes a transformer un besoin telecom en workflow operationnel controle.</p><p>' . e($lead) . '</p>',
            ],
                [
                    'heading' => 'Signaux qualite',
                    'body' => "Les premiers controles qualite a discuter sont : {$proofsText}.",
                    'body_html' => '<p>Les premiers controles qualite a discuter sont :</p><ul>' . $proofsHtml . '</ul>',
                ],
                [
                    'heading' => 'Workflow de lancement',
                    'body' => "Workflow recommande : {$workflowText}.",
                    'body_html' => '<p>Workflow recommande : <strong>' . e($workflowText) . '</strong>.</p>',
                ],
        ];
    }
}
