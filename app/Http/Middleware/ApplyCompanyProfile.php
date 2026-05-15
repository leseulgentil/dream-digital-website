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
        foreach ([$request->segment(1), $request->segment(2)] as $segment) {
            if (in_array($segment, ['fr', 'en'], true)) {
                return $segment;
            }
        }

        if (app()->bound('current_locale') && in_array(app('current_locale'), ['fr', 'en'], true)) {
            return app('current_locale');
        }

        $sessionLocale = $request->hasSession() ? $request->session()->get('locale') : null;

        return in_array($sessionLocale, ['fr', 'en'], true) ? $sessionLocale : 'fr';
    }
}
