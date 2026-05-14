<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        if ($request->is('admin*')) {
            $response->headers->set('Cache-Control', 'no-store, max-age=0, private');
            $response->headers->set('Pragma', 'no-cache');
        }

        if (filter_var(config('dream-digital.security.csp.enabled', true), FILTER_VALIDATE_BOOLEAN)) {
            $response->headers->set(
                filter_var(config('dream-digital.security.csp.report_only', true), FILTER_VALIDATE_BOOLEAN)
                    ? 'Content-Security-Policy-Report-Only'
                    : 'Content-Security-Policy',
                $this->contentSecurityPolicy()
            );
        }

        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    private function contentSecurityPolicy(): string
    {
        $directives = [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'self'",
            "img-src 'self' data: https:",
            "font-src 'self' data: https://fonts.gstatic.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "script-src 'self' 'unsafe-inline'",
            "connect-src 'self'",
            "form-action 'self'",
            'upgrade-insecure-requests',
        ];

        if ($reportUri = config('dream-digital.security.csp.report_uri')) {
            $directives[] = 'report-uri ' . $reportUri;
        }

        return implode('; ', $directives);
    }
}
