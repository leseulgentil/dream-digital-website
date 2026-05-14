<?php

namespace App\Http\Middleware;

use App\Services\CompanyProfileService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyCompanyProfile
{
    public function handle(Request $request, Closure $next): Response
    {
        app(CompanyProfileService::class)->applyToConfig($this->localeFrom($request));

        return $next($request);
    }

    private function localeFrom(Request $request): string
    {
        $segment = $request->segment(1);

        if (in_array($segment, ['fr', 'en'], true)) {
            return $segment;
        }

        $sessionLocale = $request->hasSession() ? $request->session()->get('locale') : null;

        return in_array($sessionLocale, ['fr', 'en'], true) ? $sessionLocale : 'fr';
    }
}
