<?php

use App\Routing\RouteRegistrarRunner;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$runner = new RouteRegistrarRunner;
$test = 1;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: fn (\Illuminate\Contracts\Routing\Registrar $router) => $runner($router),
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(fn (Middleware $middleware) => $middleware->web(['throttle:global']))
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (DomainException $exception) {
            flash()->alert($exception->getMessage());
        });
    })
    ->create();
