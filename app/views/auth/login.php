<?php use Core\Security; ?>
<div class="card shadow-lg border-0">
    <div class="card-body p-4">
        <h4 class="card-title mb-4">Admin Login</h4>
        <form method="post" action="<?= url('login') ?>">
            <?= Security::csrfField() ?>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="admin@foodshop.com">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Admin@123">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <div class="text-center mt-3">
                <a href="<?= url('forgot-password') ?>">Forgot password?</a>
            </div>
        </form>
    </div>
</div>
