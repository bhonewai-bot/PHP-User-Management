<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\UseCases\Users\CreateUserUseCase;
use App\Application\UseCases\Users\UpdateUserUseCase;
use App\Infrastructure\Repositories\RoleRepository;
use App\Infrastructure\Repositories\UserRepository;

class UserController extends BaseController
{
    public function index(): void
    {
        $pdo = $this->ctx['pdo'];

        $userRepo = new UserRepository($pdo);
        $users = $userRepo->allWithRole();

        $hasPermission = $this->ctx['hasPermission'];

        echo "<h2>Users</h2>";
        if ($hasPermission('user.create')) {
            echo "<a href='/users/create'>Create User</a><br><br>";
        } else {
            echo "<br>";
        }

        echo "<table border='1' cellpadding='6' cellspacing='0'>";
        echo "<tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Role</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Active</th>
                <th>Action</th>
              </tr>";

        foreach ($users as $user) {
            $id = (int)$user['id'];
            $name = htmlspecialchars($user['name']);
            $username = htmlspecialchars($user['username']);
            $role = htmlspecialchars($user['role_name']);
            $email = htmlspecialchars($user['email'] ?? '---');
            $phone = htmlspecialchars($user['phone'] ?? '---');

            $activeHtml = '';
            if ($hasPermission('user.update')) {
                $activeHtml = "
                    <form method='post' action='/users/toggle-active' style='display:inline;'>
                        <input type='hidden' name='id' value='{$id}'>
                        <button type='submit'>Toggle</button>
                    </form>";
            }

            $actions = [];

            if ($hasPermission('user.update')) {
                $actions[] = "<a href='/users/edit?id={$id}'>Edit</a>";
            }

            $actionHtml = count($actions) ? implode(' ', $actions) : '';

            echo "<tr>
                    <td>{$id}</td>
                    <td>{$name}</td>
                    <td>{$username}</td>
                    <td>{$role}</td>
                    <td>{$email}</td>
                    <td>{$phone}</td>
                    <td>{$activeHtml}</td>
                    <td>{$actionHtml}</td>
                </tr>";
        }
        echo "</table>";
    }

    public function create(): void
    {
        $pdo = $this->ctx['pdo'];

        $roleRepo = new RoleRepository($pdo);
        $roles = $roleRepo->all();

        echo "<h2>Create User</h2>";
        echo "<form method='post' action='/users'>";

        echo "Name<br><input name='name' required><br><br>";
        echo "Username<br><input name='username' required><br><br>";
        echo "Password<br><input name='password' type='password' required><br><br>";

        echo "Role<br><select name='role_id' required>";
        echo "<option value=''>-- select --</option>";

        foreach ($roles as $role) {
            $id = (int)$role['id'];
            $name = htmlspecialchars($role['name']);

            echo "<option value='{$id}'>{$name}</option>";
        }
        echo "</select><br><br>";

        echo "Email<br><input name='email'><br><br>";
        echo "Phone<br><input name='phone'><br><br>";
        echo "Address<br><input name='address'><br><br>";

        echo "Gender<br>
            <select name='gender'>
            <option value=''>-- select --</option>
            <option value='1'>Male</option>
            <option value='0'>Female</option>
            </select><br><br>";

        echo "<label><input type='checkbox' name='is_active' checked> Active</label><br><br>";

        echo "<button type='submit'>Save</button>";
        echo "</form>";
    }

    public function store(): void
    {
        try {
            $pdo = $this->ctx['pdo'];

            $useCase = new CreateUserUseCase($pdo);
            $useCase->execute($_POST);

            $this->redirect('/users');
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

    public function toggleActive(): void
    {
        $pdo = $this->ctx['pdo'];

        $userId = (int)$_POST['id'];
        if ($userId <= 0) {
            $this->json([
                'error' => 'Invalid user id'
            ], 422);
            return;
        }

        $userRepo = new UserRepository($pdo);
        $userRepo->toggleActive($userId);

        $this->redirect('/users');
    }

    public function edit(): void
    {
        $pdo = $this->ctx['pdo'];

        $userId = (int)$_GET['id'] ?? 0;
        if ($userId <= 0) {
            $this->json([
                'error' => 'Invalid user id'
            ], 422);
            return;
        }

        $userRepo = new UserRepository($pdo);
        $roleRepo = new RoleRepository($pdo);

        $user = $userRepo->find($userId);
        if (!$user) {
            $this->json([
                'error' => 'User not found'
            ], 404);
            return;
        }

        $roles = $roleRepo->all();

        $name = htmlspecialchars($user['name']);
        $username = htmlspecialchars($user['username']);
        $email = htmlspecialchars($user['email'] ?? '');
        $phone = htmlspecialchars($user['phone'] ?? '');
        $address = htmlspecialchars($user['address'] ?? '');
        $roleId = (int)$user['role_id'];
        $gender = $user['gender'];
        $isActive = $user['is_active'];

        echo "<h2>Edit User</h2>";
        echo "<form method='post' action='/users/update'>";
        echo "<input type='hidden' name='id' value='{$userId}'>";

        echo "Name<br><input name='name' value='{$name}' required><br><br>";
        echo "Username<br><input name='username' value='{$username}' required><br><br>";

        echo "Role<br><select name='role_id' required>";
        foreach ($roles as $role) {
            $roleId = (int)$role['id'];
            $roleName = htmlspecialchars($role['name']);
            $selected = $roleId === $user['role_id'] ? 'selected' : '';
            echo "<option value='{$roleId}' {$selected}>{$roleName}</option>";
        }
        echo "</select><br><br>";

        echo "Email<br><input name='email' value='{$email}'><br><br>";
        echo "Phone<br><input name='phone' value='{$phone}'><br><br>";
        echo "Address<br><input name='address' value='{$address}'><br><br>";

        echo "Gender<br>
            <select name='gender'>
                <option value='' ".($gender === null ? "selected" : "").">-- select --</option>
                <option value='1' ".((string)$gender === '1' ? "selected" : "").">Male</option>
                <option value='0' ".((string)$gender === '0' ? "selected" : "").">Female</option>
            </select><br><br>";

        $checked = $isActive ? "checked" : "";
        echo "<label><input type='checkbox' name='is_active' {$checked}> Active</label><br><br>";

        echo "<button type='submit'>Update</button>";
        echo "</form>";
    }

    public function update(): void
    {
        $pdo = $this->ctx['pdo'];

        $userId = (int)$_POST['id'];
        if ($userId <= 0) {
            $this->json([
                'error' => 'Invalid user id'
            ], 422);
            return;
        }

        try {
            $useCase = new UpdateUserUseCase($pdo);
            $useCase->execture($userId, $_POST);

            foreach (array_keys($_SESSION) as $key) {
                if (str_starts_with($key, 'permission_keys_')) {
                    unset($_SESSION[$key]);
                }
            }

            $this->redirect('/users');
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