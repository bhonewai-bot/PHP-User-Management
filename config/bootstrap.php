<?php

declare(strict_types=1);

use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

error_reporting(E_ALL);
ini_set('display_errors', ($_ENV['APP_ENV'] ?? 'local') === 'local' ? '1' : '0');

if (session_start() === PHP_SESSION_NONE) {
    session_start();
}