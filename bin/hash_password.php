<?php

declare(strict_types=1);

if ($argc < 3) {
    echo "Usage: php bin/hash_password.php <username> <plain_password>\n";
    exit(1);
}

$username = $argv[1];
$plain = $argv[2];

$hash = password_hash($plain, PASSWORD_BCRYPT);

echo "Hash: " . $hash . PHP_EOL;