<?php
$config = require CONFIG_PATH . '/app.php';
$symbol = $config['currency_symbol'] ?? 'Rs.';
$count = count($bills);
$totalAmount = array_sum(array_map(fn($b) => (float) $b['total_amount'], $bills));
?>

<div class="transaction-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-receipt me-1"></i> Bill History</span>
            <strong class="list-toolbar-value"><?= $count ?> bill<?= $count !== 1 ? 's' : '' ?></strong>
            <?php if ($count > 0): ?>
                <span class="list-toolbar-meta"><?= $symbol ?> <?= money($totalAmount) ?></span>
            <?php endif; ?>
        </div>
        <div class="page-header-actions list-toolbar-actions">
            <a href="<?= url('pos') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New Bill</a>
        </div>
    </div>

    <div class="table-card filter-card mb-3">
        <form method="GET" action="<?= url('pos/history') ?>" class="filter-form-mobile">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-sm"><label class="form-label">Date</label><input type="date" name="date" class="form-control" value="<?= e($filterDate ?? '') ?>"></div>
                <div class="col-12 col-sm-auto"><div class="filter-form-actions"><button type="submit" class="btn btn-primary">Filter</button><a href="<?= url('pos/history') ?>" class="btn btn-outline-secondary">Clear</a></div></div>
            </div>
        </form>
    </div>

    <?php if ($count === 0): ?>
        <div class="table-card"><div class="empty-state"><p>No bills found.</p></div></div>
    <?php else: ?>
        <div class="report-mobile-list d-lg-none">
            <?php foreach ($bills as $b): ?>
                <a href="<?= url('pos/receipt/' . $b['id']) ?>" class="report-item-card transaction-card text-decoration-none">
                    <div class="report-item-top">
                        <strong class="report-item-title"><?= e($b['bill_number']) ?></strong>
                        <span class="report-item-amount"><?= $symbol ?> <?= money($b['total_amount']) ?></span>
                    </div>
                    <div class="transaction-detail-grid">
                        <div class="transaction-detail"><span class="transaction-detail-label">Date</span><span><?= e($b['bill_date']) ?></span></div>
                        <div class="transaction-detail"><span class="transaction-detail-label">By</span><span><?= e($b['user_name']) ?></span></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="table-card d-none d-lg-block">
            <table class="table table-striped data-table-lg mb-0">
                <thead><tr><th>Bill #</th><th>Date</th><th>Total</th><th>Payment</th><th>By</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($bills as $b): ?>
                        <tr>
                            <td><strong><?= e($b['bill_number']) ?></strong></td>
                            <td><?= e($b['bill_date']) ?></td>
                            <td><?= money($b['total_amount']) ?></td>
                            <td><?= e(ucfirst($b['payment_method'])) ?></td>
                            <td><?= e($b['user_name']) ?></td>
                            <td><a href="<?= url('pos/receipt/' . $b['id']) ?>" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
