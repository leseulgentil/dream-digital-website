<?php

namespace App\Services\Ai;

use App\Models\AiKnowledgeChunk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AiKnowledgeRetriever
{
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
        $search = trim($message);

        if ($search === '') {
            return $query
                ->orderByDesc('priority')
                ->limit($limit)
                ->get();
        }

        return $query
            ->whereRaw("to_tsvector('simple', coalesce(title, '') || ' ' || coalesce(content, '')) @@ plainto_tsquery('simple', ?)", [$search])
            ->orderByRaw("ts_rank(to_tsvector('simple', coalesce(title, '') || ' ' || coalesce(content, '')), plainto_tsquery('simple', ?)) desc", [$search])
            ->orderByDesc('priority')
            ->limit($limit)
            ->get();
    }

    private function retrieveWithLikeFallback(Builder $query, string $message, int $limit): Collection
    {
        $terms = $this->terms($message);

        if ($terms === []) {
            return $query
                ->orderByDesc('priority')
                ->limit($limit)
                ->get();
        }

        $matched = (clone $query)
            ->where(function (Builder $query) use ($terms): void {
                foreach ($terms as $term) {
                    $query->orWhere('title', 'like', "%{$term}%")
                        ->orWhere('content', 'like', "%{$term}%");
                }
            })
            ->orderByDesc('priority')
            ->limit($limit)
            ->get();

        if ($matched->isNotEmpty()) {
            return $matched;
        }

        return $query
            ->orderByDesc('priority')
            ->limit($limit)
            ->get();
    }

    /**
     * @return array<int, string>
     */
    private function terms(string $message): array
    {
        return collect(preg_split('/[^\pL\pN]+/u', Str::lower($message)) ?: [])
            ->map(fn (string $term) => trim($term))
            ->filter(fn (string $term) => mb_strlen($term) >= 3)
            ->unique()
            ->take(8)
            ->values()
            ->all();
    }
}
