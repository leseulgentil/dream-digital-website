<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use Illuminate\Database\Seeder;

class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        foreach (CompanyProfile::LOCALES as $locale) {
            CompanyProfile::updateOrCreate(
                ['locale' => $locale],
                [
                    'company_name' => 'Dream Digital',
                    'legal_name' => env('DD_COMPANY_LEGAL_NAME', 'DREAM DIGITAL'),
                    'public_phone' => env('DD_PUBLIC_PHONE') ?: null,
                    'email_sales' => env('DD_SALES_EMAIL', 'sales@dream-digital.info'),
                    'email_support' => env('DD_SUPPORT_EMAIL', 'support@dream-digital.info'),
                    'email_security' => env('DD_SECURITY_EMAIL', 'security@dream-digital.info'),
                    'email_privacy' => env('DD_PRIVACY_EMAIL', 'privacy@dream-digital.info'),
                    'social_linkedin' => env('DD_SOCIAL_LINKEDIN') ?: null,
                    'social_twitter' => env('DD_SOCIAL_TWITTER') ?: null,
                    'social_github' => env('DD_SOCIAL_GITHUB') ?: null,
                    'og_image_path' => env('DD_OG_IMAGE', '/img/brand/logo-dd-horizontal.png'),
                    'legal_validated' => filter_var(env('DD_LEGAL_VALIDATED', true), FILTER_VALIDATE_BOOLEAN),
                    'admin_password_rotated' => filter_var(env('DD_ADMIN_PASSWORD_ROTATED', true), FILTER_VALIDATE_BOOLEAN),
                ],
            );
        }
    }
}
