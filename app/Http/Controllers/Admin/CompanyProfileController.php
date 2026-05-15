<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompanyProfileRequest;
use App\Models\CompanyProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompanyProfileController extends Controller
{
    public function edit(): View
    {
        return view('admin.company-profile.edit', [
            'profiles' => $this->profiles(),
            'locales' => CompanyProfile::LOCALES,
            'entityCountries' => CompanyProfile::ENTITY_COUNTRIES,
        ]);
    }

    public function update(CompanyProfileRequest $request): RedirectResponse
    {
        foreach ($request->profilePayloads() as $payload) {
            CompanyProfile::updateOrCreate([
                'country_code' => $payload['country_code'],
                'locale' => $payload['locale'],
            ], $payload);
        }

        return redirect()
            ->route('admin.company-profile.edit')
            ->with('status', 'Company Profile mis a jour pour FR et EN.');
    }

    private function profiles()
    {
        return collect(CompanyProfile::ENTITY_COUNTRIES)
            ->mapWithKeys(fn (array $country, string $countryCode): array => [
                $countryCode => collect(CompanyProfile::LOCALES)
                    ->mapWithKeys(fn (string $locale): array => [
                        $locale => CompanyProfile::firstOrCreate(
                            ['country_code' => $countryCode, 'locale' => $locale],
                            $this->defaultPayload($countryCode, $country),
                        ),
                    ]),
            ]);
    }

    /**
     * @param array{label:string, city:string} $country
     * @return array<string, mixed>
     */
    private function defaultPayload(string $countryCode, array $country): array
    {
        return [
            'company_name' => config('dream-digital.site.company.name', 'Dream Digital'),
            'legal_name' => config('dream-digital.site.company.legal_name') ?: 'DREAM DIGITAL',
            'public_phone' => $countryCode === 'cd' ? config('dream-digital.site.contact.phone') : null,
            'whatsapp_number' => $countryCode === 'cd' ? config('dream-digital.site.contact.whatsapp') : null,
            'address_line' => $countryCode === 'cd' ? data_get(config('dream-digital.site'), 'company.address.line') : null,
            'city' => $country['city'],
            'country_label' => $country['label'],
            'registration_number' => $countryCode === 'cd' ? config('dream-digital.site.company.registration_number') : null,
            'tax_id' => $countryCode === 'cd' ? config('dream-digital.site.company.tax_id') : null,
            'support_hours' => config('dream-digital.site.contact.support_hours'),
            'latitude' => null,
            'longitude' => null,
            'email_sales' => config('dream-digital.site.contact.email_sales', 'sales@dream-digital.info'),
            'email_support' => config('dream-digital.site.contact.email_support') ?: 'support@dream-digital.info',
            'email_security' => config('dream-digital.site.contact.email_security') ?: 'security@dream-digital.info',
            'email_privacy' => config('dream-digital.site.contact.email_privacy') ?: 'privacy@dream-digital.info',
            'social_linkedin' => config('dream-digital.site.social.linkedin'),
            'social_twitter' => config('dream-digital.site.social.twitter'),
            'social_github' => config('dream-digital.site.social.github'),
            'og_image_path' => config('dream-digital.site.meta.og_image') ?: '/img/brand/logo-dd-horizontal.png',
            'legal_validated' => filter_var(config('dream-digital.launch.legal_validated', true), FILTER_VALIDATE_BOOLEAN),
            'admin_password_rotated' => filter_var(config('dream-digital.launch.admin_password_rotated', true), FILTER_VALIDATE_BOOLEAN),
            'public_basic_auth_disabled' => filter_var(config('dream-digital.launch.public_basic_auth_disabled', false), FILTER_VALIDATE_BOOLEAN),
            'backups_configured' => filter_var(config('dream-digital.launch.backups_configured', false), FILTER_VALIDATE_BOOLEAN),
            'env_backed_up' => filter_var(config('dream-digital.launch.env_backed_up', false), FILTER_VALIDATE_BOOLEAN),
            'deployment_runbook_reviewed' => filter_var(config('dream-digital.launch.deployment_runbook_reviewed', false), FILTER_VALIDATE_BOOLEAN),
        ];
    }
}
