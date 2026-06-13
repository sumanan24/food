<form method="POST" action="<?= url('login') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-group-icon">
            <i class="bi bi-envelope input-icon"></i>
            <input type="email" class="form-control" id="email" name="email" placeholder="admin@foodshop.com" required autofocus>
        </div>
    </div>
    <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <div class="input-group-icon">
            <i class="bi bi-lock input-icon"></i>
            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
        </div>
    </div>
    <button type="submit" class="btn btn-primary btn-auth">
        <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
    </button>
</form>
