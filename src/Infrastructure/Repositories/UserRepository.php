<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use PDO;

class UserRepository
{
    public function __construct(private PDO $pdo) {}

    public function allWithRole(): array
    {
        $stmt = $this->pdo->query("
            SELECT 
                u.id,
                u.name,
                u.username,
                u.email,
                u.phone,
                u.is_active,
                r.id as role_id,
                r.name as role_name
            FROM admin_users u
            JOIN roles r ON u.role_id = r.id
            ORDER BY u.id ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): int 
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO admin_users 
                (name, username, role_id, phone, email, address, password, gender, is_active)
            VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['name'],
            $data['username'],
            $data['role_id'],
            $data['phone'],
            $data['email'],
            $data['address'],
            $data['password'],
            $data['gender'],
            $data['is_active']
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function toggleActive(int $userId): void 
    {
        $stmt = $this->pdo->prepare("
            UPDATE admin_users 
            SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
    }

    public function find(int $userId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, name, username, role_id, phone, email, address, gender, is_active
            FROM admin_users
            WHERE id = ?
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function update(int $userId, array $data): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE admin_users
            SET name = ?, username = ?, role_id = ?, phone = ?, email = ?, address = ?, gender = ?, is_active = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $data['name'],
            $data['username'],
            $data['role_id'],
            $data['phone'],
            $data['email'],
            $data['address'],
            $data['gender'],
            $data['is_active'],
            $userId
        ]);
    }
}