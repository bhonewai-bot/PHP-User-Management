<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

abstract class BaseController
{
    public function __construct(protected array $ctx) {}

    protected function json(array $data, int $status = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
    }

    protected function redirect(string $path): void
    {
        header("Location: {$path}");
        exit;
    }

    protected function view(string $template, array $data = [], string $title = 'User Management'): void
    {
        $ctx = $this->ctx;

        if (!isset($ctx['hasPermission']) || !is_callable($ctx['hasPermission'])) {
            $ctx['hasPermission'] = static fn(string $requiredKey): bool => false;
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include __DIR__ . '/../Views/' . $template . '.php';
        $content = (string)ob_get_clean();

        include __DIR__ . '/../Views/layout.php';
    }
}
