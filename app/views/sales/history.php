<?php
$config = require CONFIG_PATH . '/app.php';
$symbol = $config['currency_symbol'] ?? 'Rs.';
$count = count($sales);
$totalAmount = array_sum(array_map(fn($s) => (float) $s['total_price'], $sales));
$filterLabel = $filterDate ? e($filterDate) : 'All dates';
?>

<div class="transaction-page sales-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-clock-history me-1"></i> Sales History</span>
            <strong class="list-toolbar-value"><?= $filterLabel ?></strong>
            <?php if ($count > 0): ?>
                <span class="list-toolbar-meta"><?= $count ?> record<?= $count !== 1 ? 's' : '' ?> · <?= $symbol ?> <?= money($totalAmount) ?></span>
            <?php endif; ?>
        </div>
        <div class="page-header-actions list-toolbar-actions">
            <a href="<?= url('sales') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-calendar-day me-1"></i> Today
            </a>
        </div>
    </div>

    <div class="table-card filter-card mb-3">
        <div class="list-filters-head">
            <span class="list-filters-title"><i class="bi bi-calendar3 me-1"></i> Filter by date</span>
        </div>
        <form method="GET" action="<?= url('sales/history') ?>" class="filter-form-mobile">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-sm">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="<?= e($filterDate ?? '') ?>">
                </div>
                <div class="col-12 col-sm-auto">
                    <label class="form-label d-none d-sm-block">&nbsp;</label>
                    <div class="filter-form-actions">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="<?= url('sales/history') ?>" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php if ($count === 0): ?>
        <div class="table-card">
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p>No sales found<?= $filterDate ? ' for this date' : '' ?>.</p>
            </div>
        </div>
    <?php else: ?>
        <div data-filter-scope="sales-history">
        <?php
        $filterScope = 'sales-history';
        $searchPlaceholder = 'Search by item, user, date...';
        require VIEW_PATH . '/partials/list-filters.php';
        ?>
        <div class="report-mobile-list d-lg-none" data-filter-mobile>
            <?php foreach ($sales as $i => $s): ?>
                <div class="report-item-card transaction-card" data-filter-item data-filter-date="<?= e($s['sale_date']) ?>">
                    <div class="report-item-top">
                        <div class="transaction-item-main">
                            <span class="transaction-item-no">#<?= $i + 1 ?></span>
                            <strong class="report-item-title"><?= e($s['item_name']) ?></strong>
                        </div>
                        <span class="report-item-amount"><?= $symbol ?> <?= money($s['total_price']) ?></span>
                    </div>
                    <div class="transaction-detail-grid">
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">Date</span>
                            <span><?= e($s['sale_date']) ?></span>
                        </div>
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">Qty</span>
                            <span><?= money($s['quantity']) ?></span>
                        </div>
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">Unit</span>
                            <span><?= $symbol ?> <?= money($s['unit_price']) ?></span>
                        </div>
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">By</span>
                            <span><?= e($s['user_name']) ?></span>
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
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $i => $s): ?>
                            <tr data-filter-item data-filter-date="<?= e($s['sale_date']) ?>">
                                <td><?= $i + 1 ?></td>
                                <td><?= e($s['sale_date']) ?></td>
                                <td><strong><?= e($s['item_name']) ?></strong></td>
                                <td><?= money($s['quantity']) ?></td>
                                <td><?= money($s['unit_price']) ?></td>
                                <td><strong><?= money($s['total_price']) ?></strong></td>
                                <td><?= e($s['user_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-card d-none" data-filter-empty>
            <div class="empty-state py-3"><p class="mb-0">No sales match your filters.</p></div>
        </div>
        </div>
    <?php endif; ?>
</div>
