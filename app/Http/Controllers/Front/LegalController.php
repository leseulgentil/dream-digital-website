<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegalController extends Controller
{
    public function show(Request $request, string $locale, string $slug): View
    {
        $locale = in_array($locale, ['fr', 'en'], true) ? $locale : 'fr';

        $legal = $this->resolveLegal($slug, $locale);
        abort_if($legal === null, 404);

        app()->setLocale($locale);
        $request->session()->put('locale', $locale);

        return view('content.front-pages.legal-page', [
            'pageConfigs' => ['myLayout' => 'front'],
            'locale'      => $locale,
            'page'        => 'legal-' . $slug,
            'legal'       => $legal,
            'site'        => config('dream-digital.site'),
            'allPages'    => $this->resolveAllPages($locale),
        ]);
    }

    /**
     * Lookup en DB d'abord (table `pages`, section='legal'), fallback
     * sur config('dream-digital.legal') si pas trouve. La DB devient la
     * source de verite si peuplee (via LegalPageSeeder ou edition admin).
     */
    private function resolveLegal(string $slug, string $locale): ?array
    {
        $page = Page::published()
            ->where('section', 'legal')
            ->where('slug', $slug)
            ->where('locale', $locale)
            ->whereNull('country_id')
            ->first();

        if ($page !== null) {
            return $this->pageToLegalArray($page);
        }

        $cfg = config("dream-digital.legal.pages.$slug");
        if (empty($cfg)) {
            return null;
        }

        return [
            'slug'         => $cfg['slug'] ?? $slug,
            'title'        => $cfg['title'][$locale] ?? $cfg['title']['fr'] ?? $slug,
            'eyebrow'      => $cfg['eyebrow'][$locale] ?? $cfg['eyebrow']['fr'] ?? '',
            'lead'         => $cfg['lead'][$locale] ?? $cfg['lead']['fr'] ?? '',
            'last_updated' => $cfg['last_updated'] ?? null,
            'sections'     => array_map(
                fn ($section) => [
                    'heading' => $section['heading'][$locale] ?? $section['heading']['fr'] ?? '',
                    'body' => $section['body'][$locale] ?? $section['body']['fr'] ?? '',
                ],
                $cfg['sections'] ?? []
            ),
            'source'       => 'config',
        ];
    }

    private function pageToLegalArray(Page $page): array
    {
        $blocks = $page->content_blocks ?? [];
        return [
            'slug'         => $page->slug,
            'title'        => $page->title,
            'eyebrow'      => $blocks['eyebrow'] ?? '',
            'lead'         => $blocks['lead'] ?? '',
            'last_updated' => $blocks['last_updated'] ?? optional($page->updated_at)->format('Y-m-d'),
            'sections'     => $blocks['sections'] ?? [],
            'source'       => 'db',
        ];
    }

    /**
     * Liste des slugs legal disponibles pour la ToC. Privilegie DB,
     * sinon retombe sur config.
     */
    private function resolveAllPages(string $locale): array
    {
        $dbPages = Page::published()
            ->where('section', 'legal')
            ->where('locale', $locale)
            ->whereNull('country_id')
            ->orderBy('slug')
            ->get();

        if ($dbPages->isNotEmpty()) {
            return $dbPages
                ->mapWithKeys(fn (Page $p) => [$p->slug => ['slug' => $p->slug, 'title' => $p->title]])
                ->all();
        }

        return collect(config('dream-digital.legal.pages', []))
            ->mapWithKeys(fn ($cfg, $slug) => [$slug => [
                'slug' => $cfg['slug'] ?? $slug,
                'title' => $cfg['title'][$locale] ?? $cfg['title']['fr'] ?? $slug,
            ]])
            ->all();
    }
}
