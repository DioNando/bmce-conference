<?php

use App\Exceptions\Handler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middlewares\RoleMiddleware;
use App\Http\Middlewares\SetLocaleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(
            [
                'role' => RoleMiddleware::class,
                'locale' => SetLocaleMiddleware::class,
            ]
        );

        // Appliquer le middleware SetLocaleMiddleware à toutes les routes web
        $middleware->web(append: [SetLocaleMiddleware::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Enregistrer le gestionnaire personnalisé
        // $exceptions->handler(Handler::class);

        // Vous pouvez définir des fonctions reportable et renderable supplémentaires si nécessaire
        // $exceptions->reportable(function (\Throwable $e) {
        //    // Logique supplémentaire de rapport d'erreurs
        // });
    })->create();
