<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServicePrice;
use Illuminate\Support\Facades\Schema;

class Sprint1TestController extends Controller
{
    public function global()
    {
        return $this->render('Global');
    }

    public function country()
    {
        $country = app()->bound('current_country') ? app('current_country') : null;

        return $this->render('Country: ' . ($country->name ?? 'Unknown'));
    }

    public function test()
    {
        return $this->render('Test page');
    }

    private function render(string $mode)
    {
        $services = Schema::hasTable('services') ? Service::active()->get() : collect();
        $price = Schema::hasTable('service_prices')
            ? ServicePrice::published()->first()
            : null;

        $price ??= new ServicePrice(['price_usd' => 0.008900, 'use_manual_local' => false]);

        return view('sprint1.test-page', [
            'mode' => $mode,
            'services' => $services,
            'price' => $price,
        ]);
    }
}
