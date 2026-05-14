<?php

namespace Tests\Feature\Admin;

use App\Models\CompanyProfile;
use App\Models\User;
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
            ->assertSee('Profil FR')
            ->assertSee('Profil EN');

        $this->assertDatabaseHas('company_profiles', ['locale' => 'fr']);
        $this->assertDatabaseHas('company_profiles', ['locale' => 'en']);
    }

    public function test_owner_can_update_bilingual_company_profile(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_OWNER]));

        $this->put(route('admin.company-profile.update'), [
            'profiles' => [
                'fr' => $this->profilePayload('fr', 'DREAM DIGITAL', '+243 000 000 000'),
                'en' => $this->profilePayload('en', 'DREAM DIGITAL LTD', '+243 111 111 111'),
            ],
        ])->assertRedirect(route('admin.company-profile.edit'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('company_profiles', [
            'locale' => 'fr',
            'legal_name' => 'DREAM DIGITAL',
            'public_phone' => '+243 000 000 000',
            'legal_validated' => true,
            'admin_password_rotated' => true,
        ]);
        $this->assertDatabaseHas('company_profiles', [
            'locale' => 'en',
            'legal_name' => 'DREAM DIGITAL LTD',
            'public_phone' => '+243 111 111 111',
        ]);
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
            'locale' => 'fr',
            'company_name' => 'Dream Digital',
            'legal_name' => 'DREAM DIGITAL',
            'email_security' => 'security-profile@example.test',
        ]);

        $this->get('/.well-known/security.txt')
            ->assertOk()
            ->assertSee('Contact: mailto:security-profile@example.test', false);
    }

    private function profilePayload(string $locale, string $legalName, string $phone): array
    {
        return [
            'locale' => $locale,
            'company_name' => 'Dream Digital',
            'legal_name' => $legalName,
            'public_phone' => $phone,
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
