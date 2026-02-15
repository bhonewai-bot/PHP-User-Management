<?php

declare(strict_types=1);

namespace App\Application\UseCases\Roles;

use App\Infrastructure\Repositories\RolePermissionRepository;
use App\Infrastructure\Repositories\RoleRepository;
use PDO;

class UpdateRoleUseCase
{
    public function __construct(private PDO $pdo) {}

    public function execute(int $roleId, string $name, array $permissionIds): void
    {
        $name = trim($name);

        if ($roleId <= 0) throw new \InvalidArgumentException("Invalid role id");
        if ($name === '') throw new \InvalidArgumentException("Role name is required");

        $permissionIds = array_values(array_filter(array_map('intval', $permissionIds)));

        $roleRepo = new RoleRepository($this->pdo);
        $rolePermissionRepo = new RolePermissionRepository($this->pdo);

        $this->pdo->beginTransaction();

        try {
            $roleRepo->updateName($roleId, $name);
            $rolePermissionRepo->replaceAll($roleId, $permissionIds);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}