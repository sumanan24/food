<?php
$config = require CONFIG_PATH . '/app.php';
$symbol = $config['currency_symbol'] ?? 'Rs.';
$count = count($sales);
$totalAmount = array_sum(array_map(fn($s) => (float) $s['total_price'], $sales));
?>

<div class="transaction-page sales-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-calendar3 me-1"></i> Today</span>
            <strong class="list-toolbar-value"><?= e($date) ?></strong>
            <?php if ($count > 0): ?>
                <span class="list-toolbar-meta"><?= $count ?> sale<?= $count !== 1 ? 's' : '' ?> · <?= $symbol ?> <?= money($totalAmount) ?></span>
            <?php endif; ?>
        </div>
        <div class="page-header-actions list-toolbar-actions">
            <a href="<?= url('sales/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Record
            </a>
            <a href="<?= url('sales/history') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history me-1"></i> History
            </a>
        </div>
    </div>

    <?php if ($count === 0): ?>
        <div class="table-card">
            <div class="empty-state">
                <i class="bi bi-receipt"></i>
                <p>No sales recorded today.</p>
                <a href="<?= url('sales/create') ?>" class="btn btn-primary btn-sm">Record Sale</a>
            </div>
        </div>
    <?php else: ?>
        <div class="report-mobile-list d-lg-none">
            <?php foreach ($sales as $i => $s): ?>
                <div class="report-item-card transaction-card">
                    <div class="report-item-top">
                        <div class="transaction-item-main">
                            <span class="transaction-item-no">#<?= $i + 1 ?></span>
                            <strong class="report-item-title"><?= e($s['item_name']) ?></strong>
                        </div>
                        <span class="report-item-amount"><?= $symbol ?> <?= money($s['total_price']) ?></span>
                    </div>
                    <div class="transaction-detail-grid">
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">Qty</span>
                            <span><?= money($s['quantity']) ?></span>
                        </div>
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">Unit Price</span>
                            <span><?= $symbol ?> <?= money($s['unit_price']) ?></span>
                        </div>
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">By</span>
                            <span><?= e($s['user_name']) ?></span>
                        </div>
                    </div>
                    <?php if (!empty($s['notes'])): ?>
                        <div class="transaction-notes"><i class="bi bi-chat-left-text me-1"></i><?= e($s['notes']) ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="table-card d-none d-lg-block">
            <div class="table-responsive-wrap">
                <table class="table table-striped data-table-lg mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>By</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $i => $s): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><strong><?= e($s['item_name']) ?></strong></td>
                                <td><?= money($s['quantity']) ?></td>
                                <td><?= money($s['unit_price']) ?></td>
                                <td><strong><?= money($s['total_price']) ?></strong></td>
                                <td><?= e($s['user_name']) ?></td>
                                <td><?= e($s['notes'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
