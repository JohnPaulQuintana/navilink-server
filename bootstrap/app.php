<?php

use App\Exceptions\ApiException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
// use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (Throwable $e, $request) {

            // only API routes
            if (! $request->is('api/*')) {
                return null;
            }

            // ✅ Validation errors
            if ($e instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            // ✅ Custom API Exception (ONLY if you use it)
            if ($e instanceof ApiException) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->getErrors(),
                ], $e->getStatus());
            }

            // ✅ HTTP exceptions
            if (method_exists($e, 'getStatusCode')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], $e->getStatusCode());
            }

            // ❌ fallback (unexpected errors)
            return response()->json([
                'success' => false,
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Server Error',
            ], 500);

        });

    })->create();
