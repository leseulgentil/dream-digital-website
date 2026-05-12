<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $page = $this->route('page');
        $pageId = is_object($page) ? $page->id : null;

        return [
            'slug' => ['required', 'string', 'max:120', 'regex:/^[a-z0-9-]+$/'],
            'section' => ['required', 'string', 'max:60', Rule::in(['legal', 'marketing', 'blog', 'help'])],
            'country_id' => ['nullable', 'integer', Rule::exists('countries', 'id')],
            'locale' => ['required', 'string', 'size:2', Rule::in(['fr', 'en'])],
            'title' => ['required', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_image_path' => ['nullable', 'string', 'max:500'],
            'eyebrow' => ['nullable', 'string', 'max:200'],
            'lead' => ['nullable', 'string'],
            'last_updated' => ['nullable', 'string', 'max:30'],
            'sections_json' => ['nullable', 'string'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    public function prepareForValidation(): void
    {
        $rawSlug = $this->input('slug');
        $rawLocale = $this->input('locale');
        $rawCountry = $this->input('country_id');

        $this->merge([
            'is_published' => $this->boolean('is_published'),
            'country_id' => $rawCountry !== '' && $rawCountry !== null ? $rawCountry : null,
            'slug' => $rawSlug ? strtolower(trim($rawSlug)) : null,
            'locale' => $rawLocale ? strtolower($rawLocale) : null,
        ]);
    }

    /**
     * Valide que sections_json est du JSON valide si fourni, et le
     * decode pour utilisation dans le controller. Retourne null si vide.
     */
    public function decodedSections(): ?array
    {
        $raw = $this->input('sections_json');
        if (empty($raw)) {
            return null;
        }
        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        return is_array($decoded) ? $decoded : null;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $raw = $this->input('sections_json');
            if (!empty($raw) && $this->decodedSections() === null) {
                $validator->errors()->add('sections_json', 'Le champ "Sections (JSON)" doit etre un JSON valide (tableau).');
            }
        });
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'Le slug ne doit contenir que des lettres minuscules, chiffres et tirets.',
        ];
    }
}
