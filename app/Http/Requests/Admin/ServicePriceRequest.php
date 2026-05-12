<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServicePriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => ['required', 'integer', Rule::exists('services', 'id')],
            'country_id' => ['required', 'integer', Rule::exists('countries', 'id')],
            'destination_country' => ['nullable', 'string', 'size:2', 'alpha'],
            'label_fr' => ['required', 'string', 'max:200'],
            'label_en' => ['required', 'string', 'max:200'],
            'price_usd' => ['required', 'numeric', 'min:0', 'max:999999.999999'],
            'price_local' => ['nullable', 'numeric', 'min:0', 'max:999999.999999'],
            'local_currency' => ['nullable', 'string', 'size:3', 'alpha'],
            'unit' => ['required', 'string', 'max:20'],
            'use_manual_local' => ['sometimes', 'boolean'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'use_manual_local' => $this->boolean('use_manual_local'),
            'is_published' => $this->boolean('is_published'),
            'destination_country' => $this->destination_country
                ? strtoupper($this->destination_country)
                : null,
            'local_currency' => $this->local_currency
                ? strtoupper($this->local_currency)
                : null,
        ]);
    }

    public function messages(): array
    {
        return [
            'destination_country.size' => 'Le code pays destination doit faire 2 caracteres (ISO 3166-1 alpha-2).',
            'local_currency.size' => 'Le code devise doit faire 3 caracteres (ISO 4217).',
        ];
    }
}
