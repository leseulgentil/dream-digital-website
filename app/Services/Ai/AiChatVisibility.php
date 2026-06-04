<?php

namespace App\Services\Ai;

use App\Models\AiChatSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AiChatVisibility
{
    public function shouldRender(?AiChatSetting $settings, Request $request): bool
    {
        if (! $settings?->enabled) {
            return false;
        }

        return $this->allows(
            $settings,
            '/' . trim($request->path(), '/'),
            session('dd_country_code', 'global'),
            app()->getLocale(),
        );
    }

    public function allowsPayload(AiChatSetting $settings, ?string $pageUrl, ?string $countryCode, ?string $locale): bool
    {
        if (! $settings->enabled) {
            return false;
        }

        return $this->allows(
            $settings,
            $this->pathFromUrl($pageUrl),
            $countryCode ?: 'global',
            $locale ?: app()->getLocale(),
        );
    }

    private function allows(AiChatSetting $settings, string $path, ?string $countryCode, ?string $locale): bool
    {
        $rules = is_array($settings->display_rules) ? $settings->display_rules : [];
        $path = $this->normalizePath($path);

        if (! $this->matchesPages($path, $this->strings(data_get($rules, 'pages', ['*'])))) {
            return false;
        }

        if (! $this->matchesOptionalList($countryCode ?: 'global', $this->strings(data_get($rules, 'countries')))) {
            return false;
        }

        return $this->matchesOptionalList($locale ?: 'fr', $this->strings(data_get($rules, 'locales')));
    }

    /**
     * @param array<int, string> $patterns
     */
    private function matchesPages(string $path, array $patterns): bool
    {
        $patterns = $patterns === [] ? ['*'] : $patterns;

        foreach ($patterns as $pattern) {
            if (str_starts_with($pattern, '!') && $this->matchesPathPattern($path, substr($pattern, 1))) {
                return false;
            }
        }

        $positive = array_values(array_filter($patterns, fn (string $pattern): bool => ! str_starts_with($pattern, '!')));

        if ($positive === []) {
            return true;
        }

        foreach ($positive as $pattern) {
            if ($this->matchesPathPattern($path, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int, string> $allowed
     */
    private function matchesOptionalList(string $value, array $allowed): bool
    {
        if ($allowed === [] || in_array('*', $allowed, true)) {
            return true;
        }

        return in_array(Str::lower($value), $allowed, true);
    }

    private function matchesPathPattern(string $path, string $pattern): bool
    {
        $patternPath = $this->normalizePath($this->pathFromUrl($pattern));

        return $patternPath === '*'
            || Str::is($patternPath, $path)
            || Str::is(ltrim($patternPath, '/'), ltrim($path, '/'));
    }

    private function normalizePath(string $path): string
    {
        $path = Str::lower(trim($path));

        if ($path === '' || $path === '/') {
            return '/';
        }

        if ($path === '*') {
            return '*';
        }

        return '/' . ltrim($path, '/');
    }

    private function pathFromUrl(?string $url): string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return '/';
        }

        $path = parse_url($url, PHP_URL_PATH);

        if (! is_string($path) || $path === '') {
            return $url;
        }

        return $path;
    }

    /**
     * @return array<int, string>
     */
    private function strings(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(fn (mixed $item): string => Str::lower(trim((string) $item)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
