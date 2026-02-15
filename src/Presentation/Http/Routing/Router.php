<?php

declare(strict_types=1);

namespace App\Presentation\Http\Routing;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->map('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->map('POST', $path, $handler, $middleware);
    }

    private function map(string $method, string $path, array $handler, array $middleware): void
    {
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function dispatch(string $method, string $path, array $ctx = []): void
    {
        $route = $this->routes[$method][$path] ?? null;
        
        if ($route === null) {
            http_response_code(404);
            echo 'Not Found';
            return;
        }

        foreach ($route['middleware'] as $mw) {
            $mw->__invoke($ctx);
        }

        [$class, $action] = $route['handler'];
        $controller = new $class($ctx);
        $controller->$action();
    }
}