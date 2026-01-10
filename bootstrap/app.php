<?php

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
    ->withCommands([
        \App\Support\Commands\MakeApiRequest::class,
        \App\Support\Commands\MakeApiController::class,
        \App\Support\Commands\MakeApiResource::class,
        \App\Support\Commands\MakeDomainModel::class,
        \App\Support\Commands\MakeDomainPolicy::class,
        \App\Support\Commands\MakeDomainService::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Support\Http\Middlewares\HandleLocalization::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
