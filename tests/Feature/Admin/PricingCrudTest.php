<?php

namespace Tests\Feature\Admin;

use App\Models\Country;
use App\Models\Service;
use App\Models\ServicePrice;
use App\Models\User;
use Database\Seeders\CountrySeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([CountrySeeder::class, ServiceSeeder::class]);
        $this->actingAs(User::factory()->create());
    }

    public function test_guest_cannot_access_pricing_admin(): void
    {
        auth()->logout();
        $this->get(route('admin.pricing.index'))
            ->assertRedirect(route('login'));
    }

    public function test_index_renders_with_filters(): void
    {
        $service = Service::firstOrFail();
        $country = Country::firstOrFail();
        ServicePrice::create($this->validPayload($service->id, $country->id));

        $this->get(route('admin.pricing.index'))
            ->assertOk()
            ->assertSee('Pricing');

        $this->get(route('admin.pricing.index', ['service_id' => $service->id]))
            ->assertOk();
    }

    public function test_create_form_renders(): void
    {
        $this->get(route('admin.pricing.create'))
            ->assertOk()
            ->assertSee('Nouveau tarif');
    }

    public function test_store_creates_service_price(): void
    {
        $service = Service::firstOrFail();
        $country = Country::where('code', 'cd')->firstOrFail();

        $this->post(route('admin.pricing.store'), $this->validPayload($service->id, $country->id))
            ->assertRedirect(route('admin.pricing.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('service_prices', [
            'service_id' => $service->id,
            'country_id' => $country->id,
            'destination_country' => 'FR',
            'label_fr' => 'SMS marketing France',
        ]);
    }

    public function test_store_rejects_invalid_payload(): void
    {
        $this->from(route('admin.pricing.create'))
            ->post(route('admin.pricing.store'), [
                'service_id' => 99999,
                'country_id' => 99999,
                'label_fr' => '',
                'label_en' => '',
                'price_usd' => 'not-a-number',
                'unit' => '',
            ])
            ->assertRedirect(route('admin.pricing.create'))
            ->assertSessionHasErrors(['service_id', 'country_id', 'label_fr', 'label_en', 'price_usd', 'unit']);
    }

    public function test_update_modifies_service_price(): void
    {
        $price = ServicePrice::create($this->validPayload(
            Service::firstOrFail()->id,
            Country::firstOrFail()->id,
        ));

        $newPayload = $this->validPayload($price->service_id, $price->country_id);
        $newPayload['label_fr'] = 'Tarif mis a jour';
        $newPayload['price_usd'] = 0.012;

        $this->put(route('admin.pricing.update', $price), $newPayload)
            ->assertRedirect(route('admin.pricing.index'));

        $price->refresh();
        $this->assertSame('Tarif mis a jour', $price->label_fr);
        $this->assertSame('0.012000', (string) $price->price_usd);
    }

    public function test_destroy_removes_service_price(): void
    {
        $price = ServicePrice::create($this->validPayload(
            Service::firstOrFail()->id,
            Country::firstOrFail()->id,
        ));

        $this->delete(route('admin.pricing.destroy', $price))
            ->assertRedirect(route('admin.pricing.index'));

        $this->assertDatabaseMissing('service_prices', ['id' => $price->id]);
    }

    public function test_destination_country_is_uppercased(): void
    {
        $service = Service::firstOrFail();
        $country = Country::firstOrFail();
        $payload = $this->validPayload($service->id, $country->id);
        $payload['destination_country'] = 'fr';
        $payload['local_currency'] = 'eur';

        $this->post(route('admin.pricing.store'), $payload)->assertRedirect();

        $this->assertDatabaseHas('service_prices', [
            'destination_country' => 'FR',
            'local_currency' => 'EUR',
        ]);
    }

    private function validPayload(int $serviceId, int $countryId): array
    {
        return [
            'service_id' => $serviceId,
            'country_id' => $countryId,
            'destination_country' => 'FR',
            'label_fr' => 'SMS marketing France',
            'label_en' => 'France marketing SMS',
            'price_usd' => 0.0089,
            'price_local' => 0.0081,
            'local_currency' => 'EUR',
            'unit' => 'SMS',
            'use_manual_local' => false,
            'is_published' => true,
        ];
    }
}
