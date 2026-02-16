<?php declare(strict_types=1); ?>
<?php
$hasPermission = $ctx['hasPermission'] ?? null;
$hasPermission = is_callable($hasPermission) ? $hasPermission : static fn(string $requiredKey): bool => false;
$canUsers = $hasPermission('users.read');
$canRoles = $hasPermission('roles.read');
$isLoggedIn = isset($_SESSION['user_id']);
$currentPath = (string)(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');

$homePath = '/health';
if ($canUsers) {
    $homePath = '/users';
} elseif ($canRoles) {
    $homePath = '/roles';
}
?>
<nav class="navbar navbar-expand-lg bg-white border-bottom mb-4">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="<?= htmlspecialchars($homePath, ENT_QUOTES, 'UTF-8') ?>">User Management</a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($canUsers): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPath === '/users' ? 'active' : '' ?>" href="/users">Users</a>
                    </li>
                <?php endif; ?>

                <?php if ($canRoles): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPath === '/roles' ? 'active' : '' ?>" href="/roles">Roles</a>
                    </li>
                <?php endif; ?>
            </ul>

            <?php if ($isLoggedIn): ?>
                <form method="post" action="/logout" class="d-flex">
                    <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</nav>
