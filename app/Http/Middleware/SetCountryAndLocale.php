<?php

namespace App\Http\Middleware;

use App\Models\Country;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetCountryAndLocale
{
    private const SUPPORTED_LOCALES = ['fr', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $segments = $request->segments();
        $country = null;
        $locale = null;

        if (in_array($segments[0] ?? null, self::SUPPORTED_LOCALES, true)) {
            $locale = $segments[0];
            $country = $this->globalCountry();
        } elseif ($this->isSupportedCountry($segments[0] ?? null)) {
            $country = $this->countryByCode($segments[0]);
            $locale = in_array($segments[1] ?? null, self::SUPPORTED_LOCALES, true)
                ? $segments[1]
                : ($country->default_locale ?? 'fr');

            if ($country && !in_array($locale, $country->available_locales ?? ['fr', 'en'], true)) {
                $locale = $country->default_locale;
            }
        }

        if (!$locale) {
            return $next($request);
        }

        App::setLocale($locale);
        session()->put('locale', $locale);

        app()->instance('current_country', $country);
        app()->instance('current_locale', $locale);

        View::share('currentCountry', $country);
        View::share('currentLocale', $locale);

        return $next($request);
    }

    private function globalCountry(): ?Country
    {
        if (!Schema::hasTable('countries')) {
            return null;
        }

        return Country::active()->where('is_global', true)->first();
    }

    private function countryByCode(?string $code): ?Country
    {
        if (!$code || !Schema::hasTable('countries')) {
            return null;
        }

        return Country::active()
            ->where('is_global', false)
            ->where('code', $code)
            ->first();
    }

    private function isSupportedCountry(?string $code): bool
    {
        if (!$code) {
            return false;
        }

        if (!Schema::hasTable('countries')) {
            return in_array($code, ['cd', 'cg', 'ci'], true);
        }

        return Country::active()
            ->where('is_global', false)
            ->where('code', $code)
            ->exists();
    }
}
