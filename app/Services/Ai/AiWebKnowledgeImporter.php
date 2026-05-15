<?php

namespace App\Services\Ai;

use App\Models\AiKnowledgeSource;
use App\Models\AiKnowledgeWebSource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use InvalidArgumentException;

class AiWebKnowledgeImporter
{
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
        $response = Http::timeout(20)->accept('*/*')->get($webSource->url)->throw();
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

        $response = Http::timeout(20)
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

        $payload = Http::timeout(20)
            ->acceptJson()
            ->get($webSource->url)
            ->throw()
            ->json();

        $items = is_array($payload) ? data_get($payload, 'items', []) : [];
        $imported = 0;

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $content = trim((string) ($item['content_markdown'] ?? $item['content'] ?? ''));
            $title = trim((string) ($item['title'] ?? ''));

            if ($content === '' || $title === '') {
                continue;
            }

            $url = trim((string) ($item['canonical_url'] ?? ''));

            if ($url === '') {
                $externalId = trim((string) ($item['external_id'] ?? hash('sha256', $title.$content)));
                $url = rtrim($webSource->url, '#').'#'.$externalId;
            }

            $this->guardPublicUrl($url);

            $imported += $this->storeKnowledgePage(
                $webSource,
                $url,
                Str::limit($title, 180, ''),
                $content,
                trim((string) ($item['content_hash'] ?? '')) ?: hash('sha256', $content),
                AiKnowledgeSource::TYPE_WEB_ENDPOINT,
                $this->normalizedLocale($item['locale'] ?? null, $webSource->locale),
                $this->normalizedCountryCode($item['country'] ?? null, $webSource->country_code),
                trim((string) ($item['category'] ?? '')) ?: $webSource->category,
            );
        }

        return $imported;
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
    ): int {
        $locale ??= $webSource->locale;
        $countryCode ??= $webSource->country_code;
        $category ??= $webSource->category;

        return DB::transaction(function () use ($webSource, $url, $title, $content, $hash, $type, $locale, $countryCode, $category): int {
            $source = AiKnowledgeSource::query()
                ->where('ai_knowledge_web_source_id', $webSource->id)
                ->where('source_url', $url)
                ->first();

            if ($source && $source->content_hash === $hash) {
                $source->update(['fetched_at' => now()]);

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
                    'metadata' => [
                        'category' => $category,
                    ],
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
                        'category' => $category,
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
                ]);
                $created++;
            }

            return $created;
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
}
