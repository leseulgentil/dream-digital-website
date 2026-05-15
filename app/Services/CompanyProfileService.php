<?php

namespace App\Services;

use App\Models\CompanyProfile;
use Illuminate\Support\Facades\Schema;
use Throwable;

class CompanyProfileService
{
    public function applyToConfig(?string $locale = null): void
    {
        $profile = $this->profileForLocale($locale ?? app()->getLocale());

        if (! $profile) {
            return;
        }

        $site = $this->siteConfigFrom($profile);

        config([
            'dream-digital.site' => $site,
            'dream-digital.launch.legal_validated' => $profile->legal_validated,
            'dream-digital.launch.admin_password_rotated' => $profile->admin_password_rotated,
            'dream-digital.launch.public_basic_auth_disabled' => $profile->public_basic_auth_disabled,
            'dream-digital.launch.backups_configured' => $profile->backups_configured,
            'dream-digital.launch.env_backed_up' => $profile->env_backed_up,
            'dream-digital.launch.deployment_runbook_reviewed' => $profile->deployment_runbook_reviewed,
            'dream-digital.security.security_txt.contact' => $profile->email_security
                ?: $profile->email_support
                ?: config('dream-digital.security.security_txt.contact'),
        ]);
    }

    public function profileForLocale(?string $locale): ?CompanyProfile
    {
        if (! $this->tableExists()) {
            return null;
        }

        $locale = in_array($locale, CompanyProfile::LOCALES, true) ? $locale : 'fr';
        $countryCode = $this->currentCountryCode();

        return CompanyProfile::query()->where('country_code', $countryCode)->where('locale', $locale)->first()
            ?? CompanyProfile::query()->where('country_code', 'cd')->where('locale', $locale)->first()
            ?? CompanyProfile::query()->where('locale', $locale)->first()
            ?? CompanyProfile::query()->where('country_code', 'cd')->where('locale', 'fr')->first()
            ?? CompanyProfile::query()->where('locale', 'fr')->first();
    }

    public function tableExists(): bool
    {
        try {
            return Schema::hasTable('company_profiles');
        } catch (Throwable) {
            return false;
        }
    }

    private function siteConfigFrom(CompanyProfile $profile): array
    {
        $site = config('dream-digital.site', []);

        data_set($site, 'company.name', $profile->company_name);
        data_set($site, 'company.legal_name', $profile->legal_name);
        data_set($site, 'contact.phone', $profile->public_phone);
        data_set($site, 'contact.whatsapp', $profile->whatsapp_number);
        data_set($site, 'contact.support_hours', $profile->support_hours);
        data_set($site, 'contact.email_sales', $profile->email_sales);
        data_set($site, 'contact.email_support', $profile->email_support);
        data_set($site, 'contact.email_security', $profile->email_security);
        data_set($site, 'contact.email_privacy', $profile->email_privacy);
        data_set($site, 'company.address.line', $profile->address_line);
        data_set($site, 'company.address.city', $profile->city);
        data_set($site, 'company.address.country', $profile->country_label);
        data_set($site, 'company.registration_number', $profile->registration_number);
        data_set($site, 'company.tax_id', $profile->tax_id);
        data_set($site, 'company.geo.latitude', $profile->latitude);
        data_set($site, 'company.geo.longitude', $profile->longitude);
        data_set($site, 'company.entities', $this->entitiesForLocale($profile->locale));
        data_set($site, 'social.linkedin', $profile->social_linkedin);
        data_set($site, 'social.twitter', $profile->social_twitter);
        data_set($site, 'social.github', $profile->social_github);
        data_set($site, 'meta.og_image', $profile->og_image_path);

        return $site;
    }

    private function currentCountryCode(): string
    {
        $country = app()->bound('current_country') ? app('current_country') : null;
        $code = $country?->code;

        return in_array($code, array_keys(CompanyProfile::ENTITY_COUNTRIES), true) ? $code : 'cd';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function entitiesForLocale(string $locale): array
    {
        return CompanyProfile::query()
            ->where('locale', $locale)
            ->whereIn('country_code', array_keys(CompanyProfile::ENTITY_COUNTRIES))
            ->orderByRaw("case country_code when 'cd' then 1 when 'ci' then 2 when 'cg' then 3 else 4 end")
            ->get()
            ->map(fn (CompanyProfile $profile): array => [
                'country_code' => $profile->country_code,
                'company_name' => $profile->company_name,
                'legal_name' => $profile->legal_name,
                'public_phone' => $profile->public_phone,
                'whatsapp_number' => $profile->whatsapp_number,
                'address_line' => $profile->address_line,
                'city' => $profile->city,
                'country_label' => $profile->country_label,
                'registration_number' => $profile->registration_number,
                'tax_id' => $profile->tax_id,
                'support_hours' => $profile->support_hours,
                'latitude' => $profile->latitude,
                'longitude' => $profile->longitude,
                'email_sales' => $profile->email_sales,
                'email_support' => $profile->email_support,
                'email_security' => $profile->email_security,
                'email_privacy' => $profile->email_privacy,
            ])
            ->all();
    }
}
