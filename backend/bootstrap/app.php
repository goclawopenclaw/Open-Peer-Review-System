<?php

// Suppress PHP 8.5 PDO deprecation warnings
error_reporting(E_ALL & ~E_DEPRECATED);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Use stateless API approach for Sanctum tokens
        // $middleware->statefulApi();
        // Mock API is disabled - using real controllers
        // $middleware->append(\App\Http\Middleware\MockApiResponses::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
