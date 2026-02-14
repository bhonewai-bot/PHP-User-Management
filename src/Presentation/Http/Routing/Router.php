<?php

declare(strict_types=1);

namespace App\Presentation\Http\Routing;

final class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $path, array $ctx = []): void
    {
        $handler = $this->routes[$method][$path] ?? null;
        if ($handler === null) {
            http_response_code(404);
            echo 'Not Found';
            return;
        }

        [$class, $action] = $handler;
        $controller = new $class($ctx);
        $controller->$action();
    }
}