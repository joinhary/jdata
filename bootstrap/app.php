<?php

use App\Http\Middleware\SentinelAuth;
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

        // Đăng ký middleware ở đây
        $middleware->alias([
            'user' => App\Http\Middleware\UserMiddleware::class,
             'has_any_role' => App\Http\Middleware\HasAnyRole::class,
             
             'sentinel.auth' => \App\Http\Middleware\SentinelAuth::class,

        ]);
    

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
    
