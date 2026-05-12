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
        $indexable = filter_var(env('DD_PUBLIC_INDEXABLE', false), FILTER_VALIDATE_BOOLEAN);
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
        $indexable = filter_var(env('DD_PUBLIC_INDEXABLE', false), FILTER_VALIDATE_BOOLEAN);

        if (!$indexable) {
            return response("Sitemap unavailable: site not yet publicly indexable.\n", 410, ['Content-Type' => 'text/plain']);
        }

        $base = rtrim(config('app.url'), '/');
        $locales = ['fr', 'en'];
        $hubs = ['products', 'developers', 'solutions', 'coverage', 'pricing', 'company', 'contact'];
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
}
