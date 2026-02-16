<?php declare(strict_types=1); ?>
<?php
$hasPermission = $ctx['hasPermission'] ?? null;
$hasPermission = is_callable($hasPermission) ? $hasPermission : static fn(string $requiredKey): bool => false;
$canCreate = $hasPermission('roles.create');
$canUpdate = $hasPermission('roles.update');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Roles</h1>
    <?php if ($canCreate): ?>
        <a href="/roles/create" class="btn btn-primary">Create Role</a>
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
                    <th class="text-end">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($roles as $role): ?>
                    <?php
                    $id = (int)$role['id'];
                    $name = htmlspecialchars((string)$role['name'], ENT_QUOTES, 'UTF-8');
                    ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><?= $name ?></td>
                        <td class="text-end">
                            <?php if ($canUpdate): ?>
                                <a href="/roles/edit?id=<?= $id ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($roles) === 0): ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">No roles found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
