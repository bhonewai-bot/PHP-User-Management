<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use PDO;

class RolePermissionRepository
{
    public function __construct(private PDO $pdo) {}

    public function createMany(int $roleId, array $permissionIds): void
    {
        if (count($permissionIds) === 0) return;

        $stmt = $this->pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
        foreach ($permissionIds as $permissionId) {
            $stmt->execute([$roleId, $permissionId]);
        }
    }
}