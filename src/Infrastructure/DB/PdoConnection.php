<?php

declare(strict_types=1);

namespace App\Infrastructure\DB;

use PDO;

class PdoConnection
{
    public static function connect(array $env): PDO 
    {
        $host = $env['DB_HOST'] ?? 'db';
        $port = $env['DB_PORT'] ?? '3306';
        $db = $env['DB_NAME'] ?? 'user_mgmt';
        $user = $env['DB_USER'] ?? 'root';
        $pass = $env['DB_PASS'] ?? 'root';

        $conn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

        return new PDO($conn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }
}