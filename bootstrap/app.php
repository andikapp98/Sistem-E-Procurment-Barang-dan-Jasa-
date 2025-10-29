<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\LogUserActivity::class,
        ]);

        // Middleware aliases
        $middleware->alias([
            'redirect.role' => \App\Http\Middleware\RedirectBasedOnRole::class,
            'log.activity' => \App\Http\Middleware\LogUserActivity::class,
        ]);
        
        // Validate CSRF tokens but don't throw on mismatch for certain routes
        $middleware->validateCsrfTokens(except: [
            // Add any routes that should be excluded from CSRF verification
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
