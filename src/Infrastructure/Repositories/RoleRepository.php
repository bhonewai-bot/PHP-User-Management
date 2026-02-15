<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use PDO;

final class RoleRepository
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        $smst = $this->pdo->query("SELECT * FROM roles ORDER BY id ASC");
        return $smst->fetchAll(PDO::FETCH_ASSOC);
    }
}