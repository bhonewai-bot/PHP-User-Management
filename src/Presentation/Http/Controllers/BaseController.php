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
}