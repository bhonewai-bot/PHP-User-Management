<?php

declare(strict_types=1);

namespace App\Application\UseCases\Users;

use App\Infrastructure\Repositories\UserRepository;
use PDO;

class CreateUserUseCase
{
    public function __construct(private PDO $pdo) {}

    public function execute(array $data): int
    {
        $name = trim($data['name']);
        $username = trim($data['username']);
        $password = $data['password'];
        $roleId = (int)$data['role_id'];

        if ($name === '') throw new \InvalidArgumentException("Name is required");
        if ($username === '') throw new \InvalidArgumentException("Username is required");
        if ($password === '') throw new \InvalidArgumentException("Password is required");
        if ($roleId <= 0) throw new \InvalidArgumentException("Role is required");

        $phone = trim($data['phone'] ?? '');
        $email = trim($data['email'] ?? '');
        $address = trim($data['address'] ?? '');

        $genderRaw = $data['gender'] ?? null;
        $gender = null;
        if ($genderRaw !== null && $genderRaw !== '') {
            $gender = ((int)$genderRaw) === 1 ? 1 : 0;
        }

        $isActive = isset($data['is_active']) ? 1 : 0;

        $hash = password_hash($password, PASSWORD_BCRYPT);

        $users = new UserRepository($this->pdo);

        return $users->create([
            'name' => $name,
            'username' => $username,
            'role_id' => $roleId,
            'phone' => $phone !== '' ? $phone : null,
            'email' => $email !== '' ? $email : null,
            'address' => $address !== '' ? $address : null,
            'password' => $hash,
            'gender' => $gender,
            'is_active' => $isActive,
        ]);
    }
}