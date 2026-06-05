<?php

namespace App\Services\Ai;

use App\Models\AiKnowledgeSource;
use App\Models\AiKnowledgeWebSource;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use InvalidArgumentException;

class AiWebKnowledgeImporter
{
    private const MAX_ENDPOINT_JSON_PAGES = 100;

    public function __construct(
        private readonly AiKnowledgeChunker $chunker,
    ) {}

    public function sync(AiKnowledgeWebSource $webSource): int
    {
        $imported = match ($webSource->type) {
            AiKnowledgeWebSource::TYPE_URL => $this->importUrl($webSource, $webSource->url),
            AiKnowledgeWebSource::TYPE_SITEMAP => $this->importSitemap($webSource),
            AiKnowledgeWebSource::TYPE_ENDPOINT_JSON => $this->importEndpointJson($webSource),
            default => throw new InvalidArgumentException("Unsupported web source type [{$webSource->type}]."),
        };

        $webSource->forceFill([
            'last_synced_at' => now(),
            'next_sync_at' => $webSource->nextSyncDate(),
            'last_error' => null,
        ])->save();

        return $imported;
    }

    private function importSitemap(AiKnowledgeWebSource $webSource): int
    {
        $response = $this->http($webSource)->accept('*/*')->get($webSource->url)->throw();
        $urls = $this->sitemapUrls($response->body(), $webSource->url);
        $imported = 0;

        foreach (array_slice($urls, 0, 100) as $url) {
            $imported += $this->importUrl($webSource, $url);
        }

        return $imported;
    }

    private function importUrl(AiKnowledgeWebSource $webSource, string $url): int
    {
        $this->guardPublicUrl($url);

        $response = $this->http($webSource)
            ->accept('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8')
            ->get($url)
            ->throw();

        $html = $response->body();
        $title = $this->pageTitle($html, $url);
        $content = $this->pageText($html, $title);

        if ($content === '') {
            return 0;
        }

        $hash = hash('sha256', $content);
        $type = $webSource->type === AiKnowledgeWebSource::TYPE_SITEMAP
            ? AiKnowledgeSource::TYPE_WEB_SITEMAP
            : AiKnowledgeSource::TYPE_WEB_URL;

        return $this->storeKnowledgePage($webSource, $url, $title, $content, $hash, $type);
    }

