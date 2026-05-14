<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureAdminAccess;
use App\Http\Middleware\EnsureAdminRole;
use App\Http\Middleware\InternalDemoGuard;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\RequestId;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetCountryAndLocale;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            RequestId::class,
            SecurityHeaders::class,
            LocaleMiddleware::class,
            SetCountryAndLocale::class,
        ]);
        $middleware->alias([
            'admin.access' => EnsureAdminAccess::class,
            'admin.role' => EnsureAdminRole::class,
            'internal.demo' => InternalDemoGuard::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
