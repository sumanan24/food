<div class="page-header">
    <h1 class="h3 mb-0">Manage Users</h1>
    <a href="<?= url('users/create') ?>" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Add User
    </a>
</div>

<div class="table-card">
    <table class="table table-striped data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $i => $user): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($user['name']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><span class="badge bg-<?= $user['role'] === 'admin' ? 'primary' : 'secondary' ?>"><?= e(ucfirst($user['role'])) ?></span></td>
                    <td><span class="badge bg-<?= $user['is_active'] ? 'success' : 'danger' ?>"><?= $user['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                    <td><?= e($user['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
