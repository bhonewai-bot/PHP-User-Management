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

    public function permissionIdsForRole(int $roleId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT permission_id
            FROM role_permissions
            WHERE role_id = ?
            ORDER BY permission_id ASC
        ");

        $stmt->execute([$roleId]);
        return array_values(array_filter(array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN))));
    }

    public function replaceAll(int $roleId, array $permissionIds): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?")
            ->execute([$roleId]);

        $this->createMany($roleId, $permissionIds);
    }
}