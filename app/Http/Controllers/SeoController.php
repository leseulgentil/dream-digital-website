<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class SeoController extends Controller
{
    /**
     * robots.txt dynamique :
     *  - DD_PUBLIC_INDEXABLE=true  -> User-agent: * + Allow: / + Sitemap
     *  - sinon -> Disallow: / (alignement avec meta robots noindex)
     */
    public function robots(): Response
    {
        $indexable = $this->isPublicIndexable();
        $base = rtrim(config('app.url'), '/');

        $lines = ['User-agent: *'];
        if ($indexable) {
            $lines[] = 'Allow: /';
            $lines[] = '';
            $lines[] = "Sitemap: {$base}/sitemap.xml";
        } else {
            $lines[] = 'Disallow: /';
        }

        return response(implode("\n", $lines) . "\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * sitemap.xml dynamique listant les URLs publiques :
     *  - homes locale (fr, en)
     *  - hubs marketing (products, developers, solutions, coverage, pricing,
     *    company, contact) x 2 locales
     *  - pages produit detail (services actifs) x 2 locales
     *  - pages legales (mentions, cgu, rgpd) x 2 locales
     *
     * Renvoie un 410 Gone si DD_PUBLIC_INDEXABLE=false, pour aligner avec
     * la posture noindex (rien a indexer tant que le site n'est pas
     * ouvert publiquement).
     */
    public function sitemap(): Response
    {
        $indexable = $this->isPublicIndexable();

        if (!$indexable) {
            return response("Sitemap unavailable: site not yet publicly indexable.\n", 410, ['Content-Type' => 'text/plain']);
        }

        $base = rtrim(config('app.url'), '/');
        $locales = ['fr', 'en'];
        $hubs = ['products', 'developers', 'solutions', 'coverage', 'pricing', 'company', 'contact', 'blog'];
        $legalSlugs = ['mentions', 'cgu', 'rgpd'];
        $serviceSlugs = collect(config('dream-digital.services.items', []))
            ->where('active', true)
            ->pluck('slug')
            ->filter()
            ->values()
            ->all();

        $urls = [];
        $today = now()->toDateString();

        foreach ($locales as $locale) {
            $urls[] = ['loc' => "{$base}/{$locale}", 'priority' => '1.0', 'changefreq' => 'weekly', 'lastmod' => $today];

            foreach ($hubs as $hub) {
                $urls[] = ['loc' => "{$base}/{$locale}/{$hub}", 'priority' => '0.8', 'changefreq' => 'weekly', 'lastmod' => $today];
            }

            foreach ($serviceSlugs as $slug) {
                $urls[] = ['loc' => "{$base}/{$locale}/products/{$slug}", 'priority' => '0.7', 'changefreq' => 'monthly', 'lastmod' => $today];
            }

            foreach ($legalSlugs as $slug) {
                $urls[] = ['loc' => "{$base}/{$locale}/legal/{$slug}", 'priority' => '0.3', 'changefreq' => 'yearly', 'lastmod' => $this->lastmodForLegal($slug, $locale, $today)];
            }
        }

        foreach ($this->blogUrls($base, $today) as $blogUrl) {
            $urls[] = $blogUrl;
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>\n";
            $xml .= "    <lastmod>{$u['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$u['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>' . "\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Last-modified du Page DB si dispo, sinon date du jour.
     */
    private function lastmodForLegal(string $slug, string $locale, string $fallback): string
    {
        if (!Schema::hasTable('pages')) {
            return $fallback;
        }
        $page = Page::published()
            ->where('section', 'legal')
            ->where('slug', $slug)
            ->where('locale', $locale)
            ->whereNull('country_id')
            ->first();
        return optional($page?->updated_at)->toDateString() ?? $fallback;
    }

    private function isPublicIndexable(): bool
    {
        return filter_var(config('dream-digital.launch.public_indexable', false), FILTER_VALIDATE_BOOLEAN);
    }

    private function blogUrls(string $base, string $fallback): array
    {
        if (!Schema::hasTable('pages')) {
            return [];
        }

        return Page::published()
            ->where('section', 'blog')
            ->whereNull('country_id')
            ->orderBy('locale')
            ->orderBy('slug')
            ->get()
            ->map(fn (Page $page) => [
                'loc' => "{$base}/{$page->locale}/blog/{$page->slug}",
                'priority' => '0.6',
                'changefreq' => 'monthly',
                'lastmod' => optional($page->updated_at)->toDateString() ?? $fallback,
            ])
            ->all();
    }
}
