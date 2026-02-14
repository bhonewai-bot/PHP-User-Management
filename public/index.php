<?php
declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/health') {
    header('Content-Type: application/json');
    
    $dsn = "mysql:host=db;port=3306;dbname=user_mgmt;charset=utf8mb4";
    $pdo = new PDO($dsn, "root", "root", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $version = $pdo->query("SELECT VERSION()")->fetchColumn();

    echo json_encode([
        "ok" => true,
        "mysql_version" => $version
    ]);
    exit;
}

echo "OK - try /health";