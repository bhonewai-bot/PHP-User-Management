<?php

declare(strict_types=1);

use App\Presentation\Http\Controllers\AuthController;
use App\Presentation\Http\Controllers\HealthController;
use App\Presentation\Http\Middlewares\AuthMiddleware;
use App\Presentation\Http\Routing\Router;

return function (Router $router): void {
    $auth = new AuthMiddleware();

    // Auth
    $router->get('/', [AuthController::class, 'showLogin']);
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->post('/logout', [AuthController::class, 'logout'], [$auth]);

    $router->get('/health', [HealthController::class, 'index'], [$auth]);
};