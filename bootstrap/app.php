<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
         $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'verifyAccessToken' => \App\Http\Middleware\VerifyAccessToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    
        $exceptions->renderable(function (UnauthorizedException $e, $request) {
            return response()->json([
                'error' => 'You do not have the required authorization.'
            ], 403);
        });

        $exceptions->renderable(function (RouteNotFoundException $e, $request) {
            if (str_contains($request->getRequestUri(), '/api/')) {
                return response()->json([
                    'error' => 'Unauthenticated. Please login first.',
                ], 401);
            }
        });

        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if (str_contains($request->getRequestUri(), '/api/')) {
                return response()->json([
                    'error' => 'Unauthenticated. Please login first.',
                ], 401);
            }
        });
    })->create();
