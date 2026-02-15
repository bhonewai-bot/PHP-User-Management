<?php

declare(strict_types=1);

namespace App\Application\UseCases\Roles;

use App\Infrastructure\Repositories\RolePermissionRepository;
use App\Infrastructure\Repositories\RoleRepository;
use PDO;

class CreateRoleUseCase
{
    public function __construct(private PDO $pdo) {}

    public function execute(string $name, array $permissionIds): int
    {
        $name = trim($name);

        if ($name === '') throw new \InvalidArgumentException("Role name is required");

        $permissionIds = array_values(array_filter(array_map('intval', $permissionIds)));

        $roles = new RoleRepository($this->pdo);
        $rolePermissions = new RolePermissionRepository($this->pdo);

        $this->pdo->beginTransaction();

        try {
            $roleId = $roles->create($name);
            $rolePermissions->createMany($roleId, $permissionIds);

            $this->pdo->commit();
            return $roleId;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}