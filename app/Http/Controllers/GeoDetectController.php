<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class GeoDetectController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        if ($pref = $request->cookie('dd_country_pref')) {
            if ($country = $this->countryByCode($pref)) {
                return redirect("/{$country->code}/{$country->default_locale}");
            }
        }

        $detected = strtolower((string) (
            $request->query('country')
            ?? $request->headers->get('X-DreamDigital-Country')
            ?? ''
        ));

        if ($country = $this->countryByCode($detected)) {
            return redirect("/{$country->code}/{$country->default_locale}")
                ->cookie('dd_country_pref', $country->code, 60 * 24 * 90);
        }

        return redirect('/fr');
    }

    public function resetToGlobal(Request $request): RedirectResponse
    {
        $locale = $request->query('locale', 'fr');
        $locale = in_array($locale, ['fr', 'en'], true) ? $locale : 'fr';

        return redirect("/{$locale}")->withoutCookie('dd_country_pref');
    }

    private function countryByCode(?string $code): ?object
    {
        if (!$code || !in_array($code, ['cd', 'cg', 'ci'], true)) {
            return null;
        }

        if (!Schema::hasTable('countries')) {
            return (object) [
                'code' => $code,
                'default_locale' => 'fr',
            ];
        }

        return Country::active()
            ->where('is_global', false)
            ->where('code', $code)
            ->first();
    }
}
