<?php

declare(strict_types=1);    

namespace App\Presentation\Http\Middlewares;

final class AuthMiddleware
{
    public function __invoke(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }
}