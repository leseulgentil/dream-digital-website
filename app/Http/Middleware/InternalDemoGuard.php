<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalDemoGuard
{
  /**
   * Keep inherited template/demo surfaces out of the public production site.
   */
  public function handle(Request $request, Closure $next): Response
  {
    abort_unless(
      app()->environment('local', 'staging') || config('app.debug'),
      404
    );

    return $next($request);
  }
}
