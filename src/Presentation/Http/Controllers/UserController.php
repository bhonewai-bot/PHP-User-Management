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

        $this->view('users/index', [
            'users' => $users
        ], 'Users');
    }

    public function create(): void
    {
        $pdo = $this->ctx['pdo'];

        $roleRepo = new RoleRepository($pdo);
        $roles = $roleRepo->all();

        $this->view('users/form', [
            'mode' => 'create',
            'formAction' => '/users',
            'submitLabel' => 'Save',
            'pageHeading' => 'Create User',
            'showPassword' => true,
            'roles' => $roles,
            'user' => [
                'name' => '',
                'username' => '',
                'email' => '',
                'phone' => '',
                'address' => '',
                'role_id' => 0,
                'gender' => null,
                'is_active' => 1
            ]
        ], 'Create User');
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

        $userId = (int)($_POST['id'] ?? 0);
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

        $userId = (int)($_GET['id'] ?? 0);
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

        $this->view('users/form', [
            'mode' => 'edit',
            'formAction' => '/users/update',
            'submitLabel' => 'Update',
            'pageHeading' => 'Edit User',
            'showPassword' => false,
            'userId' => $userId,
            'roles' => $roles,
            'user' => [
                'name' => (string)$user['name'],
                'username' => (string)$user['username'],
                'email' => (string)($user['email'] ?? ''),
                'phone' => (string)($user['phone'] ?? ''),
                'address' => (string)($user['address'] ?? ''),
                'role_id' => (int)$user['role_id'],
                'gender' => $user['gender'],
                'is_active' => (int)$user['is_active']
            ]
        ], 'Edit User');
    }

    public function update(): void
    {
        $pdo = $this->ctx['pdo'];

        $userId = (int)($_POST['id'] ?? 0);
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
