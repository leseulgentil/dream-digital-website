<?php

namespace App\Http\Requests\Admin;

use App\Models\NavigationItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NavigationItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'integer', Rule::exists('navigation_items', 'id')],
            'menu_area' => ['required', 'string', Rule::in(['main'])],
            'label_fr' => ['required', 'string', 'max:120'],
            'label_en' => ['nullable', 'string', 'max:120'],
            'type' => ['required', 'string', Rule::in(array_keys(NavigationItem::TYPES))],
            'url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'settings_description_fr' => ['nullable', 'string', 'max:220'],
            'settings_description_en' => ['nullable', 'string', 'max:220'],
            'settings_icon' => ['nullable', 'string', 'max:80'],
            'opens_new_tab' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'parent_id' => $this->input('parent_id') ?: null,
            'menu_area' => $this->input('menu_area') ?: 'main',
            'sort_order' => $this->input('sort_order') ?: 0,
            'opens_new_tab' => $this->boolean('opens_new_tab'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $item = $this->route('navigation');

            if ($item instanceof NavigationItem && (int) $this->input('parent_id') === $item->id) {
                $validator->errors()->add('parent_id', 'Un lien ne peut pas etre son propre parent.');
            }
        });
    }

    public function payload(): array
    {
        $validated = $this->validated();

        return [
            'parent_id' => $validated['parent_id'] ?? null,
            'menu_area' => $validated['menu_area'],
            'label_fr' => $validated['label_fr'],
            'label_en' => $validated['label_en'] ?? null,
            'type' => $validated['type'],
            'url' => $validated['url'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'opens_new_tab' => (bool) ($validated['opens_new_tab'] ?? false),
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'settings' => array_filter([
                'description_fr' => $validated['settings_description_fr'] ?? null,
                'description_en' => $validated['settings_description_en'] ?? null,
                'icon' => $validated['settings_icon'] ?? null,
            ]),
        ];
    }
}
