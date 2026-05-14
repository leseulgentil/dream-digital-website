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

        foreach (CompanyProfile::LOCALES as $locale) {
            $rules["profiles.{$locale}.locale"] = ['required', Rule::in(CompanyProfile::LOCALES)];
            $rules["profiles.{$locale}.company_name"] = ['required', 'string', 'max:120'];
            $rules["profiles.{$locale}.legal_name"] = ['nullable', 'string', 'max:160'];
            $rules["profiles.{$locale}.public_phone"] = ['nullable', 'string', 'max:60'];
            $rules["profiles.{$locale}.email_sales"] = ['nullable', 'email:rfc', 'max:160'];
            $rules["profiles.{$locale}.email_support"] = ['nullable', 'email:rfc', 'max:160'];
            $rules["profiles.{$locale}.email_security"] = ['nullable', 'email:rfc', 'max:160'];
            $rules["profiles.{$locale}.email_privacy"] = ['nullable', 'email:rfc', 'max:160'];
            $rules["profiles.{$locale}.social_linkedin"] = ['nullable', 'url', 'max:500'];
            $rules["profiles.{$locale}.social_twitter"] = ['nullable', 'url', 'max:500'];
            $rules["profiles.{$locale}.social_github"] = ['nullable', 'url', 'max:500'];
            $rules["profiles.{$locale}.og_image_path"] = ['nullable', 'string', 'max:500'];
            $rules["profiles.{$locale}.legal_validated"] = ['sometimes', 'boolean'];
            $rules["profiles.{$locale}.admin_password_rotated"] = ['sometimes', 'boolean'];
        }

        return $rules;
    }

    public function prepareForValidation(): void
    {
        $profiles = $this->input('profiles', []);

        foreach (CompanyProfile::LOCALES as $locale) {
            $profiles[$locale]['locale'] = $locale;
            $profiles[$locale]['legal_validated'] = (bool) data_get($profiles, "{$locale}.legal_validated", false);
            $profiles[$locale]['admin_password_rotated'] = (bool) data_get($profiles, "{$locale}.admin_password_rotated", false);
        }

        $this->merge(['profiles' => $profiles]);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function profilePayloads(): array
    {
        return collect($this->validated('profiles'))
            ->map(fn (array $profile): array => [
                'company_name' => $profile['company_name'],
                'legal_name' => $profile['legal_name'] ?? null,
                'public_phone' => $profile['public_phone'] ?? null,
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
            ])
            ->all();
    }
}
