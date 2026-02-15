<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use PDO;

class AuthController extends BaseController
{
    public function showLogin(): void
    {
        echo '
        <h2>Login</h2>
        <form method="post" action="/login">
            <label>Username</label><br>
            <input name="username" required><br><br>

            <label>Password</label><br>
            <input name="password" type="password" required><br><br>

            <button type="submit">Login</button>
        </form>
        ';
    }

    public function login(): void
    {
        $pdo = $this->ctx['pdo'];
        
        $username = trim($_POST['username'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $this->json([
                'error' => 'Username and password are required'
            ], 422);
            return;
        }

        $stmt = $pdo->prepare("SELECT id, password, is_active FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $this->json([
                'error' => 'Invalid credentials'
            ], 401);
            return;
        }

        if ((int)$user['is_active'] !== 1) {
            $this->json([
                'error' => 'User is inactive'
            ], 403);
            return;
        }

        if (!password_verify($password, $user['password'])) {
            $this->json([
                'error' => 'Invalid credentials'
            ], 401);
            return;
        }

        foreach (array_keys($_SESSION) as $key) {
            if (str_starts_with($key, 'permission_keys_')) {
                unset($_SESSION[$key]);
            }
        }

        $_SESSION['user_id'] = $user['id'];

        $this->redirect('/users');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        $this->redirect('/login');
    }
}