<?php $count = count($users); ?>

<div class="transaction-page users-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-people me-1"></i> Manage Users</span>
            <strong class="list-toolbar-value"><?= $count ?> user<?= $count !== 1 ? 's' : '' ?></strong>
        </div>
        <div class="page-header-actions list-toolbar-actions">
            <a href="<?= url('users/create') ?>" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i> Add
            </a>
        </div>
    </div>

    <?php if ($count === 0): ?>
        <div class="table-card">
            <div class="empty-state">
                <i class="bi bi-people"></i>
                <p>No users found.</p>
                <a href="<?= url('users/create') ?>" class="btn btn-primary btn-sm">Add User</a>
            </div>
        </div>
    <?php else: ?>
        <div class="report-mobile-list d-lg-none">
            <?php foreach ($users as $i => $user): ?>
                <div class="report-item-card transaction-card">
                    <div class="report-item-top">
                        <div class="transaction-item-main">
                            <span class="transaction-item-no">#<?= $i + 1 ?></span>
                            <strong class="report-item-title"><?= e($user['name']) ?></strong>
                        </div>
                        <span class="badge bg-<?= $user['role'] === 'admin' ? 'primary' : 'secondary' ?>"><?= e(ucfirst($user['role'])) ?></span>
                    </div>
                    <div class="transaction-detail-grid">
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">Email</span>
                            <span><?= e($user['email']) ?></span>
                        </div>
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">Status</span>
                            <span>
                                <span class="stock-badge <?= $user['is_active'] ? 'ok' : 'low' ?>">
                                    <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </span>
                        </div>
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">Created</span>
                            <span><?= e($user['created_at']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="table-card d-none d-lg-block">
            <div class="table-responsive-wrap">
                <table class="table table-striped data-table-lg mb-0">
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
                                <td><strong><?= e($user['name']) ?></strong></td>
                                <td><?= e($user['email']) ?></td>
                                <td><span class="badge bg-<?= $user['role'] === 'admin' ? 'primary' : 'secondary' ?>"><?= e(ucfirst($user['role'])) ?></span></td>
                                <td><span class="stock-badge <?= $user['is_active'] ? 'ok' : 'low' ?>"><?= $user['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                                <td><?= e($user['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
