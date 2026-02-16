<?php declare(strict_types=1); ?>
<?php
$hasPermission = $ctx['hasPermission'] ?? null;
$hasPermission = is_callable($hasPermission) ? $hasPermission : static fn(string $requiredKey): bool => false;
$canCreate = $hasPermission('users.create');
$canUpdate = $hasPermission('users.update');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Users</h1>
    <?php if ($canCreate): ?>
        <a href="/users/create" class="btn btn-primary">Create User</a>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Active</th>
                    <th class="text-end">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <?php
                    $id = (int)$user['id'];
                    $name = htmlspecialchars((string)$user['name'], ENT_QUOTES, 'UTF-8');
                    $username = htmlspecialchars((string)$user['username'], ENT_QUOTES, 'UTF-8');
                    $role = htmlspecialchars((string)$user['role_name'], ENT_QUOTES, 'UTF-8');
                    $emailRaw = trim((string)($user['email'] ?? ''));
                    $phoneRaw = trim((string)($user['phone'] ?? ''));
                    $email = htmlspecialchars($emailRaw !== '' ? $emailRaw : '---', ENT_QUOTES, 'UTF-8');
                    $phone = htmlspecialchars($phoneRaw !== '' ? $phoneRaw : '---', ENT_QUOTES, 'UTF-8');
                    $isActive = (int)$user['is_active'] === 1;
                    ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><?= $name ?></td>
                        <td><?= $username ?></td>
                        <td><?= $role ?></td>
                        <td><?= $email ?></td>
                        <td><?= $phone ?></td>
                        <td>
                            <?php if ($canUpdate): ?>
                                <form method="post" action="/users/toggle-active" class="d-flex align-items-center gap-2 mb-0">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <div class="form-check form-switch mb-0">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            id="active-switch-<?= $id ?>"
                                            <?= $isActive ? 'checked' : '' ?>
                                            onchange="this.form.submit()"
                                        >
                                        <label class="form-check-label small text-muted" for="active-switch-<?= $id ?>">
                                            <?= $isActive ? 'On' : 'Off' ?>
                                        </label>
                                    </div>
                                    <noscript>
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">Apply</button>
                                    </noscript>
                                </form>
                            <?php else: ?>
                                <span class="badge text-bg-<?= $isActive ? 'success' : 'secondary' ?>">
                                    <?= $isActive ? 'Active' : 'Inactive' ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if ($canUpdate): ?>
                                <a href="/users/edit?id=<?= $id ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($users) === 0): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No users found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
