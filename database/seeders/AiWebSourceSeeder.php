<?php

namespace Database\Seeders;

use App\Models\AiKnowledgeWebSource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class AiWebSourceSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedEsimZone();
    }

    private function seedEsimZone(): void
    {
        $config = config('dream-digital.ai.web_sources.esimzone', []);
        $url = trim((string) ($config['url'] ?? ''));

        if (! filter_var($config['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN) || $url === '') {
            return;
        }

        $frequency = $this->allowed((string) ($config['frequency'] ?? AiKnowledgeWebSource::FREQUENCY_WEEKLY), [
            AiKnowledgeWebSource::FREQUENCY_MANUAL,
            AiKnowledgeWebSource::FREQUENCY_DAILY,
            AiKnowledgeWebSource::FREQUENCY_WEEKLY,
        ], AiKnowledgeWebSource::FREQUENCY_WEEKLY);
        $importStatus = $this->allowed((string) ($config['import_status'] ?? 'draft'), ['draft', 'published'], 'draft');
        $source = AiKnowledgeWebSource::query()->firstOrNew(['url' => $url]);
        $metadata = $source->metadata ?? [];
        $authToken = trim((string) ($config['auth_token'] ?? ''));

        if ($authToken !== '') {
            $metadata['auth_token'] = Crypt::encryptString($authToken);
        } elseif (! array_key_exists('auth_token', $metadata)) {
            $metadata['auth_token'] = null;
        }

        $source->fill([
            'title' => trim((string) ($config['title'] ?? 'eSIMZone API')) ?: 'eSIMZone API',
            'type' => AiKnowledgeWebSource::TYPE_ENDPOINT_JSON,
            'url' => $url,
            'locale' => $this->allowed((string) ($config['locale'] ?? 'fr'), ['fr', 'en'], 'fr'),
            'country_code' => $this->allowed((string) ($config['country_code'] ?? 'global'), ['global', 'cd', 'ci', 'cg'], 'global'),
            'category' => trim((string) ($config['category'] ?? 'esim')) ?: 'esim',
            'frequency' => $frequency,
            'import_status' => $importStatus,
            'status' => AiKnowledgeWebSource::STATUS_ACTIVE,
            'next_sync_at' => $frequency === AiKnowledgeWebSource::FREQUENCY_MANUAL
                ? null
                : ($source->next_sync_at ?? now()),
            'metadata' => $metadata,
        ]);
        $source->save();

        $this->command?->info("AI web source ready: {$source->title}");
    }

    /**
     * @param  array<int, string>  $allowed
     */
    private function allowed(string $value, array $allowed, string $fallback): string
    {
        return in_array($value, $allowed, true) ? $value : $fallback;
    }
}
