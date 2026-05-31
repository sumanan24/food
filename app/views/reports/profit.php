<?php use Core\Security;
include __DIR__ . '/_filter.php';
$config = require dirname(__DIR__, 3) . '/config/app.php';
?>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="row text-center g-3 mb-4">
            <div class="col-md-3"><div class="p-3 bg-primary-subtle rounded"><h6>Total Sales</h6><h4><?= $config['currency'] . number_format($data['total_sales'], 2) ?></h4></div></div>
            <div class="col-md-3"><div class="p-3 bg-warning-subtle rounded"><h6>Product Cost</h6><h4><?= $config['currency'] . number_format($data['product_cost'], 2) ?></h4></div></div>
            <div class="col-md-3"><div class="p-3 bg-danger-subtle rounded"><h6>Expenses</h6><h4><?= $config['currency'] . number_format($data['expenses'], 2) ?></h4></div></div>
            <div class="col-md-3"><div class="p-3 bg-success-subtle rounded"><h6>Profit</h6><h4 class="text-success"><?= $config['currency'] . number_format($data['profit'], 2) ?></h4></div></div>
        </div>
        <p class="text-muted small">Formula: Profit = Total Sales - Product Buying Cost - Expenses</p>
        <div class="d-flex gap-2">
            <a href="<?= url('reports/export?type=profit&format=csv&from=' . $from . '&to=' . $to) ?>" class="btn btn-sm btn-outline-success">Export Excel/CSV</a>
            <button onclick="window.print()" class="btn btn-sm btn-outline-primary">Print</button>
        </div>
    </div>
</div>
