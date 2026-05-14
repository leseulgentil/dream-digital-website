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
        ]);
    }

    public function update(CompanyProfileRequest $request): RedirectResponse
    {
        foreach ($request->profilePayloads() as $locale => $payload) {
            CompanyProfile::updateOrCreate(['locale' => $locale], $payload);
        }

        return redirect()
            ->route('admin.company-profile.edit')
            ->with('status', 'Company Profile mis a jour pour FR et EN.');
    }

    private function profiles()
    {
        return collect(CompanyProfile::LOCALES)
            ->mapWithKeys(fn (string $locale): array => [
                $locale => CompanyProfile::firstOrCreate(
                    ['locale' => $locale],
                    [
                        'company_name' => config('dream-digital.site.company.name', 'Dream Digital'),
                        'legal_name' => config('dream-digital.site.company.legal_name') ?: 'DREAM DIGITAL',
                        'public_phone' => config('dream-digital.site.contact.phone'),
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
                    ],
                ),
            ]);
    }
}
