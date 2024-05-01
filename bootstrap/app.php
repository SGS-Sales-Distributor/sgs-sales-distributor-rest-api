<?php

use App\Http\Middleware\JwtAuthMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (UnauthorizedHttpException $e) {
            return response()->json([
                'status' => $e->getStatusCode(), 
                'success' => false, 
                'message' => 'Authorization required.', 
                'error' => $e->getMessage(), 
                'resource' => null
            ], $e->getStatusCode());
        });
        $exceptions->renderable(function (NotFoundHttpException $e) {
            return response()->json([
                'status' => $e->getStatusCode(), 
                'success' => false, 
                'message' => 'Data not found.', 
                'error' => $e->getMessage(), 
                'resource' => null
            ], $e->getStatusCode());
        });
        $exceptions->renderable(function (MethodNotAllowedHttpException $e) {
            return response()->json([
                'status' => $e->getStatusCode(), 
                'success' => false, 
                'message' => 'Method not allowed.', 
                'error' => $e->getMessage(), 
                'resource' => null    
            ], $e->getStatusCode());
        });
        $exceptions->renderable(function (UnsupportedMediaTypeHttpException $e) {
            return response()->json([
                'status' => $e->getStatusCode(), 
                'success' => false, 
                'message' => 'Unsupported media type.', 
                'error' => $e->getMessage(), 
                'resource' => null
            ], $e->getStatusCode());
        });
        $exceptions->renderable(function (UnprocessableEntityHttpException $e) {
            return response()->json([
                'status' => $e->getStatusCode(), 
                'success' => false, 
                'message' => 'Unprocessable content.', 
                'error' => $e->getMessage(), 
                'resource' => null
            ], $e->getStatusCode());
        });
        $exceptions->renderable(function (TooManyRequestsHttpException $e) {
            return response()->json([
                'status' => $e->getStatusCode(), 
                'success' => false, 
                'message' => 'Unprocessable content.', 
                'error' => $e->getMessage(), 
                'resource' => null
            ], $e->getStatusCode());
        });
        $exceptions->renderable(function (ServiceUnavailableHttpException $e) {
            return response()->json([
                'status' => $e->getStatusCode(), 
                'success' => false, 
                'message' => 'Unprocessable content.', 
                'error' => $e->getMessage(), 
                'resource' => null
            ], $e->getStatusCode());
        });
        $exceptions->renderable(function (\Error $e) {
            return response()->json([
                'status' => 500, 
                'success' => false, 
                'message' => 'Internal Server Error.', 
                'error' => $e->getMessage(), 
                'resource' => null
            ], 500);
        });
        $exceptions->renderable(function (\Exception $e) {
            return response()->json([
                'status' => 500, 
                'success' => false, 
                'message' => 'Internal Server Error.', 
                'error' => $e->getMessage(), 
                'resource' => null
            ], 500);
        });
    })->create();
