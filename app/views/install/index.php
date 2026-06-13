<?php if ($step === 1): ?>
    <div class="install-progress"><div class="install-progress-bar" style="width:50%"></div></div>
    <p class="install-step-label">Step 1 of 2 — Database Setup</p>

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
            <div class="form-text">Created automatically if it doesn't exist.</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Username *</label>
            <input type="text" name="username" class="form-control" value="<?= e($dbConfig['username'] ?? 'root') ?>" required>
        </div>
        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary btn-auth">
            <i class="bi bi-database me-2"></i> Setup Database
        </button>
    </form>
<?php else: ?>
    <div class="install-progress"><div class="install-progress-bar" style="width:100%"></div></div>
    <p class="install-step-label">Step 2 of 2 — Admin Account</p>

    <div class="alert alert-app alert-success mb-4">
        <strong>Default admin ready:</strong><br>
        <code>admin@foodshop.com</code> / <code>admin123</code>
    </div>

    <a href="<?= url('login') ?>" class="btn btn-primary btn-auth mb-3">
        <i class="bi bi-box-arrow-in-right me-2"></i> Login Now
    </a>

    <p class="text-muted small text-center mb-3">— or create custom admin —</p>

    <form method="POST" action="<?= url('install/admin') ?>">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="Administrator">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="admin@foodshop.com">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" minlength="6">
        </div>
        <div class="mb-4">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" minlength="6">
        </div>
        <button type="submit" class="btn btn-outline-primary btn-auth">
            Create Custom Admin
        </button>
    </form>
<?php endif; ?>
