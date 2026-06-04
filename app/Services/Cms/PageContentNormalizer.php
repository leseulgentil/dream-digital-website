<?php

namespace App\Services\Cms;

class PageContentNormalizer
{
    public function __construct(private readonly RichTextSanitizer $sanitizer)
    {
    }

    public function normalizeSections(?array $sections): array
    {
        return collect($sections ?? [])
            ->filter(fn ($section) => is_array($section))
            ->map(function (array $section): array {
                $body = trim((string) ($section['body'] ?? ''));
                $bodyHtml = $this->sanitizer->clean($section['body_html'] ?? '');

                if ($bodyHtml === '' && $body !== '') {
                    $bodyHtml = $this->bodyToHtml($body);
                }

                if ($body === '' && $bodyHtml !== '') {
                    $body = $this->htmlToText($bodyHtml);
                }

                return [
                    'heading' => trim(strip_tags((string) ($section['heading'] ?? ''))),
                    'body' => $body,
                    'body_html' => $bodyHtml,
                ];
            })
            ->filter(fn (array $section) => $section['heading'] !== '' || $section['body'] !== '' || $section['body_html'] !== '')
            ->values()
            ->all();
    }

    private function bodyToHtml(string $body): string
    {
        return collect(preg_split("/\r?\n\r?\n/", $body))
            ->map(fn ($paragraph) => trim((string) $paragraph))
            ->filter()
            ->map(fn (string $paragraph) => '<p>' . nl2br(e($paragraph), false) . '</p>')
            ->implode('');
    }

    private function htmlToText(string $html): string
    {
        $text = preg_replace('/<(br|\/p|\/li|\/h[1-6])\b[^>]*>/i', "\n", $html);
        $text = strip_tags((string) $text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace("/[ \t]+\n/", "\n", $text);
        $text = preg_replace("/\n{3,}/", "\n\n", (string) $text);

        return trim((string) $text);
    }
}
