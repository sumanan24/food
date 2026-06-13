<?php
$config = require CONFIG_PATH . '/app.php';
$symbol = $config['currency_symbol'] ?? 'Rs.';
$count = count($purchases);
$totalAmount = array_sum(array_map(fn($p) => (float) $p['total_cost'], $purchases));
$filterLabel = $filterDate ? e($filterDate) : 'All dates';
?>

<div class="transaction-page purchases-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-clock-history me-1"></i> Purchase History</span>
            <strong class="list-toolbar-value"><?= $filterLabel ?></strong>
            <?php if ($count > 0): ?>
                <span class="list-toolbar-meta"><?= $count ?> record<?= $count !== 1 ? 's' : '' ?> · <?= $symbol ?> <?= money($totalAmount) ?></span>
            <?php endif; ?>
        </div>
        <div class="page-header-actions list-toolbar-actions">
            <a href="<?= url('purchases') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-calendar-day me-1"></i> Today
            </a>
        </div>
    </div>

    <div class="table-card filter-card mb-3">
        <form method="GET" action="<?= url('purchases/history') ?>" class="filter-form-mobile">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-sm-auto flex-grow-1">
                    <label class="form-label">Filter by date</label>
                    <input type="date" name="date" class="form-control" value="<?= e($filterDate ?? '') ?>">
                </div>
                <div class="col-12 col-sm-auto">
                    <div class="filter-form-actions">
                        <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                        <a href="<?= url('purchases/history') ?>" class="btn btn-outline-secondary flex-fill">Clear</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php if ($count === 0): ?>
        <div class="table-card">
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p>No purchases found<?= $filterDate ? ' for this date' : '' ?>.</p>
            </div>
        </div>
    <?php else: ?>
        <div data-filter-scope="purchases-history">
        <?php
        $filterScope = 'purchases-history';
        $searchPlaceholder = 'Search by item, user, date...';
        require VIEW_PATH . '/partials/list-filters.php';
        ?>
        <div class="report-mobile-list d-lg-none" data-filter-mobile>
            <?php foreach ($purchases as $i => $p): ?>
                <div class="report-item-card transaction-card" data-filter-item data-filter-date="<?= e($p['purchase_date']) ?>">
                    <div class="report-item-top">
                        <div class="transaction-item-main">
                            <span class="transaction-item-no">#<?= $i + 1 ?></span>
                            <strong class="report-item-title"><?= e($p['item_name']) ?></strong>
                        </div>
                        <span class="report-item-amount"><?= $symbol ?> <?= money($p['total_cost']) ?></span>
                    </div>
                    <div class="transaction-detail-grid">
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">Date</span>
                            <span><?= e($p['purchase_date']) ?></span>
                        </div>
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">Qty</span>
                            <span><?= money($p['quantity']) ?></span>
                        </div>
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">Unit</span>
                            <span><?= $symbol ?> <?= money($p['unit_cost']) ?></span>
                        </div>
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">By</span>
                            <span><?= e($p['user_name']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="table-card d-none d-lg-block">
            <div class="table-responsive-wrap">
                <table class="table table-striped data-table-lg mb-0" data-filter-table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Unit Cost</th>
                            <th>Total</th>
                            <th>By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchases as $i => $p): ?>
                            <tr data-filter-item data-filter-date="<?= e($p['purchase_date']) ?>">
                                <td><?= $i + 1 ?></td>
                                <td><?= e($p['purchase_date']) ?></td>
                                <td><strong><?= e($p['item_name']) ?></strong></td>
                                <td><?= money($p['quantity']) ?></td>
                                <td><?= money($p['unit_cost']) ?></td>
                                <td><strong><?= money($p['total_cost']) ?></strong></td>
                                <td><?= e($p['user_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-card d-none" data-filter-empty>
            <div class="empty-state py-3"><p class="mb-0">No purchases match your filters.</p></div>
        </div>
        </div>
    <?php endif; ?>
</div>
