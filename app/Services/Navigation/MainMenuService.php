<?php

namespace App\Services\Navigation;

use App\Models\NavigationItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MainMenuService
{
    public function items(string $locale = 'fr'): Collection
    {
        $locale = $this->normalizeLocale($locale);

        try {
            if ($this->databaseMenuIsAvailable()) {
                return NavigationItem::main()
                    ->active()
                    ->whereNull('parent_id')
                    ->with(['children' => fn ($query) => $query->active()])
                    ->orderBy('sort_order')
                    ->orderBy('label_fr')
                    ->get()
                    ->map(fn (NavigationItem $item) => $this->normalizeItem($item, $locale));
            }
        } catch (Throwable) {
            // Fallback keeps the public site available if migrations are pending.
        }

        return collect($this->fallbackItems($locale));
    }

    public function suggestions(string $locale = 'fr'): array
    {
        $locale = $this->normalizeLocale($locale);
        $pages = [
            ['label' => 'Accueil', 'url' => '/{locale}', 'type' => NavigationItem::TYPE_LINK],
            ['label' => 'Produits', 'url' => '/{locale}/products', 'type' => NavigationItem::TYPE_MEGA_SERVICES],
            ['label' => 'Developers', 'url' => '/{locale}/developers', 'type' => NavigationItem::TYPE_MEGA_DEVELOPERS],
            ['label' => 'Solutions', 'url' => '/{locale}/solutions', 'type' => NavigationItem::TYPE_MEGA_SOLUTIONS],
            ['label' => 'Coverage', 'url' => '/{locale}/coverage', 'type' => NavigationItem::TYPE_LINK],
            ['label' => 'Pricing', 'url' => '/{locale}/pricing', 'type' => NavigationItem::TYPE_LINK],
            ['label' => 'Blog', 'url' => '/{locale}/blog', 'type' => NavigationItem::TYPE_LINK],
            ['label' => 'Societe', 'url' => '/{locale}/company', 'type' => NavigationItem::TYPE_MEGA_COMPANY],
            ['label' => 'Contact', 'url' => '/{locale}/contact', 'type' => NavigationItem::TYPE_LINK],
            ['label' => 'Mentions legales', 'url' => '/{locale}/legal/mentions', 'type' => NavigationItem::TYPE_LINK],
            ['label' => 'CGU', 'url' => '/{locale}/legal/cgu', 'type' => NavigationItem::TYPE_LINK],
            ['label' => 'RGPD', 'url' => '/{locale}/legal/rgpd', 'type' => NavigationItem::TYPE_LINK],
        ];

        $services = collect(config('dream-digital.services.items', []))
            ->where('active', true)
            ->sortBy('order')
            ->map(function (array $service) use ($locale) {
                $label = $service['name'][$locale] ?? $service['name']['fr'] ?? ($service['slug'] ?? 'Service');

                return [
                    'label' => 'Produit - ' . $label,
                    'url' => '/{locale}/products/' . ($service['slug'] ?? $service['id']),
                    'type' => NavigationItem::TYPE_LINK,
                ];
            })
            ->values()
            ->all();

        return array_merge($pages, $services);
    }

    public function resolveUrl(?string $url, string $locale = 'fr'): string
    {
        $locale = $this->normalizeLocale($locale);
        $url = trim((string) $url);

        if ($url === '') {
            return '#';
        }

        $url = str_replace('{locale}', $locale, $url);

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, 'mailto:')) {
            return $url;
        }

        if (str_starts_with($url, '#')) {
            return $url;
        }

        return url(str_starts_with($url, '/') ? $url : '/' . $url);
    }

    private function databaseMenuIsAvailable(): bool
    {
        return Schema::hasTable('navigation_items')
            && NavigationItem::main()->active()->whereNull('parent_id')->exists();
    }

    private function normalizeItem(NavigationItem $item, string $locale): array
    {
        return [
            'id' => $item->id,
            'label' => $item->label($locale),
            'type' => $item->type,
            'url' => $this->resolveUrl($item->url, $locale),
            'raw_url' => $item->url,
            'opens_new_tab' => $item->opens_new_tab,
            'settings' => $item->settings ?? [],
            'children' => $item->children
                ->map(fn (NavigationItem $child) => $this->normalizeItem($child, $locale))
                ->values()
                ->all(),
        ];
    }

    private function fallbackItems(string $locale): array
    {
        $items = [
            ['label' => 'Produits', 'type' => NavigationItem::TYPE_MEGA_SERVICES, 'url' => '/{locale}/products'],
            ['label' => 'Developers', 'type' => NavigationItem::TYPE_MEGA_DEVELOPERS, 'url' => '/{locale}/developers'],
            ['label' => 'Solutions', 'type' => NavigationItem::TYPE_MEGA_SOLUTIONS, 'url' => '/{locale}/solutions'],
            ['label' => 'Coverage', 'type' => NavigationItem::TYPE_LINK, 'url' => '/{locale}/coverage'],
            ['label' => 'Pricing', 'type' => NavigationItem::TYPE_LINK, 'url' => '/{locale}/pricing'],
            ['label' => 'Blog', 'type' => NavigationItem::TYPE_LINK, 'url' => '/{locale}/blog'],
            ['label' => 'Societe', 'type' => NavigationItem::TYPE_MEGA_COMPANY, 'url' => '/{locale}/company'],
        ];

        return array_map(function (array $item) use ($locale) {
            $item['id'] = null;
            $item['raw_url'] = $item['url'];
            $item['url'] = $this->resolveUrl($item['url'], $locale);
            $item['opens_new_tab'] = false;
            $item['settings'] = [];
            $item['children'] = [];

            return $item;
        }, $items);
    }

    private function normalizeLocale(string $locale): string
    {
        return in_array($locale, ['fr', 'en'], true) ? $locale : 'fr';
    }
}
