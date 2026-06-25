<?php $config = require CONFIG_PATH . '/app.php'; $symbol = $config['currency_symbol'] ?? 'Rs.'; ?>

<div class="transaction-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-trash me-1"></i> Daily Wastage</span>
            <strong class="list-toolbar-value">End-of-day spoilage</strong>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="table-card form-card">
                <h5 class="mb-3">Record Wastage</h5>
                <form method="POST" action="<?= url('wastage/store') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Daily Item *</label>
                        <select name="item_id" class="form-select" required>
                            <option value="">Select Item</option>
                            <?php foreach ($items as $item): ?>
                                <option value="<?= $item['id'] ?>"><?= e($item['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Quantity *</label><input type="number" name="quantity" class="form-control" step="0.01" min="0.01" required></div>
                    <div class="mb-3"><label class="form-label">Date *</label><input type="date" name="wastage_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
                    <div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                    <button type="submit" class="btn btn-primary w-100">Save Wastage</button>
                </form>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="table-card filter-card mb-3">
                <form method="GET" action="<?= url('wastage') ?>" class="row g-2 align-items-end">
                    <div class="col-12 col-sm"><label class="form-label">Filter Date</label><input type="date" name="date" class="form-control" value="<?= e($filterDate ?? '') ?>"></div>
                    <div class="col-12 col-sm-auto"><div class="filter-form-actions"><button type="submit" class="btn btn-primary">Filter</button><a href="<?= url('wastage') ?>" class="btn btn-outline-secondary">Clear</a></div></div>
                </form>
            </div>
            <div class="table-card">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Date</th><th>Item</th><th>Qty</th><th>By</th><th>Notes</th></tr></thead>
                    <tbody>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td><?= e($r['wastage_date']) ?></td>
                                <td><?= e($r['item_name']) ?></td>
                                <td><?= money($r['quantity']) ?></td>
                                <td><?= e($r['user_name']) ?></td>
                                <td><?= e($r['notes'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
