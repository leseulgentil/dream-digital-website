<?php

namespace Database\Seeders;

use App\Models\AiKnowledgeWebSource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class AiWebSourceSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedEsimZone();
    }

    private function seedEsimZone(): void
    {
        $config = config('dream-digital.ai.web_sources.esimzone', []);
        $url = trim((string) ($config['url'] ?? ''));

        if (! filter_var($config['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN) || $url === '') {
            return;
        }

        $seeded = 0;
        $frequency = $this->allowed((string) ($config['frequency'] ?? AiKnowledgeWebSource::FREQUENCY_WEEKLY), [
            AiKnowledgeWebSource::FREQUENCY_MANUAL,
            AiKnowledgeWebSource::FREQUENCY_DAILY,
            AiKnowledgeWebSource::FREQUENCY_WEEKLY,
        ], AiKnowledgeWebSource::FREQUENCY_WEEKLY);
        $importStatus = $this->allowed((string) ($config['import_status'] ?? 'draft'), ['draft', 'published'], 'draft');
        $authToken = trim((string) ($config['auth_token'] ?? ''));
        $title = trim((string) ($config['title'] ?? 'eSIMZone API')) ?: 'eSIMZone API';
        $countryCode = $this->allowed((string) ($config['country_code'] ?? 'global'), ['global', 'cd', 'ci', 'cg'], 'global');

        $definitions = $this->esimZoneSourceDefinitions($config, $url, $title, $countryCode);
        $activeUrls = [];

        foreach ($definitions as $definition) {
            $activeUrls[] = $definition['url'];
            $source = AiKnowledgeWebSource::query()->firstOrNew(['url' => $definition['url']]);
            $metadata = $source->metadata ?? [];

            if ($authToken !== '') {
                $metadata['auth_token'] = Crypt::encryptString($authToken);
            } elseif (! array_key_exists('auth_token', $metadata)) {
                $metadata['auth_token'] = null;
            }

            $metadata = [
                ...$metadata,
                'audience_country' => $definition['country_code'],
                'destination_country' => $definition['destination_country'],
                'endpoint_category' => $definition['endpoint_category'],
            ];

            $source->fill([
                'title' => $definition['title'],
                'type' => AiKnowledgeWebSource::TYPE_ENDPOINT_JSON,
                'url' => $definition['url'],
                'locale' => $definition['locale'],
                'country_code' => $definition['country_code'],
                'category' => $definition['category'],
                'frequency' => $frequency,
                'import_status' => $importStatus,
                'status' => AiKnowledgeWebSource::STATUS_ACTIVE,
                'next_sync_at' => $frequency === AiKnowledgeWebSource::FREQUENCY_MANUAL
                    ? null
                    : ($source->next_sync_at ?? now()),
                'metadata' => array_filter($metadata, fn (mixed $value): bool => $value !== null && $value !== ''),
            ]);
            $source->save();
            $seeded++;
        }

        $paused = $this->pauseObsoleteEsimZoneSources($title, $url, $activeUrls);
        $this->command?->info("AI web source ready: {$title} ({$seeded})");

        if ($paused > 0) {
            $this->command?->info("Paused obsolete eSIMZone web source(s): {$paused}");
        }
    }

    /**
     * @return array<int, array{
     *     title: string,
     *     url: string,
     *     locale: string,
     *     country_code: string,
     *     category: string,
     *     endpoint_category: ?string,
     *     destination_country: ?string
     * }>
     */
    private function esimZoneSourceDefinitions(array $config, string $url, string $title, string $countryCode): array
    {
        $locales = $this->csvList($config['locales'] ?? null, 'lower');
        $categories = $this->csvList($config['categories'] ?? null, 'lower');
        $destinationCountries = $this->csvList($config['destination_countries'] ?? null, 'upper');

        if ($locales === [] && $categories === [] && $destinationCountries === []) {
            return [[
                'title' => $title,
                'url' => $url,
                'locale' => $this->allowed((string) ($config['locale'] ?? 'fr'), ['fr', 'en'], 'fr'),
                'country_code' => $countryCode,
                'category' => trim((string) ($config['category'] ?? 'esim')) ?: 'esim',
                'endpoint_category' => null,
                'destination_country' => null,
            ]];
        }

        $locales = $locales === []
            ? [$this->allowed((string) ($config['locale'] ?? 'fr'), ['fr', 'en'], 'fr')]
            : array_values(array_intersect($locales, ['fr', 'en']));
        $locales = $locales === [] ? ['fr'] : $locales;

        if ($categories === [] && $destinationCountries !== []) {
            $categories = ['offer', 'destination'];
        }

        $categories = $categories === [] ? [null] : $categories;
        $destinationCountries = $destinationCountries === [] ? [null] : $destinationCountries;
        $perPage = max(1, min(200, (int) ($config['per_page'] ?? 50)));
        $definitions = [];

        foreach ($locales as $locale) {
            foreach ($categories as $category) {
                foreach ($destinationCountries as $destinationCountry) {
                    $label = collect([
                        Str::upper($locale),
                        $category,
                        $destinationCountry,
                    ])->filter()->implode(' ');

                    $definitions[] = [
                        'title' => trim($title.' '.$label),
                        'url' => $this->endpointUrl($url, $locale, $category, $destinationCountry, $perPage),
                        'locale' => $locale,
                        'country_code' => $countryCode,
                        'category' => $category ?: (trim((string) ($config['category'] ?? 'esim')) ?: 'esim'),
                        'endpoint_category' => $category,
                        'destination_country' => $destinationCountry,
                    ];
                }
            }
        }

        return $definitions;
    }

    private function endpointUrl(string $url, string $locale, ?string $category, ?string $destinationCountry, int $perPage): string
    {
        $parts = parse_url($url);
        $query = [];

        if (is_string($parts['query'] ?? null)) {
            parse_str($parts['query'], $query);
        }

        $query['locale'] = $locale;
        $query['page'] = 1;
        $query['per_page'] = $perPage;

        if ($category !== null && $category !== '') {
            $query['category'] = $category;
        } else {
            unset($query['category']);
        }

        if ($destinationCountry !== null && $destinationCountry !== '') {
            $query['country'] = $destinationCountry;
        } else {
            unset($query['country']);
        }

        $rebuilt = ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '');

        if (isset($parts['port'])) {
            $rebuilt .= ':'.$parts['port'];
        }

        $rebuilt .= $parts['path'] ?? '';
        $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        return $queryString === '' ? $rebuilt : $rebuilt.'?'.$queryString;
    }

    /**
     * @param  array<int, string>  $activeUrls
     */
    private function pauseObsoleteEsimZoneSources(string $title, string $baseUrl, array $activeUrls): int
    {
        $baseHost = parse_url($baseUrl, PHP_URL_HOST);
        $basePath = parse_url($baseUrl, PHP_URL_PATH);
        $paused = 0;

        AiKnowledgeWebSource::query()
            ->where('type', AiKnowledgeWebSource::TYPE_ENDPOINT_JSON)
            ->where('status', AiKnowledgeWebSource::STATUS_ACTIVE)
            ->where(function ($query) use ($title): void {
                $query->where('title', $title)
                    ->orWhere('title', 'like', $title.' %');
            })
            ->get()
            ->each(function (AiKnowledgeWebSource $source) use ($activeUrls, $baseHost, $basePath, &$paused): void {
                if (in_array($source->url, $activeUrls, true) || ! $this->sameEndpoint($source->url, $baseHost, $basePath)) {
                    return;
                }

                $source->forceFill([
                    'status' => AiKnowledgeWebSource::STATUS_PAUSED,
                    'next_sync_at' => null,
                    'last_error' => null,
                ])->save();

                $paused++;
            });

        return $paused;
    }

    private function sameEndpoint(string $url, mixed $baseHost, mixed $basePath): bool
    {
        return is_string($baseHost)
            && is_string($basePath)
            && parse_url($url, PHP_URL_HOST) === $baseHost
            && parse_url($url, PHP_URL_PATH) === $basePath;
    }

    /**
     * @return array<int, string>
     */
    private function csvList(mixed $value, string $case = 'none'): array
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }

        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(fn (mixed $item): string => trim((string) $item))
            ->filter()
            ->map(function (string $item) use ($case): string {
                return match ($case) {
                    'lower' => Str::lower($item),
                    'upper' => Str::upper($item),
                    default => $item,
                };
            })
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $allowed
     */
    private function allowed(string $value, array $allowed, string $fallback): string
    {
        return in_array($value, $allowed, true) ? $value : $fallback;
    }
}
