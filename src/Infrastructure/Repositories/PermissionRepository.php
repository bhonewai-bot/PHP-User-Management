<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use PDO;

final class PermissionRepository
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
}