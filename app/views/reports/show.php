<?php $config = require CONFIG_PATH . '/app.php'; $symbol = $config['currency_symbol'] ?? 'Rs.'; ?>

<div class="page-header">
    <h1 class="h3 mb-0"><?= e($report['title']) ?></h1>
    <div>
        <a href="<?= url('reports/pdf?type=' . $type . '&filter=' . urlencode($filter)) ?>" class="btn btn-danger me-2" target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
        </a>
        <a href="<?= url('reports') ?>" class="btn btn-outline-secondary">Back</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Sales</div>
                <div class="h4 text-success mb-0"><?= $symbol ?> <?= money($report['total_sales']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Purchases</div>
                <div class="h4 text-primary mb-0"><?= $symbol ?> <?= money($report['total_purchases']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Expenses</div>
                <div class="h4 text-warning mb-0"><?= $symbol ?> <?= money($report['total_expenses']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Net Profit</div>
                <div class="h4 mb-0 <?= $report['profit'] >= 0 ? 'text-success' : 'text-danger' ?>">
                    <?= $symbol ?> <?= money($report['profit']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-card mb-4">
    <h5>Sales</h5>
    <table class="table table-striped data-table">
        <thead>
            <tr><th>Date</th><th>Item</th><th>Qty</th><th>Total</th><th>By</th></tr>
        </thead>
        <tbody>
            <?php foreach ($report['sales'] as $s): ?>
                <tr>
                    <td><?= e($s['sale_date']) ?></td>
                    <td><?= e($s['item_name']) ?></td>
                    <td><?= money($s['quantity']) ?></td>
                    <td><?= money($s['total_price']) ?></td>
                    <td><?= e($s['user_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="table-card mb-4">
    <h5>Purchases</h5>
    <table class="table table-striped data-table">
        <thead>
            <tr><th>Date</th><th>Item</th><th>Qty</th><th>Total</th><th>By</th></tr>
        </thead>
        <tbody>
            <?php foreach ($report['purchases'] as $p): ?>
                <tr>
                    <td><?= e($p['purchase_date']) ?></td>
                    <td><?= e($p['item_name']) ?></td>
                    <td><?= money($p['quantity']) ?></td>
                    <td><?= money($p['total_cost']) ?></td>
                    <td><?= e($p['user_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="table-card">
    <h5>Expenses</h5>
    <table class="table table-striped data-table">
        <thead>
            <tr><th>Date</th><th>Title</th><th>Category</th><th>Amount</th><th>By</th></tr>
        </thead>
        <tbody>
            <?php foreach ($report['expenses'] as $e): ?>
                <tr>
                    <td><?= e($e['expense_date']) ?></td>
                    <td><?= e($e['title']) ?></td>
                    <td><?= e($e['category_name']) ?></td>
                    <td><?= money($e['amount']) ?></td>
                    <td><?= e($e['user_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
