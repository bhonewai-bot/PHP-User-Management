<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use PDO;

class RoleRepository
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM roles ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(string $name): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO roles (name) VALUES (?)");
        $stmt->execute([$name]);
        return (int)$this->pdo->lastInsertId();
    }

    public function find(int $roleId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, name FROM roles WHERE id = ? LIMIT 1");
        $stmt->execute([$roleId]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        return $role ?: null;
    }

    public function updateName(int $roleId, string $name): void
    {
        $stmt = $this->pdo->prepare("UPDATE roles SET name = ? WHERE id = ?");
        $stmt->execute([$name, $roleId]);
    }
}