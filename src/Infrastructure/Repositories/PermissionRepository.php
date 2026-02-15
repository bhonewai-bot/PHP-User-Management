<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use PDO;

class PermissionRepository
{
    public function __construct(private PDO $pdo) {}

    public function groupedByFeature(): array 
    {
        $rows = $this->pdo->query("
            SELECT 
                f.id as feature_id,
                f.name as feature_name,
                p.id as permission_id,
                p.name as permission_name
            FROM features f
            JOIN permissions p ON f.id = p.feature_id
            ORDER BY f.id ASC, p.id ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($rows as $row) {
            $featureId = (int)$row['feature_id'];
            if (!isset($grouped[$featureId])) {
                $grouped[$featureId] = [
                    'feature_id' => $featureId,
                    'feature_name' => $row['feature_name'],
                    'permissions' => []
                ];
            }

            $grouped[$featureId]['permissions'][] = [
                'id' => (int)$row['permission_id'],
                'name' => $row['permission_name']
            ];
        }

        return array_values($grouped);
    }

    public function keyForUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT CONCAT(f.name, '.', p.name) AS permission_key
            FROM admin_users u
            JOIN roles r ON r.id = u.role_id
            JOIN role_permissions rp ON rp.role_id = r.id
            JOIN permissions p ON p.id = rp.permission_id
            JOIN features f ON f.id = p.feature_id
            WHERE u.id = ?
            ORDER BY permission_key ASC
        ");
        $stmt->execute([$userId]);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $keys = [];

        foreach ($rows as $row) {
            $keys[] = (string)$row['permission_key'];
        }

        return array_values(array_unique($keys));
    }
}