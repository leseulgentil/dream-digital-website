<?php

namespace App\Http\Requests\Admin;

use App\Models\AiKnowledgeWebSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AiWebSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'type' => ['required', Rule::in([
                AiKnowledgeWebSource::TYPE_URL,
                AiKnowledgeWebSource::TYPE_SITEMAP,
                AiKnowledgeWebSource::TYPE_ENDPOINT_JSON,
            ])],
            'url' => ['required', 'url:http,https', 'max:2048', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! $this->isPublicWebUrl((string) $value)) {
                    $fail('Cette URL doit pointer vers un domaine public autorise.');
                }
            }],
            'locale' => ['required', Rule::in(['fr', 'en'])],
            'country_code' => ['required', Rule::in(['global', 'cd', 'ci', 'cg'])],
            'category' => ['nullable', 'string', 'max:80'],
            'frequency' => ['required', Rule::in([
                AiKnowledgeWebSource::FREQUENCY_MANUAL,
                AiKnowledgeWebSource::FREQUENCY_DAILY,
                AiKnowledgeWebSource::FREQUENCY_WEEKLY,
            ])],
            'import_status' => ['required', Rule::in(['draft', 'published'])],
            'sync_now' => ['nullable', 'boolean'],
        ];
    }

    private function isPublicWebUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return false;
        }

        $host = strtolower(trim($host, '[]'));

        if ($host === 'localhost' || str_ends_with($host, '.localhost')) {
            return false;
        }

        if (filter_var($host, FILTER_VALIDATE_IP) === false) {
            return true;
        }

        return filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }
}
