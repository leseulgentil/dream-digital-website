<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = collect(config('dream-digital.services.items', []))
            ->map(function (array $service) {
                return [
                    'slug' => $this->normalizeSlug($service['slug'] ?? $service['id'] ?? ''),
                    'name_fr' => $service['name']['fr'] ?? $service['id'],
                    'name_en' => $service['name']['en'] ?? $service['name']['fr'] ?? $service['id'],
                    'icon' => $service['icon'] ?? 'bx-radio-circle',
                    'color_accent' => null,
                    'short_desc_fr' => $service['tagline']['fr'] ?? null,
                    'short_desc_en' => $service['tagline']['en'] ?? $service['tagline']['fr'] ?? null,
                    'is_active' => (bool) ($service['active'] ?? true),
                    'sort_order' => (int) ($service['order'] ?? 0),
                ];
            })
            ->values();

        foreach ($services as $data) {
            Service::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }

    private function normalizeSlug(string $slug): string
    {
        return match ($slug) {
            'sms-a2p' => 'sms',
            'sip' => 'sip-trunking',
            default => $slug,
        };
    }
}
