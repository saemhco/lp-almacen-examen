<?php

use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureProyectoListo;
use App\Traits\ApiResponseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(EnsureProyectoListo::class);

        $middleware->alias([
            'permission' => CheckPermission::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            return $request->is('api/*') ? null : route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $responder = new class
        {
            use ApiResponseTrait;
        };

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($responder) {
            if ($request->is('api/*')) {
                return $responder->unauthenticatedResponse();
            }
        });

        $exceptions->render(function (NotFoundHttpException|ModelNotFoundException $e, Request $request) use ($responder) {
            if ($request->is('api/*')) {
                return $responder->notFoundResponse('Resource not found');
            }
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) use ($responder) {
            if ($request->is('api/*')) {
                return $responder->errorResponse($e->getMessage() ?: 'Error', $e->getStatusCode());
            }
        });

        $exceptions->render(function (\Throwable $e, Request $request) use ($responder) {
            if ($request->is('api/*')) {
                return $responder->serverErrorResponse($e);
            }
        });
    })->create();
