<?php use Core\Security; ?>
<div class="card shadow border-0">
    <div class="card-body p-4">
        <h4>Reset Password</h4>
        <form method="post" action="<?= url('reset-password') ?>">
            <?= Security::csrfField() ?>
            <input type="hidden" name="token" value="<?= Security::escape($token) ?>">
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirm" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Password</button>
        </form>
    </div>
</div>
