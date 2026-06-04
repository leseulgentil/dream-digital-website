<?php

namespace App\Services\Ai;

use Illuminate\Support\Str;

class AiTextEmbedding
{
    public const MODEL = 'local-hash-v1';

    public const LOCAL_MODEL = self::MODEL;

    public const DIMENSIONS = 64;

    /**
     * @return array<int, float>
     */
    public function embed(string $text): array
    {
        $vector = array_fill(0, self::DIMENSIONS, 0.0);
        $terms = $this->terms($text);

        foreach ($terms as $term) {
            $index = (int) (((int) sprintf('%u', crc32($term))) % self::DIMENSIONS);
            $vector[$index] += 1.0;
        }

        return $this->normalize($vector);
    }

    public static function hash(string $text): string
    {
        return hash('sha256', implode(' ', self::termsFor($text)));
    }

    /**
     * @param array<int, float|int|string|null> $left
     * @param array<int, float|int|string|null> $right
     */
    public function cosine(array $left, array $right): float
    {
        $dot = 0.0;
        $leftMagnitude = 0.0;
        $rightMagnitude = 0.0;

        for ($i = 0; $i < self::DIMENSIONS; $i++) {
            $a = (float) ($left[$i] ?? 0.0);
            $b = (float) ($right[$i] ?? 0.0);
            $dot += $a * $b;
            $leftMagnitude += $a * $a;
            $rightMagnitude += $b * $b;
        }

        if ($leftMagnitude <= 0.0 || $rightMagnitude <= 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($leftMagnitude) * sqrt($rightMagnitude));
    }

    /**
     * @return array<int, string>
     */
    private function terms(string $text): array
    {
        return self::termsFor($text);
    }

    /**
     * @return array<int, string>
     */
    private static function termsFor(string $text): array
    {
        return collect(preg_split('/[^\pL\pN]+/u', Str::lower($text)) ?: [])
            ->map(fn (string $term): string => trim($term))
            ->filter(fn (string $term): bool => mb_strlen($term) >= 3)
            ->values()
            ->all();
    }

    /**
     * @param array<int, float> $vector
     * @return array<int, float>
     */
    private function normalize(array $vector): array
    {
        $magnitude = sqrt(array_reduce(
            $vector,
            fn (float $carry, float $value): float => $carry + ($value * $value),
            0.0,
        ));

        if ($magnitude <= 0.0) {
            return $vector;
        }

        return array_map(fn (float $value): float => round($value / $magnitude, 8), $vector);
    }
}
