<?php

namespace Tests\Feature;

use App\Models\ContactLead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactLeadTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_contact_form_creates_lead(): void
    {
        $this->post('/fr/contact', [
            'full_name' => 'Grace Mbala',
            'company_name' => 'Acme Fintech',
            'email' => 'grace@example.test',
            'phone' => '+243 810 000 000',
            'service_interest' => 'sms',
            'monthly_volume' => '100k-500k',
            'message' => 'Nous voulons tester les routes OTP RDC et CI.',
            'website' => '',
        ])->assertRedirect('/fr/contact')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('contact_leads', [
            'locale' => 'fr',
            'country_code' => null,
            'full_name' => 'Grace Mbala',
            'company_name' => 'Acme Fintech',
            'email' => 'grace@example.test',
            'service_interest' => 'sms',
            'monthly_volume' => '100k-500k',
            'status' => ContactLead::STATUS_NEW,
        ]);
    }

    public function test_honeypot_contact_submission_is_accepted_without_creating_lead(): void
    {
        $this->post('/fr/contact', [
            'full_name' => 'Bot',
            'company_name' => 'Crawler',
            'email' => 'bot@example.test',
            'service_interest' => 'voice',
            'message' => 'Spam',
            'website' => 'https://spam.example',
        ])->assertRedirect('/fr/contact')
            ->assertSessionHas('status');

        $this->assertDatabaseCount('contact_leads', 0);
    }

    public function test_admin_can_list_contact_leads(): void
    {
        ContactLead::create([
            'locale' => 'fr',
            'full_name' => 'Grace Mbala',
            'company_name' => 'Acme Fintech',
            'email' => 'grace@example.test',
            'service_interest' => 'sms',
            'message' => 'Besoin de pricing SMS.',
        ]);

        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));

        $this->get(route('admin.contact-leads.index'))
            ->assertOk()
            ->assertSee('Leads')
            ->assertSee('Grace Mbala')
            ->assertSee('Acme Fintech')
            ->assertSee('sms');
    }
}
