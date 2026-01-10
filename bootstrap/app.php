<?php

use App\Support\Enums\ResponseMessageEnum;
use App\Support\Http\Responses\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

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
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    __(ResponseMessageEnum::NOT_FOUND->value),
                    Response::HTTP_NOT_FOUND
                );
            }
        });
        $exceptions->render(function (MethodNotAllowedException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    __(ResponseMessageEnum::METHOD_NOT_ALLOWED->value),
                    Response::HTTP_METHOD_NOT_ALLOWED
                );
            }
        });
        $exceptions->render(function (UnauthorizedException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    __(ResponseMessageEnum::UNAUTHORIZED->value),
                    Response::HTTP_FORBIDDEN
                );
            }
        });
    })->create();
