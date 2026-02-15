<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\UseCases\Users\CreateUserUseCase;
use App\Infrastructure\Repositories\RoleRepository;
use App\Infrastructure\Repositories\UserRepository;

class UserController extends BaseController
{
    public function index(): void
    {
        $userRepo = new UserRepository($this->ctx['pdo']);
        $users = $userRepo->allWithRole();

        echo "<h2>Users</h2>";
        echo "<a href='/users/create'>Create User</a><br><br>";

        echo "<table border='1' cellpadding='6' cellspacing='0'>";
        echo "<tr>
                <th>ID</th><th>Name</th><th>Username</th><th>Role</th>
                <th>Email</th><th>Phone</th><th>Active</th>
              </tr>";

        foreach ($users as $user) {
            $id = (int)$user['id'];
            $name = htmlspecialchars($user['name']);
            $username = htmlspecialchars($user['username']);
            $role = htmlspecialchars($user['role_name']);
            $email = htmlspecialchars($user['email'] ?? '---');
            $phone = htmlspecialchars($user['phone'] ?? '---');
            $active = ((int)$user['is_active'] === 1) ? 'Yes' : 'No';

            echo "<tr>
                    <td>{$id}</td>
                    <td>{$name}</td>
                    <td>{$username}</td>
                    <td>{$role}</td>
                    <td>{$email}</td>
                    <td>{$phone}</td>
                    <td>
                        <form method='post' action='/users/toggle-active' style='display:inline;'>
                            <input type='hidden' name='id' value='{$id}'>
                            <button type='submit'>Toggle</button>
                        </form>
                    </td>
                </tr>";
        }
        echo "</table>";
    }

    public function create(): void
    {
        $roleRepo = new RoleRepository($this->ctx['pdo']);
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
            $useCase = new CreateUserUseCase($this->ctx['pdo']);
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
        $userId = (int)$_POST['id'];
        if ($userId <= 0) {
            $this->json([
                'error' => 'Invalid user id'
            ], 422);
            return;
        }

        $userRepo = new UserRepository($this->ctx['pdo']);
        $userRepo->toggleActive($userId);

        $this->redirect('/users');
    }
}