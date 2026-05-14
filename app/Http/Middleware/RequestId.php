<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = (string) config('dream-digital.observability.request_id_header', 'X-Request-Id');
        $incoming = (string) $request->headers->get($header, '');
        $requestId = preg_match('/^[A-Za-z0-9_.:-]{8,128}$/', $incoming) ? $incoming : (string) Str::uuid();
        $startedAt = microtime(true);

        $request->attributes->set('request_id', $requestId);
        Log::withContext(['request_id' => $requestId]);

        /** @var Response $response */
        $response = $next($request);
        $response->headers->set($header, $requestId);

        $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
        $thresholdMs = (int) config('dream-digital.observability.slow_request_ms', 1000);

        if ($thresholdMs > 0 && $durationMs >= $thresholdMs) {
            Log::warning('Slow web request', [
                'method' => $request->method(),
                'path' => $request->path(),
                'status' => $response->getStatusCode(),
                'duration_ms' => $durationMs,
            ]);
        }

        return $response;
    }
}
