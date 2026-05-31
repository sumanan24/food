<?php use Core\Security; $s = $settings; ?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header">Shop Settings</div>
            <div class="card-body">
                <form method="post" action="<?= url('settings/update') ?>">
                    <?= Security::csrfField() ?>
                    <div class="row g-3">
                        <div class="col-md-6"><label>Shop Name</label><input name="shop_name" class="form-control" value="<?= Security::escape($s['shop_name'] ?? '') ?>"></div>
                        <div class="col-md-6"><label>Phone</label><input name="shop_phone" class="form-control" value="<?= Security::escape($s['shop_phone'] ?? '') ?>"></div>
                        <div class="col-md-6"><label>Email</label><input name="shop_email" class="form-control" value="<?= Security::escape($s['shop_email'] ?? '') ?>"></div>
                        <div class="col-md-6"><label>Currency</label><input name="currency" class="form-control" value="<?= Security::escape($s['currency'] ?? 'Rs.') ?>"></div>
                        <div class="col-12"><label>Address</label><textarea name="shop_address" class="form-control"><?= Security::escape($s['shop_address'] ?? '') ?></textarea></div>
                        <div class="col-md-4"><label>Tax Rate %</label><input type="number" step="0.01" name="tax_rate" class="form-control" value="<?= Security::escape($s['tax_rate'] ?? '0') ?>"></div>
                        <div class="col-md-4"><label>Low Stock Threshold</label><input type="number" name="low_stock_threshold" class="form-control" value="<?= Security::escape($s['low_stock_threshold'] ?? '10') ?>"></div>
                        <div class="col-md-4"><label>Invoice Prefix</label><input name="invoice_prefix" class="form-control" value="<?= Security::escape($s['invoice_prefix'] ?? 'INV') ?>"></div>
                        <div class="col-md-4"><label>Theme</label>
                            <select name="theme" class="form-select"><option value="light" <?= ($s['theme']??'light')==='light'?'selected':'' ?>>Light</option><option value="dark" <?= ($s['theme']??'')==='dark'?'selected':'' ?>>Dark</option></select></div>
                    </div>
                    <button class="btn btn-primary mt-3">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
    <?php if (hasRole('super_admin')): ?>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header">Database Backup</div>
            <div class="card-body">
                <form method="post" action="<?= url('settings/backup') ?>"><?= Security::csrfField() ?>
                    <button class="btn btn-outline-primary w-100">Create Backup</button>
                </form>
                <p class="small text-muted mt-2 mb-0">Backups are saved in the <code>backups/</code> folder.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
