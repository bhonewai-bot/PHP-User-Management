<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use PDO;

class HealthController extends BaseController
{
    public function index(): void
    {
        $pdo = $this->ctx['pdo'];

        $version = $pdo->query("SELECT VERSION()")->fetchColumn();

        $this->json([
            "ok" => true,
            "mysql_version" => $version
        ]);
    }
}