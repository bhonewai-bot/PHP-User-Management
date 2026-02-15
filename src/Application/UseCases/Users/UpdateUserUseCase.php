<?php

declare(strict_types=1);

namespace App\Application\UseCases\Users;

use App\Infrastructure\Repositories\UserRepository;
use PDO;

class UpdateUserUseCase
{
    public function __construct(private PDO $pdo) {}

    public function execture(int $userId, array $data): void
    {
        if ($userId <= 0) throw new \InvalidArgumentException("Invalid user id");

        $name = trim($data['name']);
        $username = trim($data['username']);
        $roleId = (int)$data['role_id'];

        if ($name === '') throw new \InvalidArgumentException("Name is required");
        if ($username === '') throw new \InvalidArgumentException("Username is required");
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

        $userRepo = new UserRepository($this->pdo);
        $userRepo->update($userId, [
            'name' => $name,
            'username' => $username,
            'role_id' => $roleId,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'gender' => $gender,
            'is_active' => $isActive
        ]);
    }
}