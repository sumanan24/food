<?php $config = require CONFIG_PATH . '/app.php'; ?>

<div class="transaction-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-journal-text me-1"></i> Stock Ledger</span>
            <strong class="list-toolbar-value">Item #<?= (int) $itemId ?></strong>
        </div>
        <div class="page-header-actions list-toolbar-actions">
            <a href="<?= url('inventory?start=' . urlencode($start) . '&end=' . urlencode($end)) ?>" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="table-card">
        <table class="table table-striped mb-0">
            <thead><tr><th>Date</th><th>Type</th><th>In</th><th>Out</th><th>Balance</th><th>By</th><th>Notes</th></tr></thead>
            <tbody>
                <?php foreach ($entries as $e): ?>
                    <tr>
                        <td><?= e($e['ledger_date']) ?></td>
                        <td><?= e(ucfirst($e['transaction_type'])) ?></td>
                        <td><?= money($e['quantity_in']) ?></td>
                        <td><?= money($e['quantity_out']) ?></td>
                        <td><strong><?= money($e['balance_after']) ?></strong></td>
                        <td><?= e($e['user_name']) ?></td>
                        <td><?= e($e['notes'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
