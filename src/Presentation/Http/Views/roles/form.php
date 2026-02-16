<?php declare(strict_types=1); ?>
<?php
$mode = (string)($mode ?? 'create');
$formAction = (string)($formAction ?? '/roles');
$submitLabel = (string)($submitLabel ?? 'Save');
$pageHeading = (string)($pageHeading ?? ($mode === 'edit' ? 'Edit Role' : 'Create Role'));
$roleId = (int)($roleId ?? 0);
$roleName = (string)($roleName ?? '');
$selectedPermissionIds = is_array($selectedPermissionIds ?? null) ? $selectedPermissionIds : [];
$selectedMap = array_fill_keys(array_map('intval', $selectedPermissionIds), true);
?>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0"><?= htmlspecialchars($pageHeading, ENT_QUOTES, 'UTF-8') ?></h1>
            <a href="/roles" class="btn btn-outline-secondary">Back</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="post" action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>">
                    <?php if ($mode === 'edit'): ?>
                        <input type="hidden" name="id" value="<?= $roleId ?>">
                    <?php endif; ?>

                    <div class="mb-4">
                        <label for="name" class="form-label">Role name</label>
                        <input
                            id="name"
                            name="name"
                            value="<?= htmlspecialchars($roleName, ENT_QUOTES, 'UTF-8') ?>"
                            class="form-control"
                            required
                        >
                    </div>

                    <h2 class="h5 mb-3">Role Permissions</h2>

                    <div class="row g-3">
                        <?php foreach ($features as $feature): ?>
                            <?php
                            $featureId = (int)$feature['feature_id'];
                            $featureName = htmlspecialchars((string)$feature['feature_name'], ENT_QUOTES, 'UTF-8');
                            ?>
                            <div class="col-12 col-lg-6">
                                <div class="card h-100 border">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h3 class="h6 mb-0"><?= $featureName ?></h3>
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="feature-all-<?= $featureId ?>"
                                                    onclick="toggleFeature(<?= $featureId ?>, this.checked)"
                                                >
                                                <label class="form-check-label small" for="feature-all-<?= $featureId ?>">Select all</label>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap gap-3">
                                            <?php foreach ($feature['permissions'] as $permission): ?>
                                                <?php
                                                $permissionId = (int)$permission['id'];
                                                $permissionName = htmlspecialchars((string)$permission['name'], ENT_QUOTES, 'UTF-8');
                                                $checked = isset($selectedMap[$permissionId]) ? 'checked' : '';
                                                ?>
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input perm-<?= $featureId ?>"
                                                        type="checkbox"
                                                        id="permission-<?= $permissionId ?>"
                                                        name="permission_ids[]"
                                                        value="<?= $permissionId ?>"
                                                        <?= $checked ?>
                                                    >
                                                    <label class="form-check-label" for="permission-<?= $permissionId ?>"><?= $permissionName ?></label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8') ?></button>
                        <a href="/roles" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFeature(featureId, checked) {
    const checkboxes = document.querySelectorAll('.perm-' + featureId);
    checkboxes.forEach((checkbox) => {
        checkbox.checked = checked;
    });
}
</script>
