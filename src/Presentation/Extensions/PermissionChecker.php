<?php

declare(strict_types=1);

namespace App\Presentation\Extensions;

class PermissionChecker
{
    public static function hasPermission(string $requiredKey): bool
    {
        if (!isset($_SESSION['user_id'])) return false;

        $userId = (int)$_SESSION['user_id'];

        $cacheKey = 'permission_keys_' . $userId;

        $keys = $_SESSION[$cacheKey] ?? [];
        if (!is_array($keys)) return false;

        return in_array($requiredKey, $keys, true);
    }
}