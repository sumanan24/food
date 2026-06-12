<?php $symbol = $config['currency_symbol'] ?? 'Rs.'; ?>

<div class="page-header">
    <h1 class="h3 mb-0">Dashboard</h1>
    <span class="text-muted"><?= date('l, F j, Y') ?></span>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <div class="text-muted small">Today Sales</div>
                    <div class="h4 mb-0"><?= $symbol ?> <?= money($summary['today_sales']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="bi bi-cart-plus"></i>
                </div>
                <div>
                    <div class="text-muted small">Today Purchases</div>
                    <div class="h4 mb-0"><?= $symbol ?> <?= money($summary['today_purchases']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div>
                    <div class="text-muted small">Today Expenses</div>
                    <div class="h4 mb-0"><?= $symbol ?> <?= money($summary['today_expenses']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div>
                    <div class="text-muted small">Today Profit</div>
                    <div class="h4 mb-0 <?= $summary['today_profit'] >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= $symbol ?> <?= money($summary['today_profit']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="table-card">
            <h5 class="mb-3">Last 7 Days Overview</h5>
            <canvas id="overviewChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="table-card h-100">
            <h5 class="mb-3">Low Stock Alert</h5>
            <?php if (empty($summary['low_stock_items'])): ?>
                <p class="text-muted mb-0">All items are well stocked.</p>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($summary['low_stock_items'] as $item): ?>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span><?= e($item['name']) ?></span>
                            <span class="badge bg-danger"><?= money($item['current_stock']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('overviewChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($charts['labels']) ?>,
            datasets: [
                {
                    label: 'Sales',
                    data: <?= json_encode($charts['sales']) ?>,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Purchases',
                    data: <?= json_encode($charts['purchases']) ?>,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Expenses',
                    data: <?= json_encode($charts['expenses']) ?>,
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>
