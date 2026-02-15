<?php

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';;

use App\Infrastructure\DB\PdoConnection;
use App\Presentation\Extensions\PermissionChecker;
use App\Presentation\Http\Routing\Router;

$pdo = PdoConnection::connect($_ENV);

$router = new Router();

$registerRoutes = require __DIR__ . '/../config/routes.php';
$registerRoutes($router);

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router->dispatch($method, $path, [
    'pdo' => $pdo,
    'hasPermission' => fn(string $requiredKey): bool => PermissionChecker::hasPermission($requiredKey)
]);