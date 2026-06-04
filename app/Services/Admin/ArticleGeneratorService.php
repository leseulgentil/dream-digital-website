<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ArticleGeneratorService
{
    public function generate(array $input): array
    {
        return $this->generateWithMetadata($input)['articles'];
    }

    public function generateWithMetadata(array $input): array
    {
        $normalized = $this->normalizeInput($input);
        $provider = $this->provider();

        if ($provider === 'openai' && blank(config('services.openai.api_key'))) {
            return $this->fallbackResult($normalized, 'missing_api_key');
        }

        if ($provider === 'openai') {
            try {
                return [
                    'articles' => $this->generateWithOpenAi($normalized),
                    'provider' => 'openai',
                    'model' => $this->model(),
                    'fallback_used' => false,
                    'fallback_reason' => null,
                ];
            } catch (Throwable $exception) {
                if (! $this->fallbackOnFailure()) {
                    throw new RuntimeException('Generation OpenAI indisponible: ' . $exception->getMessage(), previous: $exception);
                }

                Log::warning('OpenAI article generation failed; using local fallback.', [
                    'message' => $exception->getMessage(),
                ]);

                return $this->fallbackResult($normalized, 'openai_error');
            }
        }

        return [
            'articles' => $this->generateLocal($normalized),
            'provider' => 'local',
            'model' => null,
            'fallback_used' => false,
            'fallback_reason' => null,
        ];
    }

    private function generateLocal(array $input): array
    {
        $locale = $input['locale'];
        $idea = $input['idea'];
        $keywords = $input['keywords'];
        $guidelines = $input['guidelines'];
        $variants = $input['variants'];

        return collect(range(1, $variants))
            ->map(fn (int $index) => $this->variant($idea, $keywords, $guidelines, $locale, $index, $input['target_section'], $input['target_slug']))
            ->all();
    }

    private function generateWithOpenAi(array $input): array
    {
        $response = Http::acceptJson()
            ->withToken((string) config('services.openai.api_key'))
            ->timeout((int) config('services.openai.timeout', 45))
            ->post(rtrim((string) config('services.openai.base_url', 'https://api.openai.com/v1'), '/') . '/responses', $this->openAiPayload($input));

        if ($response->failed()) {
            throw new RuntimeException('OpenAI API returned HTTP ' . $response->status());
        }

        $decoded = json_decode($this->extractOutputText($response->json()), true, 512, JSON_THROW_ON_ERROR);
        $articles = $decoded['articles'] ?? [];

        if (! is_array($articles) || $articles === []) {
            throw new RuntimeException('OpenAI API returned no article variants.');
        }

        return collect($articles)
            ->take($input['variants'])
            ->map(fn (array $article, int $index) => $this->normalizeArticle($article, $input, $index + 1))
            ->values()
            ->all();
    }

    private function provider(): string
    {
        $provider = strtolower((string) config('services.openai.article_provider', 'local'));

        return in_array($provider, ['local', 'openai'], true)
            ? $provider
            : 'local';
    }

    private function model(): string
    {
        return (string) config('services.openai.model', 'gpt-5-mini');
    }

    private function fallbackOnFailure(): bool
    {
        return (bool) config('services.openai.fallback_on_failure', true);
    }

    private function fallbackResult(array $input, string $reason): array
    {
        if (! $this->fallbackOnFailure()) {
            throw new RuntimeException("Generation OpenAI indisponible ({$reason}).");
        }

        return [
            'articles' => $this->generateLocal($input),
            'provider' => 'local',
            'model' => null,
            'fallback_used' => true,
            'fallback_reason' => $reason,
        ];
    }

    private function normalizeInput(array $input): array
    {
        $targetSection = $input['target_section'] ?? 'blog';

        return [
            'locale' => in_array($input['locale'] ?? 'fr', ['fr', 'en'], true) ? $input['locale'] : 'fr',
            'idea' => trim((string) $input['idea']),
            'keywords' => $this->keywords($input['keywords'] ?? ''),
            'guidelines' => trim((string) ($input['guidelines'] ?? '')),
            'variants' => max(1, min(5, (int) ($input['variants'] ?? 3))),
            'target_section' => in_array($targetSection, ['blog', 'product'], true)
                ? $targetSection
                : 'blog',
            'target_slug' => Str::slug((string) ($input['target_slug'] ?? '')),
        ];
    }

    private function openAiPayload(array $input): array
    {
        $localeLabel = $input['locale'] === 'fr' ? 'francais' : 'English';
        $keywordLine = $input['keywords'] ? implode(', ', $input['keywords']) : 'CPaaS, SMS A2P, Voice API, telecom B2B';
        $guidelines = $input['guidelines'] !== '' ? $input['guidelines'] : 'Ton expert, concret, commercial, utile pour des decideurs B2B.';

        $contentType = $input['target_section'] === 'product' ? 'product page' : 'SEO blog article';

        return [
            'model' => $this->model(),
            'instructions' => "You are Dream Digital's senior SEO editor for programmable telecom, CPaaS, SMS A2P, Voice API, DID, eSIM, cloud and digital transformation. Return only valid JSON matching the schema.",
            'input' => [[
                'role' => 'user',
                'content' => [[
                    'type' => 'input_text',
                    'text' => implode("\n", [
                        "Generate {$input['variants']} complete {$contentType} variant(s) in {$localeLabel}.",
                        "Main idea: {$input['idea']}",
                        "Target keywords: {$keywordLine}",
                        "Editorial guidelines: {$guidelines}",
                        "CMS section: {$input['target_section']}",
                        $input['target_slug'] !== '' ? "Required slug: {$input['target_slug']}" : 'Choose a clean SEO slug.',
                        'Each article must be ready to paste into a Laravel CMS form.',
                        'Use factual, non-hype language, practical examples, and HTML paragraphs/lists in body_html.',
                        'Include 2 to 4 concise FAQ entries that answer buyer/search-intent questions.',
                        'Use real-looking Unsplash image URLs when useful, or leave image fields coherent with telecom/business imagery.',
                    ]),
                ]],
            ]],
            'text' => [
                'format' => [
                    'type' => 'json_schema',
                    'name' => 'dream_digital_article_variants',
                    'strict' => true,
                    'schema' => $this->openAiSchema($input),
                ],
            ],
            'max_output_tokens' => (int) config('services.openai.max_output_tokens', 9000),
        ];
    }

    private function openAiSchema(array $input): array
    {
        $locale = $input['locale'];

        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['articles'],
            'properties' => [
                'articles' => [
                    'type' => 'array',
                    'minItems' => 1,
                    'maxItems' => 5,
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'required' => [
                            'title',
                            'slug',
                            'section',
                            'locale',
                            'seo_title',
                            'meta_description',
                            'eyebrow',
                            'lead',
                            'author',
                            'reading_time',
                            'tags',
                            'meta_image_path',
                            'image_alt',
                            'image_credit',
                            'image_source_url',
                            'faq',
                            'sections',
                        ],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'section' => ['type' => 'string', 'enum' => [$input['target_section']]],
                            'locale' => ['type' => 'string', 'enum' => [$locale]],
                            'seo_title' => ['type' => 'string'],
                            'meta_description' => ['type' => 'string'],
                            'eyebrow' => ['type' => 'string'],
                            'lead' => ['type' => 'string'],
                            'author' => ['type' => 'string'],
                            'reading_time' => ['type' => 'string'],
                            'tags' => [
                                'type' => 'array',
                                'minItems' => 3,
                                'maxItems' => 8,
                                'items' => ['type' => 'string'],
                            ],
                            'meta_image_path' => ['type' => 'string'],
                            'image_alt' => ['type' => 'string'],
                            'image_credit' => ['type' => 'string'],
                            'image_source_url' => ['type' => 'string'],
                            'faq' => [
                                'type' => 'array',
                                'minItems' => 2,
                                'maxItems' => 4,
                                'items' => [
                                    'type' => 'object',
                                    'additionalProperties' => false,
                                    'required' => ['question', 'answer'],
                                    'properties' => [
                                        'question' => ['type' => 'string'],
                                        'answer' => ['type' => 'string'],
                                    ],
                                ],
                            ],
                            'sections' => [
                                'type' => 'array',
                                'minItems' => 4,
                                'maxItems' => 7,
                                'items' => [
                                    'type' => 'object',
                                    'additionalProperties' => false,
                                    'required' => ['heading', 'body', 'body_html'],
                                    'properties' => [
                                        'heading' => ['type' => 'string'],
                                        'body' => ['type' => 'string'],
                                        'body_html' => ['type' => 'string'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function extractOutputText(array $payload): string
    {
        if (data_get($payload, 'status') === 'incomplete') {
            throw new RuntimeException('OpenAI API response was incomplete.');
        }

        $direct = data_get($payload, 'output_text');
        if (is_string($direct) && trim($direct) !== '') {
            return $direct;
        }

        foreach ((array) data_get($payload, 'output', []) as $output) {
            foreach ((array) data_get($output, 'content', []) as $content) {
                $refusal = data_get($content, 'refusal');
                if (is_string($refusal) && trim($refusal) !== '') {
                    throw new RuntimeException('OpenAI API refused the article generation request.');
                }

                $text = data_get($content, 'text');
                if (is_string($text) && trim($text) !== '') {
                    return $text;
                }
            }
        }

        throw new RuntimeException('OpenAI API response did not contain output text.');
    }

    private function normalizeArticle(array $article, array $input, int $index): array
    {
        $title = trim((string) ($article['title'] ?? ''));
        if ($title === '') {
            $title = $this->title($input['idea'], $this->angle($input['locale'], $index), $input['locale'], $index);
        }

        $lead = trim((string) ($article['lead'] ?? ''));
        if ($lead === '') {
            $lead = $this->lead($title, implode(', ', $input['keywords']), $input['locale']);
        }

        $tags = collect($article['tags'] ?? $input['keywords'])
            ->map(fn ($tag) => trim((string) $tag))
            ->filter()
            ->unique()
            ->take(8)
            ->values()
            ->all();

        $sections = collect($article['sections'] ?? [])
            ->map(function ($section) {
                $heading = trim((string) ($section['heading'] ?? ''));
                $body = trim((string) ($section['body'] ?? ''));
                $bodyHtml = trim((string) ($section['body_html'] ?? ''));

                return [
                    'heading' => $heading !== '' ? $heading : 'Section',
                    'body' => $body,
                    'body_html' => $bodyHtml !== '' ? $bodyHtml : $this->bodyToHtml($body),
                ];
            })
            ->filter(fn (array $section) => $section['body'] !== '' || $section['body_html'] !== '')
            ->values()
            ->all();

        if ($sections === []) {
            $sections = $this->sections($input['idea'], $input['keywords'], $input['guidelines'], $input['locale'], $index);
        }

        $faq = $this->normalizeFaq($article['faq'] ?? [], $input['idea'], $input['locale']);

        $imageUrl = trim((string) ($article['meta_image_path'] ?? ''));
        if (! Str::startsWith($imageUrl, ['http://', 'https://', '/'])) {
            $imageUrl = $this->imageUrl($index);
        }

        return [
            'title' => $title,
            'slug' => $input['target_slug'] !== '' ? $input['target_slug'] : Str::slug((string) ($article['slug'] ?? $title)),
            'section' => $input['target_section'],
            'locale' => $input['locale'],
            'seo_title' => Str::limit(trim((string) ($article['seo_title'] ?? $title)), 68, ''),
            'meta_description' => Str::limit(trim((string) ($article['meta_description'] ?? $lead)), 155, ''),
            'eyebrow' => trim((string) ($article['eyebrow'] ?? $this->defaultEyebrow($input['target_section'], $input['locale']))) ?: $this->defaultEyebrow($input['target_section'], $input['locale']),
            'lead' => $lead,
            'author' => trim((string) ($article['author'] ?? 'Dream Digital')) ?: 'Dream Digital',
            'reading_time' => trim((string) ($article['reading_time'] ?? ($input['locale'] === 'fr' ? '6 min' : '6 min read'))),
            'tags' => $tags ?: ($input['keywords'] ?: ['CPaaS', 'SMS A2P', 'Voice', 'B2B']),
            'meta_image_path' => $imageUrl,
            'image_alt' => trim((string) ($article['image_alt'] ?? $title)) ?: $title,
            'image_credit' => trim((string) ($article['image_credit'] ?? 'Photo Unsplash')) ?: 'Photo Unsplash',
            'image_source_url' => trim((string) ($article['image_source_url'] ?? 'https://unsplash.com/')) ?: 'https://unsplash.com/',
            'faq' => $faq,
            'sections' => $sections,
        ];
    }

    private function variant(string $idea, array $keywords, string $guidelines, string $locale, int $index, string $targetSection, string $targetSlug): array
    {
        $keywordLine = $keywords ? implode(', ', $keywords) : ($locale === 'fr' ? 'telecom B2B, CPaaS, Afrique' : 'B2B telecom, CPaaS, Africa');
        $angle = $this->angle($locale, $index);
        $title = $this->title($idea, $angle, $locale, $index);
        $lead = $this->lead($title, $keywordLine, $locale);
        $sections = $this->sections($idea, $keywords, $guidelines, $locale, $index);

        return [
            'title' => $title,
            'slug' => $targetSlug !== '' ? $targetSlug : Str::slug($title),
            'section' => $targetSection,
            'locale' => $locale,
            'seo_title' => Str::limit($title . ($locale === 'fr' ? ' | Guide Dream Digital' : ' | Dream Digital guide'), 68, ''),
            'meta_description' => Str::limit($lead, 155, ''),
            'eyebrow' => $this->defaultEyebrow($targetSection, $locale),
            'lead' => $lead,
            'author' => 'Dream Digital',
            'reading_time' => $locale === 'fr' ? '6 min' : '6 min read',
            'tags' => $keywords ?: ['CPaaS', 'SMS A2P', 'Voice', 'B2B'],
            'meta_image_path' => $this->imageUrl($index),
            'image_alt' => $locale === 'fr'
                ? "Equipe telecom analysant {$idea}"
                : "Telecom team reviewing {$idea}",
            'image_credit' => 'Photo Unsplash',
            'image_source_url' => 'https://unsplash.com/',
            'faq' => $this->faq($idea, $locale),
            'sections' => $sections,
        ];
    }

    private function title(string $idea, string $angle, string $locale, int $index): string
    {
        if ($locale === 'en') {
            return match ($index) {
                1 => "{$idea}: {$angle} for B2B telecom teams",
                2 => "How to use {$idea} to improve delivery and margins",
                default => "{$idea} playbook: practical steps for scalable CPaaS growth",
            };
        }

        return match ($index) {
            1 => "{$idea} : {$angle} pour les equipes telecom B2B",
            2 => "Comment utiliser {$idea} pour ameliorer delivrabilite et marges",
            default => "{$idea} : plan d'action pour une croissance CPaaS scalable",
        };
    }

    private function lead(string $title, string $keywordLine, string $locale): string
    {
        if ($locale === 'en') {
            return "{$title} explains how Dream Digital helps operators, aggregators and digital businesses turn {$keywordLine} into measurable performance: cleaner routing, better observability and stronger customer experience.";
        }

        return "{$title} explique comment Dream Digital aide les operateurs, agregateurs et entreprises digitales a transformer {$keywordLine} en performance mesurable : routage plus propre, meilleure observabilite et experience client plus fiable.";
    }

    private function defaultEyebrow(string $targetSection, string $locale): string
    {
        if ($targetSection === 'product') {
            return $locale === 'en' ? 'Product' : 'Produit';
        }

        return 'Blog';
    }

    private function sections(string $idea, array $keywords, string $guidelines, string $locale, int $index): array
    {
        $keywordsText = $keywords ? implode(', ', $keywords) : ($locale === 'fr' ? 'SMS A2P, voice, CPaaS' : 'A2P SMS, voice, CPaaS');
        $guidelineSentence = $guidelines !== ''
            ? ($locale === 'fr' ? "Contrainte editoriale a respecter : {$guidelines}." : "Editorial guideline to respect: {$guidelines}.")
            : ($locale === 'fr' ? 'Le contenu doit rester concret, commercialement utile et oriente decision.' : 'The content should stay concrete, commercially useful and decision-oriented.');
        $ideaHtml = e($idea);
        $keywordsHtml = e($keywordsText);
        $guidelineHtml = e($guidelineSentence);

        $content = $locale === 'en'
            ? [
                [
                    'heading' => 'Why this topic matters now',
                    'body' => "{$idea} is no longer a side topic for telecom teams. Growth now depends on delivery quality, transparent routing and the ability to connect product, sales and operations around the same indicators.\n\n{$guidelineSentence}",
                    'body_html' => "<p><strong>{$ideaHtml}</strong> is no longer a side topic for telecom teams. Growth now depends on delivery quality, transparent routing and the ability to connect product, sales and operations around the same indicators.</p><p>{$guidelineHtml}</p>",
                ],
                [
                    'heading' => 'What Dream Digital brings to the workflow',
                    'body' => "Dream Digital combines CPaaS APIs, negotiated routes and operational dashboards so teams can launch faster without losing control of quality. The priority is simple: fewer blind spots, fewer manual checks and clearer decisions.\n\nRelevant keywords: {$keywordsText}.",
                    'body_html' => "<p>Dream Digital combines CPaaS APIs, negotiated routes and operational dashboards so teams can launch faster without losing control of quality.</p><ul><li>Fewer blind spots</li><li>Fewer manual checks</li><li>Clearer routing decisions</li></ul><p>Relevant keywords: {$keywordsHtml}.</p>",
                ],
                [
                    'heading' => 'Implementation checklist',
                    'body' => "Start with a measurable use case, define the expected SLA, then validate routing, fallback logic and reporting before scaling. Teams that document these rules early save time when volumes increase.",
                    'body_html' => '<p>Start with a measurable use case, define the expected SLA, then validate routing, fallback logic and reporting before scaling.</p><p>Teams that document these rules early save time when volumes increase.</p>',
                ],
                [
                    'heading' => 'How to measure success',
                    'body' => "Track delivery rate, conversion, incident response time, gross margin and customer feedback together. A good CPaaS setup is not only technical; it creates a shared language between revenue and operations.",
                    'body_html' => '<p>Track delivery rate, conversion, incident response time, gross margin and customer feedback together.</p><p>A good CPaaS setup is not only technical; it creates a shared language between revenue and operations.</p>',
                ],
            ]
            : [
                [
                    'heading' => 'Pourquoi ce sujet compte maintenant',
                    'body' => "{$idea} n'est plus un sujet secondaire pour les equipes telecom. La croissance depend de la qualite de livraison, de la transparence du routage et de la capacite a relier produit, sales et operations autour des memes indicateurs.\n\n{$guidelineSentence}",
                    'body_html' => "<p><strong>{$ideaHtml}</strong> n'est plus un sujet secondaire pour les equipes telecom. La croissance depend de la qualite de livraison, de la transparence du routage et de la capacite a relier produit, sales et operations autour des memes indicateurs.</p><p>{$guidelineHtml}</p>",
                ],
                [
                    'heading' => 'Ce que Dream Digital apporte au workflow',
                    'body' => "Dream Digital combine APIs CPaaS, routes negociees et tableaux de bord operationnels pour lancer plus vite sans perdre le controle de la qualite. La priorite est claire : moins d'angles morts, moins de controles manuels et des decisions plus lisibles.\n\nMots cles utiles : {$keywordsText}.",
                    'body_html' => "<p>Dream Digital combine APIs CPaaS, routes negociees et tableaux de bord operationnels pour lancer plus vite sans perdre le controle de la qualite.</p><ul><li>Moins d'angles morts</li><li>Moins de controles manuels</li><li>Decisions de routage plus lisibles</li></ul><p>Mots cles utiles : {$keywordsHtml}.</p>",
                ],
                [
                    'heading' => 'Checklist de mise en place',
                    'body' => "Commencez par un cas d'usage mesurable, definissez le SLA attendu, puis validez le routage, les regles de fallback et le reporting avant de scaler. Les equipes qui documentent ces regles tot gagnent du temps quand les volumes montent.",
                    'body_html' => "<p>Commencez par un cas d'usage mesurable, definissez le SLA attendu, puis validez le routage, les regles de fallback et le reporting avant de scaler.</p><p>Les equipes qui documentent ces regles tot gagnent du temps quand les volumes montent.</p>",
                ],
                [
                    'heading' => 'Comment mesurer le succes',
                    'body' => "Suivez ensemble taux de livraison, conversion, delai de reaction incident, marge brute et retours clients. Une bonne stack CPaaS n'est pas seulement technique : elle cree un langage commun entre revenu et operations.",
                    'body_html' => "<p>Suivez ensemble taux de livraison, conversion, delai de reaction incident, marge brute et retours clients.</p><p>Une bonne stack CPaaS n'est pas seulement technique : elle cree un langage commun entre revenu et operations.</p>",
                ],
            ];

        if ($index % 2 === 0) {
            $content[] = $locale === 'fr'
                ? [
                    'heading' => 'Prochaine action recommandee',
                    'body' => "Identifiez un corridor, une campagne ou un flux OTP prioritaire, puis comparez les resultats avant/apres sur une periode courte. Cette approche donne rapidement une preuve de valeur exploitable par le business.",
                    'body_html' => "<p>Identifiez un corridor, une campagne ou un flux OTP prioritaire, puis comparez les resultats avant/apres sur une periode courte.</p><p>Cette approche donne rapidement une preuve de valeur exploitable par le business.</p>",
                ]
                : [
                    'heading' => 'Recommended next step',
                    'body' => 'Pick one corridor, campaign or OTP flow, then compare before/after results over a short period. This creates a proof point the business can use quickly.',
                    'body_html' => '<p>Pick one corridor, campaign or OTP flow, then compare before/after results over a short period.</p><p>This creates a proof point the business can use quickly.</p>',
                ];
        }

        return $content;
    }

    private function keywords(string $keywords): array
    {
        return collect(explode(',', $keywords))
            ->map(fn (string $keyword) => trim($keyword))
            ->filter()
            ->unique()
            ->take(8)
            ->values()
            ->all();
    }

    private function normalizeFaq(array $items, string $idea, string $locale): array
    {
        $faq = collect($items)
            ->map(fn ($item) => [
                'question' => trim((string) data_get($item, 'question')),
                'answer' => trim((string) data_get($item, 'answer')),
            ])
            ->filter(fn (array $item) => $item['question'] !== '' && $item['answer'] !== '')
            ->take(4)
            ->values()
            ->all();

        return count($faq) >= 2 ? $faq : $this->faq($idea, $locale);
    }

    private function faq(string $idea, string $locale): array
    {
        if ($locale === 'en') {
            return [
                [
                    'question' => "When should a company prioritize {$idea}?",
                    'answer' => "Prioritize {$idea} when the flow has a direct impact on conversion, support load, fraud risk or customer experience. Start with one measurable use case and compare results before scaling.",
                ],
                [
                    'question' => 'What data should be prepared before contacting Dream Digital?',
                    'answer' => 'Share the target countries, expected monthly volume, traffic type, SLA expectations and any current delivery or routing issues. This helps the team recommend the right corridor and operating model.',
                ],
            ];
        }

        return [
            [
                'question' => "Quand prioriser {$idea} ?",
                'answer' => "{$idea} devient prioritaire quand le flux influence directement la conversion, le support, le risque fraude ou l'experience client. Le plus efficace est de commencer par un cas d'usage mesurable, puis de comparer les resultats avant de scaler.",
            ],
            [
                'question' => 'Quelles donnees preparer avant de contacter Dream Digital ?',
                'answer' => 'Preparez les pays cibles, le volume mensuel attendu, le type de trafic, les attentes SLA et les problemes actuels de livraison ou de routage. Ces elements permettent de recommander le bon corridor et le bon modele operationnel.',
            ],
        ];
    }

    private function bodyToHtml(string $body): string
    {
        return collect(preg_split('/\R{2,}/', $body) ?: [])
            ->map(fn (string $paragraph) => trim($paragraph))
            ->filter()
            ->map(fn (string $paragraph) => '<p>' . e($paragraph) . '</p>')
            ->implode('');
    }

    private function angle(string $locale, int $index): string
    {
        $angles = $locale === 'en'
            ? ['a practical growth guide', 'a delivery quality framework', 'a margin-first operating model']
            : ['guide pratique de croissance', 'cadre de qualite de livraison', 'modele operationnel oriente marge'];

        return $angles[($index - 1) % count($angles)];
    }

    private function imageUrl(int $index): string
    {
        $images = [
            'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1600&q=80',
            'https://images.unsplash.com/photo-1551434678-e076c223a692?auto=format&fit=crop&w=1600&q=80',
            'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1600&q=80',
            'https://images.unsplash.com/photo-1559136555-9303baea8ebd?auto=format&fit=crop&w=1600&q=80',
            'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1600&q=80',
        ];

        return $images[($index - 1) % count($images)];
    }
}
