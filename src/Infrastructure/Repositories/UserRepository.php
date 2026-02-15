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
                (:name, :username, :role_id, :phone, :email, :address, :password, :gender, :is_active)
        ");

        $stmt->execute([
            ':name' => $data['name'],
            ':username' => $data['username'],
            ':role_id' => $data['role_id'],
            ':phone' => $data['phone'],
            ':email' => $data['email'],
            ':address' => $data['address'],
            ':password' => $data['password'],
            ':gender' => $data['gender'],
            ':is_active' => $data['is_active']
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
}