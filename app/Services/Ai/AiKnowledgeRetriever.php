<?php

namespace App\Services\Ai;

use App\Models\AiKnowledgeChunk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AiKnowledgeRetriever
{
    private const STOP_WORDS = [
        'and',
        'are',
        'can',
        'comment',
        'does',
        'est',
        'for',
        'how',
        'les',
        'nos',
        'not',
        'our',
        'pour',
        'que',
        'quel',
        'quelle',
        'quelles',
        'quels',
        'the',
        'une',
        'vos',
        'vous',
        'what',
        'which',
        'with',
        'your',
    ];

    public function retrieve(string $message, string $locale, string $countryCode, int $limit = 5): Collection
    {
        $limit = max(1, min(10, $limit));
        $countryCode = in_array($countryCode, ['cd', 'ci', 'cg', 'global'], true) ? $countryCode : 'global';
        $locale = in_array($locale, ['fr', 'en'], true) ? $locale : 'fr';

        $query = $this->baseQuery($locale, $countryCode);

        if (config('database.default') === 'pgsql') {
            return $this->retrieveWithPostgres($query, $message, $limit);
        }

        return $this->retrieveWithLikeFallback($query, $message, $limit);
    }

    private function baseQuery(string $locale, string $countryCode): Builder
    {
        return AiKnowledgeChunk::query()
            ->published()
            ->where('locale', $locale)
            ->whereIn('country_code', array_values(array_unique([$countryCode, 'global'])));
    }

    private function retrieveWithPostgres(Builder $query, string $message, int $limit): Collection
    {
        $search = $this->postgresWebsearchQuery($message);

        if ($search === '') {
            return collect();
        }

        return $query
            ->whereRaw("to_tsvector('simple', coalesce(title, '') || ' ' || coalesce(content, '')) @@ websearch_to_tsquery('simple', ?)", [$search])
            ->orderByRaw("ts_rank(to_tsvector('simple', coalesce(title, '') || ' ' || coalesce(content, '')), websearch_to_tsquery('simple', ?)) desc", [$search])
            ->orderByDesc('priority')
            ->limit($limit)
            ->get();
    }

    private function retrieveWithLikeFallback(Builder $query, string $message, int $limit): Collection
    {
        $terms = $this->significantTerms($message);

        if ($terms === []) {
            return collect();
        }

        $scoreSql = collect($terms)
            ->map(fn () => "(case when lower(coalesce(title, '')) like ? then 3 else 0 end) + (case when lower(coalesce(content, '')) like ? then 1 else 0 end)")
            ->implode(' + ');
        $scoreBindings = collect($terms)
            ->flatMap(fn (string $term): array => ["%{$term}%", "%{$term}%"])
            ->all();

        $matched = (clone $query)
            ->where(function (Builder $query) use ($terms): void {
                foreach ($terms as $term) {
                    $query->orWhere('title', 'like', "%{$term}%")
                        ->orWhere('content', 'like', "%{$term}%");
                }
            })
            ->orderByRaw("({$scoreSql}) desc", $scoreBindings)
            ->orderByDesc('priority')
            ->limit($limit)
            ->get();

        return $matched;
    }

    /**
     * @return array<int, string>
     */
    public function significantTerms(string $message): array
    {
        return collect(preg_split('/[^\pL\pN]+/u', Str::lower($message)) ?: [])
            ->map(fn (string $term) => trim($term))
            ->filter(fn (string $term) => mb_strlen($term) >= 3)
            ->reject(fn (string $term) => in_array($term, self::STOP_WORDS, true))
            ->unique()
            ->take(8)
            ->values()
            ->all();
    }

    public function postgresWebsearchQuery(string $message): string
    {
        return implode(' OR ', $this->significantTerms($message));
    }
}
