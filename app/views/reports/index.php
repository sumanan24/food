<?php use Core\Security; ?>
<div class="row g-3">
    <?php
    $reports = [
        ['Profit Report', 'reports/profit', 'bi-graph-up', 'success'],
        ['Sales Report', 'reports/sales', 'bi-receipt', 'primary'],
        ['Expense Report', 'reports/expenses', 'bi-wallet2', 'danger'],
        ['Purchase Report', 'reports/purchases', 'bi-truck', 'warning'],
        ['Stock Report', 'reports/stock', 'bi-box-seam', 'info'],
    ];
    foreach ($reports as [$title, $url, $icon, $color]): ?>
    <div class="col-md-4 col-lg-3">
        <a href="<?= url($url) ?>" class="card text-decoration-none shadow-sm h-100 report-card">
            <div class="card-body text-center">
                <i class="bi <?= $icon ?> display-4 text-<?= $color ?>"></i>
                <h6 class="mt-2 text-dark"><?= $title ?></h6>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>
