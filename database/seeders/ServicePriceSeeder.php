<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Service;
use App\Models\ServicePrice;
use Illuminate\Database\Seeder;

class ServicePriceSeeder extends Seeder
{
    public function run(): void
    {
        $sms = Service::where('slug', 'sms')->first();
        $voice = Service::where('slug', 'voice')->first();

        if (!$sms || !$voice) {
            return;
        }

        $prices = [
            ['service' => $sms, 'country' => 'global', 'destination_country' => null, 'label_fr' => 'SMS international', 'label_en' => 'International SMS', 'price_usd' => 0.008900, 'unit' => 'sms'],
            ['service' => $sms, 'country' => 'cd', 'destination_country' => 'COD', 'label_fr' => 'SMS vers RDC', 'label_en' => 'SMS to DRC', 'price_usd' => 0.008900, 'unit' => 'sms'],
            ['service' => $sms, 'country' => 'cg', 'destination_country' => 'COG', 'label_fr' => 'SMS vers Congo', 'label_en' => 'SMS to Congo', 'price_usd' => 0.010500, 'unit' => 'sms'],
            ['service' => $sms, 'country' => 'ci', 'destination_country' => 'CIV', 'label_fr' => 'SMS vers Cote d Ivoire', 'label_en' => 'SMS to Ivory Coast', 'price_usd' => 0.009800, 'unit' => 'sms'],
            ['service' => $voice, 'country' => 'global', 'destination_country' => null, 'label_fr' => 'Minute voix internationale', 'label_en' => 'International voice minute', 'price_usd' => 0.028000, 'unit' => 'minute'],
        ];

        foreach ($prices as $price) {
            $country = Country::where('code', $price['country'])->first();

            if (!$country) {
                continue;
            }

            ServicePrice::updateOrCreate(
                [
                    'service_id' => $price['service']->id,
                    'country_id' => $country->id,
                    'destination_country' => $price['destination_country'],
                    'unit' => $price['unit'],
                ],
                [
                    'label_fr' => $price['label_fr'],
                    'label_en' => $price['label_en'],
                    'price_usd' => $price['price_usd'],
                    'price_local' => null,
                    'local_currency' => null,
                    'use_manual_local' => false,
                    'is_published' => true,
                ]
            );
        }
    }
}
