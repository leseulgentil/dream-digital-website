<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'code' => 'global',
                'name_fr' => 'International',
                'name_en' => 'International',
                'default_currency_code' => 'USD',
                'secondary_currency_code' => null,
                'show_dual_currency' => false,
                'default_locale' => 'fr',
                'available_locales' => ['fr', 'en'],
                'phone_prefix' => '+1',
                'sales_email' => 'sales@dream-digital.info',
                'sales_phone' => null,
                'office_address' => null,
                'flag_emoji' => 'GLOBAL',
                'is_global' => true,
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'code' => 'cd',
                'name_fr' => 'Republique Democratique du Congo',
                'name_en' => 'Democratic Republic of Congo',
                'default_currency_code' => 'USD',
                'secondary_currency_code' => 'CDF',
                'show_dual_currency' => true,
                'default_locale' => 'fr',
                'available_locales' => ['fr', 'en'],
                'phone_prefix' => '+243',
                'sales_email' => 'sales.cd@dream-digital.info',
                'sales_phone' => null,
                'office_address' => 'Kinshasa, RDC',
                'flag_emoji' => 'CD',
                'is_global' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'cg',
                'name_fr' => 'Republique du Congo',
                'name_en' => 'Republic of Congo',
                'default_currency_code' => 'XAF',
                'secondary_currency_code' => null,
                'show_dual_currency' => false,
                'default_locale' => 'fr',
                'available_locales' => ['fr', 'en'],
                'phone_prefix' => '+242',
                'sales_email' => 'sales.cg@dream-digital.info',
                'sales_phone' => null,
                'office_address' => 'Brazzaville, Congo',
                'flag_emoji' => 'CG',
                'is_global' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'ci',
                'name_fr' => 'Cote d Ivoire',
                'name_en' => 'Ivory Coast',
                'default_currency_code' => 'XOF',
                'secondary_currency_code' => null,
                'show_dual_currency' => false,
                'default_locale' => 'fr',
                'available_locales' => ['fr', 'en'],
                'phone_prefix' => '+225',
                'sales_email' => 'sales.ci@dream-digital.info',
                'sales_phone' => null,
                'office_address' => 'Abidjan, Cote d Ivoire',
                'flag_emoji' => 'CI',
                'is_global' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($countries as $data) {
            Country::updateOrCreate(['code' => $data['code']], $data);
        }
    }
}
