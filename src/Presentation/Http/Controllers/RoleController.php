<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\UseCases\Roles\CreateRoleUseCase;
use App\Infrastructure\Repositories\PermissionRepository;
use App\Infrastructure\Repositories\RoleRepository;

class RoleController extends BaseController
{
    public function index(): void
    {
        $roleRepo = new RoleRepository($this->ctx['pdo']);
        $roles = $roleRepo->all();

        echo "<h2>Roles</h2>";
        echo "<a href='/roles/create'>Create Role</a><br><br>";

        echo "<table border='1' cellpadding='6' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Name</th></tr>";
        foreach ($roles as $role) {
            echo "<tr><td>{$role['id']}</td><td>{$role['name']}</td></tr>";
        }
        echo "</table>";
    }

    public function create(): void
    {
        $permissionRepo = new PermissionRepository($this->ctx['pdo']);
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
        $roleName = trim($_POST['name'] ?? '');
        $permissionIds = $_POST['permission_ids'] ?? [];

        try {
            $useCase = new CreateRoleUseCase($this->ctx['pdo']);
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
}