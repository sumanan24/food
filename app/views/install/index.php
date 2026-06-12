<?php if ($step === 1): ?>
    <div class="mb-4">
        <div class="progress" style="height:8px;">
            <div class="progress-bar" style="width:50%"></div>
        </div>
        <p class="text-center text-muted small mt-2">Step 1 of 2 — Database Setup</p>
    </div>

    <form method="POST" action="<?= url('install/database') ?>">
        <div class="mb-3">
            <label class="form-label">Database Host</label>
            <input type="text" name="host" class="form-control" value="<?= e($dbConfig['host'] ?? 'localhost') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Port</label>
            <input type="number" name="port" class="form-control" value="<?= e((string)($dbConfig['port'] ?? 3306)) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Database Name *</label>
            <input type="text" name="database" class="form-control" value="<?= e($dbConfig['database'] ?? 'foodshop') ?>" required>
            <div class="form-text">Database will be created if it does not exist.</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Username *</label>
            <input type="text" name="username" class="form-control" value="<?= e($dbConfig['username'] ?? 'root') ?>" required>
        </div>
        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-database me-1"></i> Setup Database
        </button>
    </form>
<?php else: ?>
    <div class="mb-4">
        <div class="progress" style="height:8px;">
            <div class="progress-bar" style="width:100%"></div>
        </div>
        <p class="text-center text-muted small mt-2">Step 2 of 2 — Admin Account</p>
    </div>

    <div class="alert alert-info">
        <strong>Default admin (already created):</strong><br>
        Email: <code>admin@foodshop.com</code><br>
        Password: <code>admin123</code>
    </div>

    <div class="d-grid gap-2 mb-4">
        <a href="<?= url('login') ?>" class="btn btn-success">
            <i class="bi bi-box-arrow-in-right me-1"></i> Login with Default Admin
        </a>
    </div>

    <p class="text-muted small text-center mb-3">— or create a custom admin account —</p>

    <form method="POST" action="<?= url('install/admin') ?>">
        <div class="mb-3">
            <label class="form-label">Full Name *</label>
            <input type="text" name="name" class="form-control" value="Administrator">
        </div>
        <div class="mb-3">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" value="admin@foodshop.com">
        </div>
        <div class="mb-3">
            <label class="form-label">Password *</label>
            <input type="password" name="password" class="form-control" minlength="6">
        </div>
        <div class="mb-4">
            <label class="form-label">Confirm Password *</label>
            <input type="password" name="confirm_password" class="form-control" minlength="6">
        </div>
        <button type="submit" class="btn btn-outline-primary w-100">
            <i class="bi bi-check-circle me-1"></i> Create Custom Admin
        </button>
    </form>
<?php endif; ?>
