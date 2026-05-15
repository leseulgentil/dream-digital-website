<?php

namespace App\Http\Requests\Admin;

use App\Models\CompanyProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'profiles' => ['required', 'array'],
        ];

        foreach (array_keys(CompanyProfile::ENTITY_COUNTRIES) as $countryCode) {
            foreach (CompanyProfile::LOCALES as $locale) {
                $base = "profiles.{$countryCode}.{$locale}";

                $rules["{$base}.country_code"] = ['required', Rule::in(array_keys(CompanyProfile::ENTITY_COUNTRIES))];
                $rules["{$base}.locale"] = ['required', Rule::in(CompanyProfile::LOCALES)];
                $rules["{$base}.company_name"] = ['required', 'string', 'max:120'];
                $rules["{$base}.legal_name"] = ['nullable', 'string', 'max:160'];
                $rules["{$base}.public_phone"] = ['nullable', 'string', 'max:60'];
                $rules["{$base}.whatsapp_number"] = ['nullable', 'string', 'max:60'];
                $rules["{$base}.address_line"] = ['nullable', 'string', 'max:255'];
                $rules["{$base}.city"] = ['nullable', 'string', 'max:120'];
                $rules["{$base}.country_label"] = ['nullable', 'string', 'max:120'];
                $rules["{$base}.registration_number"] = ['nullable', 'string', 'max:160'];
                $rules["{$base}.tax_id"] = ['nullable', 'string', 'max:160'];
                $rules["{$base}.support_hours"] = ['nullable', 'string', 'max:160'];
                $rules["{$base}.latitude"] = ['nullable', 'numeric', 'between:-90,90'];
                $rules["{$base}.longitude"] = ['nullable', 'numeric', 'between:-180,180'];
                $rules["{$base}.email_sales"] = ['nullable', 'email:rfc', 'max:160'];
                $rules["{$base}.email_support"] = ['nullable', 'email:rfc', 'max:160'];
                $rules["{$base}.email_security"] = ['nullable', 'email:rfc', 'max:160'];
                $rules["{$base}.email_privacy"] = ['nullable', 'email:rfc', 'max:160'];
                $rules["{$base}.social_linkedin"] = ['nullable', 'url', 'max:500'];
                $rules["{$base}.social_twitter"] = ['nullable', 'url', 'max:500'];
                $rules["{$base}.social_github"] = ['nullable', 'url', 'max:500'];
                $rules["{$base}.og_image_path"] = ['nullable', 'string', 'max:500'];
                $rules["{$base}.legal_validated"] = ['sometimes', 'boolean'];
                $rules["{$base}.admin_password_rotated"] = ['sometimes', 'boolean'];
            }
        }

        return $rules;
    }

    public function prepareForValidation(): void
    {
        $profiles = $this->input('profiles', []);

        foreach (array_keys(CompanyProfile::ENTITY_COUNTRIES) as $countryCode) {
            foreach (CompanyProfile::LOCALES as $locale) {
                $profiles[$countryCode][$locale]['country_code'] = $countryCode;
                $profiles[$countryCode][$locale]['locale'] = $locale;
                $profiles[$countryCode][$locale]['legal_validated'] = (bool) data_get($profiles, "{$countryCode}.{$locale}.legal_validated", false);
                $profiles[$countryCode][$locale]['admin_password_rotated'] = (bool) data_get($profiles, "{$countryCode}.{$locale}.admin_password_rotated", false);
            }
        }

        $this->merge(['profiles' => $profiles]);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function profilePayloads(): array
    {
        return collect($this->validated('profiles'))
            ->flatMap(fn (array $localizedProfiles): array => array_values($localizedProfiles))
            ->map(fn (array $profile): array => $this->payloadFrom($profile))
            ->values()
            ->all();
    }

    /**
     * @param array<string, mixed> $profile
     * @return array<string, mixed>
     */
    private function payloadFrom(array $profile): array
    {
        return [
            'country_code' => $profile['country_code'],
            'locale' => $profile['locale'],
            'company_name' => $profile['company_name'],
            'legal_name' => $profile['legal_name'] ?? null,
            'public_phone' => $profile['public_phone'] ?? null,
            'whatsapp_number' => $profile['whatsapp_number'] ?? null,
            'address_line' => $profile['address_line'] ?? null,
            'city' => $profile['city'] ?? null,
            'country_label' => $profile['country_label'] ?? null,
            'registration_number' => $profile['registration_number'] ?? null,
            'tax_id' => $profile['tax_id'] ?? null,
            'support_hours' => $profile['support_hours'] ?? null,
            'latitude' => $profile['latitude'] ?? null,
            'longitude' => $profile['longitude'] ?? null,
            'email_sales' => $profile['email_sales'] ?? null,
            'email_support' => $profile['email_support'] ?? null,
            'email_security' => $profile['email_security'] ?? null,
            'email_privacy' => $profile['email_privacy'] ?? null,
            'social_linkedin' => $profile['social_linkedin'] ?? null,
            'social_twitter' => $profile['social_twitter'] ?? null,
            'social_github' => $profile['social_github'] ?? null,
            'og_image_path' => $profile['og_image_path'] ?? null,
            'legal_validated' => (bool) ($profile['legal_validated'] ?? false),
            'admin_password_rotated' => (bool) ($profile['admin_password_rotated'] ?? false),
        ];
    }
}
