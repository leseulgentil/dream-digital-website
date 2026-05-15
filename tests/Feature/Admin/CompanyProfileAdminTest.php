<?php

namespace Tests\Feature\Admin;

use App\Models\CompanyProfile;
use App\Models\User;
use Database\Seeders\CompanyProfileSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyProfileAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_open_company_profile_admin(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_OWNER]));

        $this->get(route('admin.company-profile.edit'))
            ->assertOk()
            ->assertSee('Company Profile')
            ->assertSee('DREAM DIGITAL')
            ->assertSee('WhatsApp')
            ->assertSee('Adresse publique')
            ->assertSee('RCCM')
            ->assertSee('Latitude')
            ->assertSee('Longitude')
            ->assertSee('RDC')
            ->assertSee('Cote d Ivoire')
            ->assertSee('Congo')
            ->assertSee('Profil FR')
            ->assertSee('Profil EN');

        foreach (['cd', 'ci', 'cg'] as $countryCode) {
            $this->assertDatabaseHas('company_profiles', ['country_code' => $countryCode, 'locale' => 'fr']);
            $this->assertDatabaseHas('company_profiles', ['country_code' => $countryCode, 'locale' => 'en']);
        }
    }

    public function test_owner_can_update_country_entity_profiles(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_OWNER]));

        $this->put(route('admin.company-profile.update'), [
            'profiles' => [
                'cd' => [
                    'fr' => $this->profilePayload('cd', 'fr', 'DREAM DIGITAL RDC', '+243 000 000 000'),
                    'en' => $this->profilePayload('cd', 'en', 'DREAM DIGITAL DRC', '+243 111 111 111'),
                ],
                'ci' => [
                    'fr' => $this->profilePayload('ci', 'fr', 'DREAM DIGITAL COTE D IVOIRE', '+225 0101010101'),
                    'en' => $this->profilePayload('ci', 'en', 'DREAM DIGITAL IVORY COAST', '+225 0202020202'),
                ],
                'cg' => [
                    'fr' => $this->profilePayload('cg', 'fr', 'DREAM DIGITAL CONGO', '+242 060000000'),
                    'en' => $this->profilePayload('cg', 'en', 'DREAM DIGITAL CONGO', '+242 050000000'),
                ],
            ],
        ])->assertRedirect(route('admin.company-profile.edit'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('company_profiles', [
            'country_code' => 'cd',
            'locale' => 'fr',
            'legal_name' => 'DREAM DIGITAL RDC',
            'public_phone' => '+243 000 000 000',
            'whatsapp_number' => '+243 999 999 999',
            'address_line' => 'Boulevard du 30 Juin, Kinshasa',
            'city' => 'Kinshasa',
            'country_label' => 'RDC',
            'registration_number' => 'CD/KIN/RCCM/26-B-001',
            'tax_id' => 'A2600001Z',
            'support_hours' => 'Lundi-vendredi, 09:00-18:00 CAT',
            'latitude' => '-4.3250',
            'longitude' => '15.3222',
            'legal_validated' => true,
            'admin_password_rotated' => true,
        ]);
        $this->assertDatabaseHas('company_profiles', [
            'country_code' => 'ci',
            'locale' => 'en',
            'legal_name' => 'DREAM DIGITAL IVORY COAST',
            'public_phone' => '+225 0202020202',
            'whatsapp_number' => '+225 888 888 888',
            'address_line' => 'Plateau, Abidjan',
            'city' => 'Abidjan',
            'country_label' => 'Ivory Coast',
            'registration_number' => 'CI/ABJ/RCCM/26-B-001',
            'tax_id' => 'CI-A2600001Z',
            'support_hours' => 'Monday-Friday, 09:00-18:00 CAT',
            'latitude' => '5.3599',
            'longitude' => '-4.0083',
        ]);
    }

    public function test_company_profile_values_are_applied_to_site_config(): void
    {
        CompanyProfile::create([
            'country_code' => 'cd',
            'locale' => 'fr',
            'company_name' => 'Dream Digital',
            'legal_name' => 'DREAM DIGITAL',
            'public_phone' => '+243 000 000 000',
            'whatsapp_number' => '+243 999 999 999',
            'address_line' => 'Boulevard du 30 Juin, Kinshasa',
            'city' => 'Kinshasa',
            'country_label' => 'RDC',
            'registration_number' => 'CD/KIN/RCCM/26-B-001',
            'tax_id' => 'A2600001Z',
            'support_hours' => 'Lundi-vendredi, 09:00-18:00 CAT',
            'latitude' => '-4.3250',
            'longitude' => '15.3222',
            'email_support' => 'support-profile@example.test',
            'email_security' => 'security-profile@example.test',
        ]);
        CompanyProfile::create([
            'country_code' => 'ci',
            'locale' => 'fr',
            'company_name' => 'Dream Digital',
            'legal_name' => 'DREAM DIGITAL COTE D IVOIRE',
            'public_phone' => '+225 0101010101',
            'whatsapp_number' => '+225 999 999 999',
            'address_line' => 'Plateau, Abidjan',
            'city' => 'Abidjan',
            'country_label' => 'Cote d Ivoire',
            'latitude' => '5.3599',
            'longitude' => '-4.0083',
        ]);

        $this->get('/fr')->assertOk();

        $site = config('dream-digital.site');

        $this->assertSame('+243 000 000 000', data_get($site, 'contact.phone'));
        $this->assertSame('+243 999 999 999', data_get($site, 'contact.whatsapp'));
        $this->assertSame('Boulevard du 30 Juin, Kinshasa', data_get($site, 'company.address.line'));
        $this->assertSame('Kinshasa', data_get($site, 'company.address.city'));
        $this->assertSame('RDC', data_get($site, 'company.address.country'));
        $this->assertSame('CD/KIN/RCCM/26-B-001', data_get($site, 'company.registration_number'));
        $this->assertSame('A2600001Z', data_get($site, 'company.tax_id'));
        $this->assertSame('Lundi-vendredi, 09:00-18:00 CAT', data_get($site, 'contact.support_hours'));
        $this->assertSame('-4.3250', data_get($site, 'company.geo.latitude'));
        $this->assertSame('15.3222', data_get($site, 'company.geo.longitude'));
        $this->assertCount(2, data_get($site, 'company.entities'));
        $this->assertSame('Abidjan', data_get($site, 'company.entities.1.city'));
        $this->assertSame('5.3599', data_get($site, 'company.entities.1.latitude'));
    }

    public function test_country_route_uses_matching_country_entity_profile(): void
    {
        CompanyProfile::create([
            'country_code' => 'ci',
            'locale' => 'fr',
            'company_name' => 'Dream Digital CI',
            'legal_name' => 'DREAM DIGITAL COTE D IVOIRE',
            'public_phone' => '+225 0101010101',
            'whatsapp_number' => '+225 999 999 999',
            'city' => 'Abidjan',
            'country_label' => 'Cote d Ivoire',
            'latitude' => '5.3599',
            'longitude' => '-4.0083',
        ]);

        $this->get('/ci/fr')->assertOk();

        $this->assertSame('+225 0101010101', config('dream-digital.site.contact.phone'));
        $this->assertSame('Abidjan', config('dream-digital.site.company.address.city'));
    }

    public function test_contact_page_displays_country_entity_map_links(): void
    {
        foreach ([
            'cd' => ['city' => 'Kinshasa', 'country' => 'RDC', 'lat' => '-4.3250', 'lng' => '15.3222'],
            'ci' => ['city' => 'Abidjan', 'country' => 'Cote d Ivoire', 'lat' => '5.3599', 'lng' => '-4.0083'],
            'cg' => ['city' => 'Brazzaville', 'country' => 'Congo', 'lat' => '-4.2634', 'lng' => '15.2429'],
        ] as $countryCode => $entity) {
            CompanyProfile::create([
                'country_code' => $countryCode,
                'locale' => 'fr',
                'company_name' => 'Dream Digital',
                'legal_name' => 'DREAM DIGITAL',
                'public_phone' => '+243 000 000 000',
                'city' => $entity['city'],
                'country_label' => $entity['country'],
                'latitude' => $entity['lat'],
                'longitude' => $entity['lng'],
                'email_sales' => 'sales@dream-digital.info',
            ]);
        }

        $this->get('/fr/contact')
            ->assertOk()
            ->assertSee('https://www.google.com/maps?q=-4.3250,15.3222', false)
            ->assertSee('https://www.google.com/maps?q=5.3599,-4.0083', false)
            ->assertSee('https://www.google.com/maps?q=-4.2634,15.2429', false);
    }


    public function test_editor_cannot_manage_company_profile(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));

        $this->get(route('admin.company-profile.edit'))
            ->assertForbidden();
    }

    public function test_public_security_txt_uses_company_profile_contact(): void
    {
        CompanyProfile::create([
            'country_code' => 'cd',
            'locale' => 'fr',
            'company_name' => 'Dream Digital',
            'legal_name' => 'DREAM DIGITAL',
            'email_security' => 'security-profile@example.test',
        ]);

        $this->get('/.well-known/security.txt')
            ->assertOk()
            ->assertSee('Contact: mailto:security-profile@example.test', false);
    }

    public function test_company_profile_seed_does_not_read_business_identity_from_env(): void
    {
        putenv('DD_PUBLIC_PHONE=+243 SHOULD NOT LOAD');
        putenv('DD_WHATSAPP_NUMBER=+243 SHOULD NOT LOAD');
        putenv('DD_COMPANY_LEGAL_NAME=SHOULD NOT LOAD LTD');
        putenv('DD_PUBLIC_ADDRESS=SHOULD NOT LOAD ADDRESS');

        $this->beforeApplicationDestroyed(function (): void {
            putenv('DD_PUBLIC_PHONE');
            putenv('DD_WHATSAPP_NUMBER');
            putenv('DD_COMPANY_LEGAL_NAME');
            putenv('DD_PUBLIC_ADDRESS');
        });

        $this->seed(CompanyProfileSeeder::class);

        $this->assertDatabaseHas('company_profiles', [
            'country_code' => 'cd',
            'locale' => 'fr',
            'legal_name' => 'DREAM DIGITAL',
            'public_phone' => null,
            'whatsapp_number' => null,
            'address_line' => null,
        ]);
    }

    private function profilePayload(string $countryCode, string $locale, string $legalName, string $phone): array
    {
        $city = match ($countryCode) {
            'ci' => 'Abidjan',
            'cg' => 'Brazzaville',
            default => 'Kinshasa',
        };
        $countryLabelFr = match ($countryCode) {
            'ci' => 'Cote d Ivoire',
            'cg' => 'Congo',
            default => 'RDC',
        };
        $countryLabelEn = match ($countryCode) {
            'ci' => 'Ivory Coast',
            'cg' => 'Congo',
            default => 'DRC',
        };
        $latitude = match ($countryCode) {
            'ci' => '5.3599',
            'cg' => '-4.2634',
            default => '-4.3250',
        };
        $longitude = match ($countryCode) {
            'ci' => '-4.0083',
            'cg' => '15.2429',
            default => '15.3222',
        };
        $whatsapp = match ($countryCode) {
            'ci' => $locale === 'fr' ? '+225 999 999 999' : '+225 888 888 888',
            'cg' => $locale === 'fr' ? '+242 999 999 999' : '+242 888 888 888',
            default => $locale === 'fr' ? '+243 999 999 999' : '+243 888 888 888',
        };
        $registrationCityCode = match ($countryCode) {
            'ci' => 'ABJ',
            'cg' => 'BZV',
            default => 'KIN',
        };

        return [
            'country_code' => $countryCode,
            'locale' => $locale,
            'company_name' => 'Dream Digital',
            'legal_name' => $legalName,
            'public_phone' => $phone,
            'whatsapp_number' => $whatsapp,
            'address_line' => $countryCode === 'cd'
                ? ($locale === 'fr' ? 'Boulevard du 30 Juin, Kinshasa' : '30 Juin Boulevard, Kinshasa')
                : ($countryCode === 'ci' ? 'Plateau, Abidjan' : 'Centre-ville, Brazzaville'),
            'city' => $city,
            'country_label' => $locale === 'fr' ? $countryLabelFr : $countryLabelEn,
            'registration_number' => strtoupper($countryCode) . '/' . $registrationCityCode . '/RCCM/26-B-001',
            'tax_id' => $countryCode === 'cd' ? 'A2600001Z' : strtoupper($countryCode) . '-A2600001Z',
            'support_hours' => $locale === 'fr' ? 'Lundi-vendredi, 09:00-18:00 CAT' : 'Monday-Friday, 09:00-18:00 CAT',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'email_sales' => 'sales@dream-digital.info',
            'email_support' => 'support@dream-digital.info',
            'email_security' => 'security@dream-digital.info',
            'email_privacy' => 'privacy@dream-digital.info',
            'social_linkedin' => 'https://www.linkedin.com/company/dream-digital',
            'social_twitter' => 'https://x.com/dreamdigital',
            'social_github' => 'https://github.com/dream-digital',
            'og_image_path' => '/img/brand/logo-dd-horizontal.png',
            'legal_validated' => '1',
            'admin_password_rotated' => '1',
        ];
    }
}
