<?php use Core\Security; ?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between">
        <form method="get"><input name="search" class="form-control form-control-sm" placeholder="Search users" value="<?= Security::escape($search) ?>"></form>
        <a href="<?= url('users/create') ?>" class="btn btn-sm btn-success">Add User</a>
    </div>
    <table class="table">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= Security::escape($u['name']) ?></td>
            <td><?= Security::escape($u['email']) ?></td>
            <td><span class="badge bg-secondary"><?= Security::escape($u['role']) ?></span></td>
            <td><?= $u['status'] ? 'Active' : 'Inactive' ?></td>
            <td>
                <a href="<?= url('users/edit/' . $u['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <?php if ($u['id'] != (currentUser()['id'] ?? 0)): ?>
                <form method="post" action="<?= url('users/delete/' . $u['id']) ?>" class="d-inline" onsubmit="return confirmDelete(this)"><?= Security::csrfField() ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
