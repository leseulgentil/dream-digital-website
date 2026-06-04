<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AiChatSettingsRequest extends FormRequest
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
            'enabled' => ['nullable', 'boolean'],
            'model' => ['required', 'string', 'max:80'],
            'max_sources' => ['required', 'integer', 'min:1', 'max:10'],
            'max_message_chars' => ['required', 'integer', 'min:200', 'max:2000'],
            'fallback_contact_mode' => ['required', Rule::in(['contact_form', 'whatsapp'])],
            'greetings.fr' => ['required', 'string', 'max:240'],
            'greetings.en' => ['required', 'string', 'max:240'],
            'display_rules_json' => ['nullable', 'string', 'max:4000'],
            'system_prompt' => ['required', 'string', 'max:4000'],
        ];
    }

    /**
     * @return array{pages: array<int, string>, countries?: array<int, string>, locales?: array<int, string>}
     */
    public function decodedDisplayRules(): array
    {
        $raw = trim((string) $this->input('display_rules_json'));

        if ($raw === '') {
            return ['pages' => ['*']];
        }

        $decoded = json_decode($raw, true);

        if (! is_array($decoded)) {
            return ['pages' => ['*']];
        }

        $rules = [
            'pages' => $this->stringList($decoded['pages'] ?? ['*']),
        ];

        foreach (['countries', 'locales'] as $key) {
            $values = $this->stringList($decoded[$key] ?? []);
            if ($values !== []) {
                $rules[$key] = $values;
            }
        }

        if ($rules['pages'] === []) {
            $rules['pages'] = ['*'];
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $raw = trim((string) $this->input('display_rules_json'));

            if ($raw === '') {
                return;
            }

            $decoded = json_decode($raw, true);

            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                $validator->errors()->add('display_rules_json', 'Les regles doivent etre un JSON valide.');
            }
        });
    }

    /**
     * @return array<int, string>
     */
    private function stringList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(fn (mixed $item): string => strtolower(trim((string) $item)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
