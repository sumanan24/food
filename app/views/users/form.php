<?php use Core\Security; $isEdit = !empty($user); ?>
<div class="card shadow-sm col-lg-6 mx-auto">
    <div class="card-body">
        <form method="post" action="<?= url($isEdit ? 'users/update/' . $user['id'] : 'users/store') ?>">
            <?= Security::csrfField() ?>
            <div class="mb-3"><label>Name</label><input name="name" class="form-control" required value="<?= Security::escape($user['name'] ?? '') ?>"></div>
            <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required value="<?= Security::escape($user['email'] ?? '') ?>"></div>
            <div class="mb-3"><label>Phone</label><input name="phone" class="form-control" value="<?= Security::escape($user['phone'] ?? '') ?>"></div>
            <div class="mb-3"><label>Password <?= $isEdit ? '(leave blank to keep)' : '' ?></label><input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?>></div>
            <div class="mb-3"><label>Role</label>
                <select name="role" class="form-select">
                    <?php foreach (['cashier','manager','admin','super_admin'] as $r): ?>
                    <option value="<?= $r ?>" <?= ($user['role'] ?? '') === $r ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$r)) ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div class="mb-3"><label>Status</label>
                <select name="status" class="form-select"><option value="1" <?= ($user['status'] ?? 1) ? 'selected':'' ?>>Active</option><option value="0" <?= isset($user['status']) && !$user['status'] ? 'selected':'' ?>>Inactive</option></select></div>
            <button class="btn btn-primary">Save</button>
            <a href="<?= url('users') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
