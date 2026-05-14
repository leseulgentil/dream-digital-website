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

        return CompanyProfile::query()->where('locale', $locale)->first()
            ?? CompanyProfile::query()->where('locale', 'fr')->first()
            ?? CompanyProfile::query()->where('locale', 'en')->first();
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
        data_set($site, 'contact.email_sales', $profile->email_sales);
        data_set($site, 'contact.email_support', $profile->email_support);
        data_set($site, 'contact.email_security', $profile->email_security);
        data_set($site, 'contact.email_privacy', $profile->email_privacy);
        data_set($site, 'social.linkedin', $profile->social_linkedin);
        data_set($site, 'social.twitter', $profile->social_twitter);
        data_set($site, 'social.github', $profile->social_github);
        data_set($site, 'meta.og_image', $profile->og_image_path);

        return $site;
    }
}
