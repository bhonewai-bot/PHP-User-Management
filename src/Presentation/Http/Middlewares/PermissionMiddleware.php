<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middlewares;

use App\Infrastructure\Repositories\PermissionRepository;
use PDO;

class PermissionMiddleware
{
    public function __construct(private string $requiredKey) {}

    public function __invoke(array $ctx): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $pdo = $ctx['pdo'];
        $userId = (int)$_SESSION['user_id'];

        $cacheKey = 'permission_keys_' . $userId;

        if (!isset($_SESSION[$cacheKey]) || !is_array($_SESSION[$cacheKey])) {
            $permissionRepo = new PermissionRepository($pdo);
            $_SESSION[$cacheKey] = $permissionRepo->keyForUserId($userId);
        }

        $keys = $_SESSION[$cacheKey];

        if (!in_array($this->requiredKey, $keys, true)) {
            http_response_code(403);
            echo '403 Forbidden';
            exit;
        }
    }
}