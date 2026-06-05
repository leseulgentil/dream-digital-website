<?php

namespace App\Services\Admin;

use App\Models\Page;
use App\Services\Cms\PageContentNormalizer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class PageTranslationService
{
    public function __construct(private readonly PageContentNormalizer $contentNormalizer) {}

    /**
     * @return array{title: string, meta_description: ?string, content_blocks: array<string, mixed>, provider: string, fallback_used: bool, fallback_reason: ?string}
     */
    public function translate(Page $page, string $targetLocale): array
    {
        $targetLocale = $targetLocale === 'en' ? 'en' : 'fr';

        if ($this->provider() === 'openai' && filled(config('services.openai.api_key'))) {
            try {
                return [
                    ...$this->normalizeTranslatedPage($this->translateWithOpenAi($page, $targetLocale), $page, $targetLocale),
                    'provider' => 'openai',
                    'fallback_used' => false,
                    'fallback_reason' => null,
                ];
            } catch (Throwable $exception) {
                if (! $this->fallbackOnFailure()) {
                    throw new RuntimeException('Traduction OpenAI indisponible: '.$exception->getMessage(), previous: $exception);
                }

                Log::warning('OpenAI page translation failed; using local fallback.', [
                    'page_id' => $page->id,
                    'target_locale' => $targetLocale,
                    'message' => $exception->getMessage(),
                ]);

                return $this->fallback($page, $targetLocale, 'openai_error');
            }
        }

        return $this->fallback($page, $targetLocale, $this->provider() === 'openai' ? 'missing_api_key' : null);
    }

    /**
     * @return array<string, mixed>
     */
    private function translateWithOpenAi(Page $page, string $targetLocale): array
    {
        $response = Http::acceptJson()
            ->withToken((string) config('services.openai.api_key'))
            ->timeout($this->translationTimeout())
            ->post(rtrim((string) config('services.openai.base_url', 'https://api.openai.com/v1'), '/').'/responses', $this->openAiPayload($page, $targetLocale));

        if ($response->failed()) {
            throw new RuntimeException('OpenAI API returned HTTP '.$response->status());
        }

        $decoded = json_decode($this->extractOutputText($response->json()), true, 512, JSON_THROW_ON_ERROR);
        $translated = $decoded['page'] ?? [];

        if (! is_array($translated) || $translated === []) {
            throw new RuntimeException('OpenAI API returned no translated page.');
        }

        return $translated;
    }

    /**
     * @return array<string, mixed>
     */
    private function openAiPayload(Page $page, string $targetLocale): array
    {
        $sourceLocale = $page->locale === 'en' ? 'English' : 'French';
        $targetLabel = $targetLocale === 'en' ? 'English' : 'French';
        $blocks = $page->content_blocks ?? [];

        return [
            'model' => (string) config('services.openai.model', 'gpt-5-mini'),
            'instructions' => 'You are Dream Digital senior bilingual CMS editor. Translate faithfully, preserve facts, preserve HTML structure, and return only valid JSON matching the schema.',
            'input' => [[
                'role' => 'user',
                'content' => [[
                    'type' => 'input_text',
                    'text' => implode("\n", [
                        "Translate this {$page->section} CMS page from {$sourceLocale} to {$targetLabel}.",
                        'Do not add facts, prices, countries, vendors, legal terms, dates or claims that are not in the source.',
                        'Keep the slug unchanged outside this JSON; this payload only translates editorial content.',
                        'Source JSON:',
                        json_encode([
                            'title' => $page->title,
                            'meta_description' => $page->meta_description,
                            'content_blocks' => [
                                'seo_title' => $blocks['seo_title'] ?? null,
                                'eyebrow' => $blocks['eyebrow'] ?? null,
                                'lead' => $blocks['lead'] ?? null,
                                'author' => $blocks['author'] ?? null,
                                'reading_time' => $blocks['reading_time'] ?? null,
                                'tags' => $blocks['tags'] ?? [],
                                'image_alt' => $blocks['image_alt'] ?? null,
                                'faq' => $blocks['faq'] ?? [],
                                'sections' => $blocks['sections'] ?? [],
                                'product_detail' => $blocks['product_detail'] ?? ['proofs' => [], 'workflow' => []],
                            ],
                        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ]),
                ]],
            ]],
            'text' => [
                'format' => [
                    'type' => 'json_schema',
                    'name' => 'dream_digital_translated_page',
                    'strict' => true,
                    'schema' => $this->openAiSchema(),
                ],
            ],
            'max_output_tokens' => (int) config('services.openai.max_output_tokens', 9000),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function openAiSchema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['page'],
            'properties' => [
                'page' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => [
                        'title',
                        'seo_title',
                        'meta_description',
                        'eyebrow',
                        'lead',
                        'author',
                        'reading_time',
                        'tags',
                        'image_alt',
                        'faq',
                        'sections',
                        'product_detail',
                    ],
                    'properties' => [
                        'title' => ['type' => 'string'],
                        'seo_title' => ['type' => 'string'],
                        'meta_description' => ['type' => 'string'],
                        'eyebrow' => ['type' => 'string'],
                        'lead' => ['type' => 'string'],
                        'author' => ['type' => 'string'],
                        'reading_time' => ['type' => 'string'],
                        'tags' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                        ],
                        'image_alt' => ['type' => 'string'],
                        'faq' => [
                            'type' => 'array',
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
                        'product_detail' => [
                            'type' => 'object',
                            'additionalProperties' => false,
                            'required' => ['proofs', 'workflow'],
                            'properties' => [
                                'proofs' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'additionalProperties' => false,
                                        'required' => ['icon', 'title', 'body'],
                                        'properties' => [
                                            'icon' => ['type' => 'string'],
                                            'title' => ['type' => 'string'],
                                            'body' => ['type' => 'string'],
                                        ],
                                    ],
                                ],
                                'workflow' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'additionalProperties' => false,
                                        'required' => ['label', 'body'],
                                        'properties' => [
                                            'label' => ['type' => 'string'],
                                            'body' => ['type' => 'string'],
                                        ],
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
                    throw new RuntimeException('OpenAI API refused the translation request.');
                }

                $text = data_get($content, 'text');
                if (is_string($text) && trim($text) !== '') {
                    return $text;
                }
            }
        }

        throw new RuntimeException('OpenAI API response did not contain output text.');
    }

    /**
     * @param  array<string, mixed>  $translated
     * @return array{title: string, meta_description: ?string, content_blocks: array<string, mixed>}
     */
    private function normalizeTranslatedPage(array $translated, Page $page, string $targetLocale): array
    {
        $sourceBlocks = $page->content_blocks ?? [];
        $title = trim((string) ($translated['title'] ?? ''));
        $lead = trim((string) ($translated['lead'] ?? ''));

        return [
            'title' => Str::limit($title !== '' ? $title : $this->localText($page->title, $targetLocale), 200, ''),
            'meta_description' => Str::limit(trim((string) ($translated['meta_description'] ?? $page->meta_description ?? $lead)), 500, ''),
            'content_blocks' => [
                ...$sourceBlocks,
                'seo_title' => Str::limit(trim((string) ($translated['seo_title'] ?? $title)), 220, ''),
                'eyebrow' => Str::limit(trim((string) ($translated['eyebrow'] ?? ($sourceBlocks['eyebrow'] ?? ''))), 200, ''),
                'lead' => $lead !== '' ? $lead : $this->localText((string) ($sourceBlocks['lead'] ?? ''), $targetLocale),
                'author' => trim((string) ($translated['author'] ?? ($sourceBlocks['author'] ?? 'Dream Digital'))) ?: 'Dream Digital',
                'reading_time' => trim((string) ($translated['reading_time'] ?? ($sourceBlocks['reading_time'] ?? ''))),
                'image_alt' => Str::limit(trim((string) ($translated['image_alt'] ?? ($sourceBlocks['image_alt'] ?? $title))), 220, ''),
                'tags' => $this->stringList($translated['tags'] ?? ($sourceBlocks['tags'] ?? [])),
                'faq' => $this->faq($translated['faq'] ?? ($sourceBlocks['faq'] ?? [])),
                'sections' => $this->contentNormalizer->normalizeSections($translated['sections'] ?? ($sourceBlocks['sections'] ?? [])),
                'product_detail' => $this->productDetail($translated['product_detail'] ?? ($sourceBlocks['product_detail'] ?? [])),
            ],
        ];
    }

    /**
     * @return array{title: string, meta_description: ?string, content_blocks: array<string, mixed>, provider: string, fallback_used: bool, fallback_reason: ?string}
     */
    private function fallback(Page $page, string $targetLocale, ?string $reason): array
    {
        $blocks = $page->content_blocks ?? [];
        $translated = [
            'title' => $this->localText($page->title, $targetLocale),
            'seo_title' => $this->localText((string) ($blocks['seo_title'] ?? $page->title), $targetLocale),
            'meta_description' => $this->localText((string) ($page->meta_description ?? $blocks['lead'] ?? ''), $targetLocale),
            'eyebrow' => $this->localText((string) ($blocks['eyebrow'] ?? ''), $targetLocale),
            'lead' => $this->localText((string) ($blocks['lead'] ?? ''), $targetLocale),
            'author' => $blocks['author'] ?? 'Dream Digital',
            'reading_time' => $targetLocale === 'en' ? 'Draft' : 'Brouillon',
            'tags' => $blocks['tags'] ?? [],
            'image_alt' => $this->localText((string) ($blocks['image_alt'] ?? $page->title), $targetLocale),
            'faq' => $blocks['faq'] ?? [],
            'sections' => collect($blocks['sections'] ?? [])
                ->filter(fn (mixed $section): bool => is_array($section))
                ->map(fn (array $section): array => [
                    'heading' => $this->localText((string) ($section['heading'] ?? ''), $targetLocale),
                    'body' => $this->localText((string) ($section['body'] ?? ''), $targetLocale),
                    'body_html' => $this->bodyHtml($this->localText((string) ($section['body'] ?? strip_tags((string) ($section['body_html'] ?? ''))), $targetLocale)),
                ])
                ->all(),
            'product_detail' => $this->fallbackProductDetail($blocks['product_detail'] ?? [], $targetLocale),
        ];

        return [
            ...$this->normalizeTranslatedPage($translated, $page, $targetLocale),
            'provider' => 'local',
            'fallback_used' => true,
            'fallback_reason' => $reason,
        ];
    }

    private function localText(string $text, string $targetLocale): string
    {
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        return $targetLocale === 'en'
            ? "[EN draft] {$text}"
            : "[FR brouillon] {$text}";
    }

    private function bodyHtml(string $body): string
    {
        return collect(preg_split('/\R{2,}/', $body) ?: [])
            ->map(fn (string $paragraph): string => trim($paragraph))
            ->filter()
            ->map(fn (string $paragraph): string => '<p>'.e($paragraph).'</p>')
            ->implode('');
    }

    /**
     * @return array<int, string>
     */
    private function stringList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(fn (mixed $item): string => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{question: string, answer: string}>
     */
    private function faq(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(fn (mixed $item): array => [
                'question' => trim((string) data_get($item, 'question')),
                'answer' => trim((string) data_get($item, 'answer')),
            ])
            ->filter(fn (array $item): bool => $item['question'] !== '' && $item['answer'] !== '')
            ->values()
            ->all();
    }

    /**
     * @return array{proofs: array<int, array{icon: string, title: string, body: string}>, workflow: array<int, array{label: string, body: string}>}
     */
    private function productDetail(mixed $value): array
    {
        if (! is_array($value)) {
            return ['proofs' => [], 'workflow' => []];
        }

        return [
            'proofs' => collect($value['proofs'] ?? [])
                ->filter(fn (mixed $item): bool => is_array($item))
                ->map(fn (array $item): array => [
                    'icon' => $this->cleanIcon(data_get($item, 'icon', 'bx-check')),
                    'title' => Str::limit(trim((string) data_get($item, 'title')), 160, ''),
                    'body' => trim((string) data_get($item, 'body')),
                ])
                ->filter(fn (array $item): bool => $item['title'] !== '' || $item['body'] !== '')
                ->values()
                ->all(),
            'workflow' => collect($value['workflow'] ?? [])
                ->filter(fn (mixed $item): bool => is_array($item))
                ->map(fn (array $item): array => [
                    'label' => Str::limit(trim((string) data_get($item, 'label')), 160, ''),
                    'body' => trim((string) data_get($item, 'body')),
                ])
                ->filter(fn (array $item): bool => $item['label'] !== '' || $item['body'] !== '')
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array{proofs: array<int, array{icon: string, title: string, body: string}>, workflow: array<int, array{label: string, body: string}>}
     */
    private function fallbackProductDetail(mixed $value, string $targetLocale): array
    {
        $detail = $this->productDetail($value);

        return [
            'proofs' => collect($detail['proofs'])
                ->map(fn (array $item): array => [
                    'icon' => $item['icon'],
                    'title' => $this->localText($item['title'], $targetLocale),
                    'body' => $this->localText($item['body'], $targetLocale),
                ])
                ->all(),
            'workflow' => collect($detail['workflow'])
                ->map(fn (array $item): array => [
                    'label' => $this->localText($item['label'], $targetLocale),
                    'body' => $this->localText($item['body'], $targetLocale),
                ])
                ->all(),
        ];
    }

    private function cleanIcon(mixed $icon): string
    {
        $icon = trim((string) $icon);

        return preg_match('/^bx-[a-z0-9-]+$/', $icon) ? $icon : 'bx-check';
    }

    private function provider(): string
    {
        $provider = strtolower((string) config('services.openai.article_provider', 'local'));

        return in_array($provider, ['local', 'openai'], true) ? $provider : 'local';
    }

    private function fallbackOnFailure(): bool
    {
        return (bool) config('services.openai.fallback_on_failure', true);
    }

    private function translationTimeout(): int
    {
        return max(5, min(25, (int) config('services.openai.translation_timeout', 20)));
    }
}
