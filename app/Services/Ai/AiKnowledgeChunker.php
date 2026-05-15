<?php

namespace App\Services\Ai;

class AiKnowledgeChunker
{
    /**
     * @return array<int, string>
     */
    public function chunks(string $text, int $maxChars = 1200): array
    {
        $text = strip_tags($text);
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text) ?? '';
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? '';
        $text = trim($text);

        if ($text === '') {
            return [];
        }

        $paragraphs = preg_split("/\n\s*\n/", $text) ?: [];
        $chunks = [];
        $current = '';

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            if (mb_strlen($paragraph) > $maxChars) {
                $chunks = array_merge($chunks, $this->flushCurrent($current));
                $current = '';
                $chunks = array_merge($chunks, $this->splitLongParagraph($paragraph, $maxChars));

                continue;
            }

            $candidate = $current === '' ? $paragraph : "{$current}\n\n{$paragraph}";

            if (mb_strlen($candidate) <= $maxChars) {
                $current = $candidate;

                continue;
            }

            $chunks[] = $current;
            $current = $paragraph;
        }

        return array_merge($chunks, $this->flushCurrent($current));
    }

    /**
     * @return array<int, string>
     */
    private function splitLongParagraph(string $paragraph, int $maxChars): array
    {
        $chunks = [];
        $current = '';
        $words = preg_split('/\s+/', $paragraph) ?: [];

        foreach ($words as $word) {
            if ($word === '') {
                continue;
            }

            $candidate = $current === '' ? $word : "{$current} {$word}";

            if (mb_strlen($candidate) <= $maxChars) {
                $current = $candidate;

                continue;
            }

            if ($current !== '') {
                $chunks[] = $current;
            }

            $current = $word;
        }

        return array_merge($chunks, $this->flushCurrent($current));
    }

    /**
     * @return array<int, string>
     */
    private function flushCurrent(string $current): array
    {
        $current = trim($current);

        return $current === '' ? [] : [$current];
    }
}
