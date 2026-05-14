<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServicePriceRequest;
use App\Models\Country;
use App\Models\Service;
use App\Models\ServicePrice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function index(Request $request): View
    {
        $query = ServicePrice::with(['service', 'country', 'updatedBy'])
            ->orderByDesc('updated_at');

        if ($serviceFilter = $request->integer('service_id')) {
            $query->where('service_id', $serviceFilter);
        }
        if ($countryFilter = $request->integer('country_id')) {
            $query->where('country_id', $countryFilter);
        }
        if ($request->has('published') && $request->input('published') !== '') {
            $query->where('is_published', $request->boolean('published'));
        }

        return view('admin.pricing.index', [
            'prices' => $query->paginate(20)->withQueryString(),
            'services' => Service::active()->get(),
            'countries' => Country::active()->orderBy('sort_order')->get(),
            'filters' => [
                'service_id' => $serviceFilter,
                'country_id' => $countryFilter,
                'published' => $request->input('published', ''),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.pricing.create', [
            'price' => new ServicePrice(['is_published' => true, 'unit' => 'SMS']),
            'services' => Service::active()->get(),
            'countries' => Country::active()->orderBy('sort_order')->get(),
        ]);
    }

    public function store(ServicePriceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['updated_by'] = optional($request->user())->id;

        $price = ServicePrice::create($data);

        return redirect()
            ->route('admin.pricing.index')
            ->with('status', "Tarif cree : {$price->label_fr}");
    }

    public function edit(ServicePrice $pricing): View
    {
        return view('admin.pricing.edit', [
            'price' => $pricing,
            'services' => Service::active()->get(),
            'countries' => Country::active()->orderBy('sort_order')->get(),
        ]);
    }

    public function update(ServicePriceRequest $request, ServicePrice $pricing): RedirectResponse
    {
        $data = $request->validated();
        $data['updated_by'] = optional($request->user())->id;

        $pricing->update($data);

        return redirect()
            ->route('admin.pricing.index')
            ->with('status', "Tarif mis a jour : {$pricing->label_fr}");
    }

    public function destroy(ServicePrice $pricing): RedirectResponse
    {
        $label = $pricing->label_fr;
        $pricing->delete();

        return redirect()
            ->route('admin.pricing.index')
            ->with('status', "Tarif supprime : {$label}");
    }
}
