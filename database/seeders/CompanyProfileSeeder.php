<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use Illuminate\Database\Seeder;

class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        $entities = [
            'cd' => ['city' => 'Kinshasa', 'country_fr' => 'RDC', 'country_en' => 'DRC', 'lat' => '-4.3250', 'lng' => '15.3222'],
            'ci' => ['city' => 'Abidjan', 'country_fr' => 'Cote d Ivoire', 'country_en' => 'Ivory Coast', 'lat' => '5.3599', 'lng' => '-4.0083'],
            'cg' => ['city' => 'Brazzaville', 'country_fr' => 'Congo', 'country_en' => 'Congo', 'lat' => '-4.2634', 'lng' => '15.2429'],
        ];

        foreach ($entities as $countryCode => $entity) {
            foreach (CompanyProfile::LOCALES as $locale) {
                CompanyProfile::firstOrCreate(
                    ['country_code' => $countryCode, 'locale' => $locale],
                    [
                        'company_name' => 'Dream Digital',
                        'legal_name' => 'DREAM DIGITAL',
                        'public_phone' => null,
                        'whatsapp_number' => null,
                        'address_line' => null,
                        'city' => $entity['city'],
                        'country_label' => $locale === 'fr' ? $entity['country_fr'] : $entity['country_en'],
                        'registration_number' => null,
                        'tax_id' => null,
                        'support_hours' => null,
                        'latitude' => $entity['lat'],
                        'longitude' => $entity['lng'],
                        'email_sales' => 'sales@dream-digital.info',
                        'email_support' => 'support@dream-digital.info',
                        'email_security' => 'security@dream-digital.info',
                        'email_privacy' => 'privacy@dream-digital.info',
                        'social_linkedin' => null,
                        'social_twitter' => null,
                        'social_github' => null,
                        'og_image_path' => '/img/brand/logo-dd-horizontal.png',
                        'legal_validated' => filter_var(env('DD_LEGAL_VALIDATED', true), FILTER_VALIDATE_BOOLEAN),
                        'admin_password_rotated' => filter_var(env('DD_ADMIN_PASSWORD_ROTATED', true), FILTER_VALIDATE_BOOLEAN),
                        'public_basic_auth_disabled' => filter_var(env('DD_PUBLIC_BASIC_AUTH_DISABLED', false), FILTER_VALIDATE_BOOLEAN),
                        'backups_configured' => filter_var(env('DD_BACKUPS_CONFIGURED', false), FILTER_VALIDATE_BOOLEAN),
                        'env_backed_up' => filter_var(env('DD_ENV_BACKED_UP', false), FILTER_VALIDATE_BOOLEAN),
                        'deployment_runbook_reviewed' => filter_var(env('DD_DEPLOYMENT_RUNBOOK_REVIEWED', false), FILTER_VALIDATE_BOOLEAN),
                    ],
                );
            }
        }
    }
}
