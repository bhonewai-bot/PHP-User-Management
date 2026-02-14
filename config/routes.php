<?php

declare(strict_types=1);

use App\Presentation\Http\Controllers\HealthController;
use App\Presentation\Http\Routing\Router;

return function (Router $router): void {
    $router->get('/health', [HealthController::class, 'index']);
};