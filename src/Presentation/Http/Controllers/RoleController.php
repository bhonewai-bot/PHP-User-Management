<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\UseCases\Roles\CreateRoleUseCase;
use App\Application\UseCases\Roles\UpdateRoleUseCase;
use App\Infrastructure\Repositories\PermissionRepository;
use App\Infrastructure\Repositories\RolePermissionRepository;
use App\Infrastructure\Repositories\RoleRepository;

class RoleController extends BaseController
{
    public function index(): void
    {
        $pdo = $this->ctx['pdo'];

        $roleRepo = new RoleRepository($pdo);
        $roles = $roleRepo->all();

        $this->view('roles/index', [
            'roles' => $roles
        ], 'Roles');
    }

    public function create(): void
    {
        $pdo = $this->ctx['pdo'];

        $permissionRepo = new PermissionRepository($pdo);
        $features = $permissionRepo->groupedByFeature();

        $this->view('roles/form', [
            'mode' => 'create',
            'formAction' => '/roles',
            'submitLabel' => 'Save',
            'pageHeading' => 'Create Role',
            'roleName' => '',
            'features' => $features,
            'selectedPermissionIds' => []
        ], 'Create Role');
    }

    public function store(): void
    {
        $pdo = $this->ctx['pdo'];

        $roleName = trim($_POST['name'] ?? '');
        $permissionIds = $_POST['permission_ids'] ?? [];

        try {
            $useCase = new CreateRoleUseCase($pdo);
            $useCase->execute($roleName, $permissionIds);

            $this->redirect('/roles');
        } catch (\InvalidArgumentException $e) {
            $this->json([
                'error' => $e->getMessage()
            ], 422);
        } catch (\Throwable $e) {
            $this->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function edit(): void
    {
        $pdo = $this->ctx['pdo'];

        $roleId = (int)($_GET['id'] ?? 0);
        if ($roleId <= 0) {
            $this->json([
                'error' => 'Invalid role id'
            ], 422);
            return;
        }

        $roleRepo = new RoleRepository($pdo);
        $permissionRepo = new PermissionRepository($pdo);
        $rolePermissionRepo = new RolePermissionRepository($pdo);

        $role = $roleRepo->find($roleId);
        if (!$role) {
            $this->json([
                'error' => 'Role not found'
            ], 404);
            return;
        }

        $features = $permissionRepo->groupedByFeature();
        $permissions = $rolePermissionRepo->permissionIdsForRole($roleId);

        $this->view('roles/form', [
            'mode' => 'edit',
            'formAction' => '/roles/update',
            'submitLabel' => 'Update',
            'pageHeading' => 'Edit Role',
            'roleId' => $roleId,
            'roleName' => (string)$role['name'],
            'features' => $features,
            'selectedPermissionIds' => $permissions
        ], 'Edit Role');
    }

    public function update(): void
    {
        $pdo = $this->ctx['pdo'];

        $roleId = (int)($_POST['id'] ?? 0);
        $roleName = trim($_POST['name'] ?? '');
        $permissionIds = $_POST['permission_ids'] ?? [];

        try {
            $useCase = new UpdateRoleUseCase($pdo);
            $useCase->execute($roleId, $roleName, $permissionIds);

            foreach (array_keys($_SESSION) as $key) {
                if (str_starts_with($key, 'permission_keys_')) {
                    unset($_SESSION[$key]);
                }
            }

            $this->redirect('/roles');
        } catch (\InvalidArgumentException $e) {
            $this->json([
                'error' => $e->getMessage()
            ], 422);
        } catch (\Throwable $e) {
            $this->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
