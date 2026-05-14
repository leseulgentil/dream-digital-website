<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class LegalPageSeeder extends Seeder
{
    /**
     * Peuple la table `pages` (section='legal') a partir du config
     * dream-digital.legal.pages. Idempotent via updateOrCreate sur la
     * cle composite (slug, section, country_id, locale).
     */
    public function run(): void
    {
        $pages = config('dream-digital.legal.pages', []);

        foreach ($pages as $slug => $cfg) {
            foreach (['fr', 'en'] as $locale) {
                $title = $cfg['title'][$locale] ?? $cfg['title']['fr'] ?? $slug;

                Page::updateOrCreate(
                    [
                        'slug' => $cfg['slug'] ?? $slug,
                        'section' => 'legal',
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
                            'last_updated' => $cfg['last_updated'] ?? null,
                            'sections' => array_map(
                                fn ($section) => [
                                    'heading' => $section['heading'][$locale] ?? $section['heading']['fr'] ?? '',
                                    'body' => $section['body'][$locale] ?? $section['body']['fr'] ?? '',
                                ],
                                $cfg['sections'] ?? []
                            ),
                        ],
                        'is_published' => true,
                        'published_at' => now(),
                    ],
                );
            }
        }

        $this->command?->info('Legal pages seeded: ' . Page::where('section', 'legal')->count() . ' entries.');
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
