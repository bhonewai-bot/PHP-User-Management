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

        $hasPermission = $this->ctx['hasPermission'];

        echo "<h2>Roles</h2>";
        if ($hasPermission('roles.create')) {
            echo "<a href='/roles/create'>Create Role</a><br><br>";
        } else {
            echo "<br>";
        }

        echo "<table border='1' cellpadding='6' cellspacing='0'>";
        echo "<tr>
                <th>ID</th>
                <th>Name</th>
                <th>Action</th>
            </tr>";
        foreach ($roles as $role) {
            $action = '';
            if ($hasPermission('roles.update')) {
                $action = "<a href='/roles/edit?id={$role['id']}'>Edit</a>";
            }
            echo "<tr>
                    <td>{$role['id']}</td>
                    <td>{$role['name']}</td>
                    <td>{$action}</td>
                </tr>";
        }
        echo "</table>";
    }

    public function create(): void
    {
        $pdo = $this->ctx['pdo'];

        $permissionRepo = new PermissionRepository($pdo);
        $features = $permissionRepo->groupedByFeature();

        echo "<h2>Create Role</h2>";
        echo "<form method='post' action='/roles'>";
        echo "<label>Role name</label><br>";
        echo "<input name='name' required><br><br>";

        echo "<h3>Role Permissions</h3>";

        echo "<script>
            function toggleFeature(featureId, source) {
                const boxes = document.querySelectorAll('.perm-' + featureId);
                boxes.forEach(b => b.checked = source.checked);
            }
        </script>";

        foreach ($features as $feature) {
            $featureId = $feature['feature_id'];
            $featureName = htmlspecialchars($feature['feature_name']);
            
            echo "<fieldset style='margin-bottom:16px; padding:10px;'>";
            echo "<legend><strong>{$featureName}</strong></legend>";

            echo "<label>
                    <input type='checkbox' onclick='toggleFeature({$featureId}, this)'>
                    Select all
                </label><br><br>";

            foreach ($feature['permissions'] as $permission) {
                $permissionId = $permission['id'];
                $permissionName = htmlspecialchars($permission['name']);
                echo "<label style='margin-right:12px; display:inline-block; min-width:90px;'>
                        <input class='perm-{$featureId}' type='checkbox' name='permission_ids[]' value='{$permissionId}'>
                        {$permissionName}
                    </label>";
            }

            echo "</fieldset>";
        }

        echo "<button type='submit'>Save</button>";
        echo "</form>";
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

        $roleId = (int)$_GET['id'] ?? 0;
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
        $permissionsMap = array_fill_keys($permissions, true);

        $name = htmlspecialchars($role['name']);

        echo "<h2>Edit Role</h2>";
        echo "<form method='post' action='/roles/update'>";
        echo "<input type='hidden' name='id' value='{$roleId}'>";

        echo "<label>Role name</label><br>";
        echo "<input name='name' value='{$name}' required><br><br>";

        echo "<h3>Role Permissions</h3>";

        echo "<script>
            function toggleFeature(featureId, source) {
                const boxes = document.querySelectorAll('.perm-' + featureId);
                boxes.forEach(b => b.checked = source.checked);
            }
        </script>";

        foreach ($features as $feature) {
            $featureId = $feature['feature_id'];
            $featureName = htmlspecialchars($feature['feature_name']);
            
            echo "<fieldset style='margin-bottom:16px; padding:10px;'>";
            echo "<legend><strong>{$featureName}</strong></legend>";

            echo "<label>
                    <input type='checkbox' onclick='toggleFeature({$featureId}, this)'>
                    Select all
                </label><br><br>";

            foreach ($feature['permissions'] as $permission) {
                $permissionId = $permission['id'];
                $permissionName = htmlspecialchars($permission['name']);
                $checked = isset($permissionsMap[$permissionId]) ? 'checked' : '';
                echo "<label style='margin-right:12px; display:inline-block; min-width:90px;'>
                        <input class='perm-{$featureId}' type='checkbox' name='permission_ids[]' value='{$permissionId}' {$checked}>
                        {$permissionName}
                    </label>";
            }

            echo "</fieldset>";
        }

        echo "<button type='submit'>Update</button>";
        echo "</form>";
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