    private function importEndpointJson(AiKnowledgeWebSource $webSource): int
    {
        $this->guardPublicUrl($webSource->url);
        $url = $webSource->url;
        $visited = [];
        $imported = 0;

        for ($page = 0; $page < self::MAX_ENDPOINT_JSON_PAGES; $page++) {
            if (isset($visited[$url])) {
                break;
            }

            $visited[$url] = true;
            $payload = $this->endpointPayload($webSource, $url);
            $items = is_array($payload) ? data_get($payload, 'items', []) : [];

            foreach ($items as $item) {
                if (is_array($item)) {
                    $imported += $this->importEndpointJsonItem($webSource, $item);
                }
            }

            $nextUrl = $this->nextEndpointUrl($payload, $url);

            if ($nextUrl === null) {
                break;
            }

            $this->guardSameHost($webSource->url, $nextUrl);
            $url = $nextUrl;
        }

        return $imported;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function importEndpointJsonItem(AiKnowledgeWebSource $webSource, array $item): int
    {
        $content = trim((string) ($item['content_markdown'] ?? $item['content'] ?? ''));
        $title = trim((string) ($item['title'] ?? ''));
        $url = $this->endpointItemUrl($webSource, $item, $title, $content);

        if ($this->endpointItemIsDeleted($item)) {
            $this->deleteKnowledgePage($webSource, $url);

            return 0;
        }

        if ($content === '' || $title === '') {
            return 0;
        }

        $this->guardPublicUrl($url);

        $webSourceMetadata = $webSource->metadata ?? [];
        $category = trim((string) ($item['category'] ?? data_get($webSourceMetadata, 'endpoint_category') ?? '')) ?: $webSource->category;
        $audienceCountry = $item['audience_country'] ?? data_get($webSourceMetadata, 'audience_country');

        return $this->storeKnowledgePage(
            $webSource,
            $url,
            Str::limit($title, 180, ''),
            $content,
            $this->normalizedContentHash($item['content_hash'] ?? null, $content),
            AiKnowledgeSource::TYPE_WEB_ENDPOINT,
            $this->normalizedLocale($item['locale'] ?? null, $webSource->locale),
            $this->normalizedCountryCode($audienceCountry, $webSource->country_code),
            $category,
            $this->endpointItemMetadata($webSource, $item, $category),
            $this->nullableTimestamp($item['expires_at'] ?? null),
        );
    }

    private function storeKnowledgePage(
        AiKnowledgeWebSource $webSource,
        string $url,
        string $title,
        string $content,
        string $hash,
        string $type,
        ?string $locale = null,
        ?string $countryCode = null,
        ?string $category = null,
        array $metadata = [],
        ?Carbon $expiresAt = null,
    ): int {
        $locale ??= $webSource->locale;
        $countryCode ??= $webSource->country_code;
        $category ??= $webSource->category;
        $metadata = array_filter([
            ...$metadata,
            'category' => $category,
        ], fn ($value): bool => $value !== null && $value !== '');

        return DB::transaction(function () use ($webSource, $url, $title, $content, $hash, $type, $locale, $countryCode, $category, $metadata, $expiresAt): int {
            $source = AiKnowledgeSource::query()
                ->where('ai_knowledge_web_source_id', $webSource->id)
                ->where('source_url', $url)
                ->first();

            if ($source && $source->content_hash === $hash) {
                $source->update([
                    'fetched_at' => now(),
                    'metadata' => [
                        ...($source->metadata ?? []),
                        ...$metadata,
                    ],
                ]);
                $source->chunks()->update([
                    'expires_at' => $expiresAt,
                ]);

                return 0;
            }

            if (! $source) {
                $source = AiKnowledgeSource::create([
                    'ai_knowledge_web_source_id' => $webSource->id,
                    'type' => $type,
                    'title' => $title,
                    'source_url' => $url,
                    'content_hash' => $hash,
                    'fetched_at' => now(),
                    'locale' => $locale,
                    'country_code' => $countryCode,
                    'status' => $webSource->import_status,
                    'metadata' => $metadata,
                    'created_by_id' => $webSource->created_by_id,
                ]);
            } else {
                $source->chunks()->delete();
                $source->update([
                    'type' => $type,
                    'title' => $title,
                    'content_hash' => $hash,
                    'fetched_at' => now(),
                    'locale' => $locale,
                    'country_code' => $countryCode,
                    'status' => $webSource->import_status,
                    'metadata' => [
                        ...($source->metadata ?? []),
                        ...$metadata,
                    ],
                ]);
            }

            $created = 0;

            foreach ($this->chunker->chunks($content) as $index => $chunk) {
                $source->chunks()->create([
                    'title' => $index === 0 ? $title : $title.' #'.($index + 1),
                    'content' => $chunk,
                    'locale' => $locale,
                    'country_code' => $countryCode,
                    'category' => $category,
                    'status' => $webSource->import_status,
                    'priority' => 0,
                    'expires_at' => $expiresAt,
                ]);
                $created++;
            }

            return $created;
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function endpointPayload(AiKnowledgeWebSource $webSource, string $url): array
    {
        $payload = $this->http($webSource)
            ->acceptJson()
            ->get($url)
            ->throw()
            ->json();

        return is_array($payload) ? $payload : [];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function nextEndpointUrl(array $payload, string $currentUrl): ?string
    {
        $next = data_get($payload, 'links.next');

        if (is_string($next) && trim($next) !== '') {
            return trim($next);
        }

        $currentPage = (int) data_get($payload, 'meta.current_page', 0);
        $lastPage = (int) data_get($payload, 'meta.last_page', 0);

        if ($currentPage <= 0 || $lastPage <= 0 || $currentPage >= $lastPage) {
            return null;
        }

        return $this->urlWithPage($currentUrl, $currentPage + 1);
    }

    private function urlWithPage(string $url, int $page): string
    {
        $parts = parse_url($url);
        $query = [];

        if (is_string($parts['query'] ?? null)) {
            parse_str($parts['query'], $query);
        }

        $query['page'] = $page;
        $rebuilt = ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '');

        if (isset($parts['port'])) {
            $rebuilt .= ':'.$parts['port'];
        }

        $rebuilt .= $parts['path'] ?? '';

        return $rebuilt.'?'.http_build_query($query);
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function endpointItemUrl(AiKnowledgeWebSource $webSource, array $item, string $title, string $content): string
    {
        $url = trim((string) ($item['canonical_url'] ?? ''));

        if ($url !== '') {
            return $url;
        }

        $externalId = trim((string) ($item['external_id'] ?? hash('sha256', $title.$content)));

        return rtrim($webSource->url, '#').'#'.rawurlencode($externalId);
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function endpointItemIsDeleted(array $item): bool
    {
        $status = strtolower(trim((string) ($item['status'] ?? 'active')));

        return filled($item['deleted_at'] ?? null)
            || in_array($status, ['deleted', 'inactive', 'archived', 'disabled'], true);
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function endpointItemMetadata(AiKnowledgeWebSource $webSource, array $item, ?string $category): array
    {
        $webSourceMetadata = $webSource->metadata ?? [];

        return [
            'category' => $category,
            'external_id' => $this->nullableString($item['external_id'] ?? null),
            'destination_country' => $this->nullableString($item['destination_country'] ?? $item['country'] ?? data_get($webSourceMetadata, 'destination_country')),
            'audience_country' => $this->nullableString($item['audience_country'] ?? data_get($webSourceMetadata, 'audience_country')),
            'status' => $this->nullableString($item['status'] ?? null),
            'updated_at' => $this->nullableString($item['updated_at'] ?? null),
            'deleted_at' => $this->nullableString($item['deleted_at'] ?? null),
            'expires_at' => $this->nullableString($item['expires_at'] ?? null),
        ];
    }

    private function deleteKnowledgePage(AiKnowledgeWebSource $webSource, string $url): void
    {
        $this->guardPublicUrl($url);

        $source = AiKnowledgeSource::query()
            ->where('ai_knowledge_web_source_id', $webSource->id)
            ->where('source_url', $url)
            ->first();

        if (! $source) {
            return;
        }

        DB::transaction(function () use ($source): void {
            $source->chunks()->delete();
            $source->delete();
        });
    }

    /**
     * @return array<int, string>
     */
    private function sitemapUrls(string $xml, string $sitemapUrl): array
    {
        $urls = [];
        $sourceHost = parse_url($sitemapUrl, PHP_URL_HOST);
        $document = simplexml_load_string($xml);

        if ($document === false) {
            return [];
        }

        foreach ($document->url as $item) {
            $url = trim((string) $item->loc);

            if ($url === '' || parse_url($url, PHP_URL_HOST) !== $sourceHost) {
                continue;
            }

            $this->guardPublicUrl($url);
            $urls[] = $url;
        }

        return array_values(array_unique($urls));
    }

    private function pageTitle(string $html, string $url): string
    {
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches) === 1) {
            $title = $this->cleanText($matches[1]);

            if ($title !== '') {
                return Str::limit($title, 180, '');
            }
        }

        if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $html, $matches) === 1) {
            $title = $this->cleanText($matches[1]);

            if ($title !== '') {
                return Str::limit($title, 180, '');
            }
        }

        return Str::limit($url, 180, '');
    }

    private function pageText(string $html, string $title): string
    {
        $body = $html;

        if (preg_match('/<main[^>]*>(.*?)<\/main>/is', $html, $matches) === 1) {
            $body = $matches[1];
        }

        $body = preg_replace('/<(script|style|noscript|svg|nav|header|footer|form)[^>]*>.*?<\/\1>/is', ' ', $body) ?? $body;
        $content = $this->cleanText($body);
        $title = $this->cleanText($title);

        return trim($title.' '.$content);
    }

    private function cleanText(string $text): string
    {
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';

        return trim($text);
    }

    private function normalizedLocale(mixed $value, string $fallback): string
    {
        $locale = strtolower(trim((string) $value));

        return in_array($locale, ['fr', 'en'], true) ? $locale : $fallback;
    }

    private function normalizedCountryCode(mixed $value, string $fallback): string
    {
        $countryCode = strtolower(trim((string) $value));

        return in_array($countryCode, ['global', 'cd', 'ci', 'cg'], true) ? $countryCode : $fallback;
    }

    private function normalizedContentHash(mixed $value, string $content): string
    {
        $hash = strtolower(trim((string) $value));

        if (str_starts_with($hash, 'sha256:')) {
            $hash = substr($hash, 7);
        }

        if (preg_match('/^[a-f0-9]{64}$/', $hash) === 1) {
            return $hash;
        }

        if ($hash !== '' && strlen($hash) <= 64) {
            return $hash;
        }

        return hash('sha256', $content);
    }

    private function nullableTimestamp(mixed $value): ?Carbon
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function guardSameHost(string $sourceUrl, string $nextUrl): void
    {
        $this->guardPublicUrl($nextUrl);

        $sourceHost = strtolower((string) parse_url($sourceUrl, PHP_URL_HOST));
        $nextHost = strtolower((string) parse_url($nextUrl, PHP_URL_HOST));

        if ($sourceHost === '' || $nextHost === '' || $sourceHost !== $nextHost) {
            throw new InvalidArgumentException('Endpoint pagination must stay on the same public host.');
        }
    }

    private function guardPublicUrl(string $url): void
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $host = parse_url($url, PHP_URL_HOST);

        if (! in_array($scheme, ['http', 'https'], true) || ! is_string($host) || $host === '') {
            throw new InvalidArgumentException('Invalid public URL.');
        }

        $host = strtolower(trim($host, '[]'));

        if ($host === 'localhost' || str_ends_with($host, '.localhost')) {
            throw new InvalidArgumentException('Private URLs are not allowed.');
        }

        if (filter_var($host, FILTER_VALIDATE_IP) !== false
            && filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            throw new InvalidArgumentException('Private URLs are not allowed.');
        }
    }

    private function http(AiKnowledgeWebSource $webSource): PendingRequest
    {
        $request = Http::timeout(20);
        $encryptedToken = $webSource->metadata['auth_token'] ?? null;

        if (is_string($encryptedToken) && $encryptedToken !== '') {
            $request = $request->withToken(Crypt::decryptString($encryptedToken));
        }

        return $request;
    }
}
