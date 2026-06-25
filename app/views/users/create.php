<div class="transaction-page users-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-person-plus me-1"></i> New User</span>
            <strong class="list-toolbar-value">Add team member</strong>
        </div>
        <div class="page-header-actions list-toolbar-actions">
            <a href="<?= url('users') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="table-card form-card">
        <form method="POST" action="<?= url('users/store') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" placeholder="user@example.com" required>
            </div>
            <div class="row g-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-control" minlength="6" placeholder="Min. 6 characters" required>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label">Role *</label>
                    <select name="role" class="form-select" required>
                        <option value="cashier">Cashier</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 w-sm-auto mt-4">
                <i class="bi bi-check-lg me-1"></i> Create User
            </button>
        </form>
    </div>
</div>
