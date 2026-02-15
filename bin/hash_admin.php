<?php

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';

use App\Infrastructure\DB\PdoConnection;

$pdo = PdoConnection::connect($_ENV);

$username = 'admin';
$Plain = 'admin123';

$hash = password_hash($Plain, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
$stmt->execute([$hash, $username]);

echo "✅ Updated admin password to hashed for username=admin\n";