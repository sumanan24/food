<?php use Core\Security; ?>
<div class="card shadow border-0">
    <div class="card-body p-4">
        <h4>Forgot Password</h4>
        <form method="post" action="<?= url('forgot-password') ?>">
            <?= Security::csrfField() ?>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
            <a href="<?= url('login') ?>" class="btn btn-link w-100 mt-2">Back to Login</a>
        </form>
    </div>
</div>
