<?php declare(strict_types=1); ?>
<?php
$mode = (string)($mode ?? 'create');
$formAction = (string)($formAction ?? '/users');
$submitLabel = (string)($submitLabel ?? 'Save');
$pageHeading = (string)($pageHeading ?? ($mode === 'edit' ? 'Edit User' : 'Create User'));
$showPassword = (bool)($showPassword ?? false);

$userId = (int)($userId ?? 0);
$user = is_array($user ?? null) ? $user : [];

$name = (string)($user['name'] ?? '');
$username = (string)($user['username'] ?? '');
$email = (string)($user['email'] ?? '');
$phone = (string)($user['phone'] ?? '');
$address = (string)($user['address'] ?? '');
$roleId = (int)($user['role_id'] ?? 0);
$gender = $user['gender'] ?? null;
$isActive = (int)($user['is_active'] ?? 1) === 1;
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0"><?= htmlspecialchars($pageHeading, ENT_QUOTES, 'UTF-8') ?></h1>
            <a href="/users" class="btn btn-outline-secondary">Back</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="post" action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>">
                    <?php if ($mode === 'edit'): ?>
                        <input type="hidden" name="id" value="<?= $userId ?>">
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Name</label>
                            <input
                                id="name"
                                name="name"
                                value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input
                                id="username"
                                name="username"
                                value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                                class="form-control"
                                required
                            >
                        </div>

                        <?php if ($showPassword): ?>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" name="password" type="password" class="form-control" required>
                            </div>
                        <?php endif; ?>

                        <div class="col-md-6">
                            <label for="role_id" class="form-label">Role</label>
                            <select id="role_id" name="role_id" class="form-select" required>
                                <option value="">-- select --</option>
                                <?php foreach ($roles as $role): ?>
                                    <?php
                                    $optId = (int)$role['id'];
                                    $selected = $optId === $roleId ? 'selected' : '';
                                    ?>
                                    <option value="<?= $optId ?>" <?= $selected ?>>
                                        <?= htmlspecialchars((string)$role['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input id="phone" name="phone" value="<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <input id="address" name="address" value="<?= htmlspecialchars($address, ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label for="gender" class="form-label">Gender</label>
                            <select id="gender" name="gender" class="form-select">
                                <option value="" <?= $gender === null || $gender === '' ? 'selected' : '' ?>>-- select --</option>
                                <option value="1" <?= (string)$gender === '1' ? 'selected' : '' ?>>Male</option>
                                <option value="0" <?= (string)$gender === '0' ? 'selected' : '' ?>>Female</option>
                            </select>
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input id="is_active" class="form-check-input" type="checkbox" name="is_active" <?= $isActive ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8') ?></button>
                        <a href="/users" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
