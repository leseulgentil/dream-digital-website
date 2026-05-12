<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class MarketingPageSeeder extends Seeder
{
    /**
     * Peuple la table `pages` (section='marketing') a partir du config
     * dream-digital.pages.pages. 7 slugs (products, developers, solutions,
     * coverage, pricing, company, contact) x 2 locales = 14 rows.
     *
     * Idempotent via updateOrCreate sur la cle composite. Les pages produit
     * detail (/{locale}/products/{service}) ne sont PAS migrees ici --
     * elles utilisent le service Eloquent comme source de verite. Seules
     * les pages section landing/hub le sont.
     */
    public function run(): void
    {
        $pages = config('dream-digital.pages.pages', []);

        foreach ($pages as $slug => $cfg) {
            foreach (['fr', 'en'] as $locale) {
                $title = $cfg['title'][$locale] ?? $cfg['title']['fr'] ?? $slug;

                Page::updateOrCreate(
                    [
                        'slug' => $slug,
                        'section' => 'marketing',
                        'country_id' => null,
                        'locale' => $locale,
                    ],
                    [
                        'title' => $title,
                        'meta_description' => $this->extractMetaDescription($cfg, $locale),
                        'meta_image_path' => null,
                        'content_blocks' => [
                            'eyebrow' => $cfg['eyebrow'][$locale] ?? $cfg['eyebrow']['fr'] ?? null,
                            'lead' => $cfg['lead'][$locale] ?? $cfg['lead']['fr'] ?? null,
                        ],
                        'is_published' => true,
                        'published_at' => now(),
                    ],
                );
            }
        }

        $this->command?->info('Marketing pages seeded: ' . Page::where('section', 'marketing')->count() . ' entries.');
    }

    private function extractMetaDescription(array $cfg, string $locale): ?string
    {
        $lead = $cfg['lead'][$locale] ?? $cfg['lead']['fr'] ?? null;
        if (!$lead) {
            return null;
        }
        return mb_substr($lead, 0, 280);
    }
}
