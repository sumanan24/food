<?php
use App\Core\Auth;
$symbol = $config['currency_symbol'] ?? 'Rs.';
?>

<div class="dashboard-page">
<div class="dashboard-hero animate-in">
    <div class="dashboard-hero-content">
        <h2>Hello, <?= e(explode(' ', Auth::user()['name'] ?? 'User')[0]) ?> 👋</h2>
        <p>Here's what's happening with your shop today.</p>
        <div class="quick-actions">
            <a href="<?= url('pos') ?>" class="quick-action-btn"><i class="bi bi-receipt-cutoff"></i> POS Billing</a>
            <a href="<?= url('purchases/create') ?>" class="quick-action-btn"><i class="bi bi-cart-plus"></i> Purchase</a>
            <a href="<?= url('cash') ?>" class="quick-action-btn"><i class="bi bi-cash-stack"></i> Bill Counter</a>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card stat-card stat-sales">
            <div class="card-body">
                <div class="stat-icon d-none d-sm-flex"><i class="bi bi-cash-coin"></i></div>
                <div>
                    <div class="stat-label">Today Sales</div>
                    <div class="stat-value"><?= $symbol ?> <?= money($summary['today_sales']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card stat-purchases">
            <div class="card-body">
                <div class="stat-icon d-none d-sm-flex"><i class="bi bi-cart-plus"></i></div>
                <div>
                    <div class="stat-label">Purchases</div>
                    <div class="stat-value"><?= $symbol ?> <?= money($summary['today_purchases']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card stat-expenses">
            <div class="card-body">
                <div class="stat-icon d-none d-sm-flex"><i class="bi bi-wallet2"></i></div>
                <div>
                    <div class="stat-label">Expenses</div>
                    <div class="stat-value"><?= $symbol ?> <?= money($summary['today_expenses']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card stat-profit">
            <div class="card-body">
                <div class="stat-icon d-none d-sm-flex"><i class="bi bi-graph-up-arrow"></i></div>
                <div>
                    <div class="stat-label">Today Profit</div>
                    <div class="stat-value <?= $summary['today_profit'] >= 0 ? 'text-profit' : 'text-loss' ?>">
                        <?= $symbol ?> <?= money($summary['today_profit']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card stat-card stat-sales">
            <div class="card-body">
                <div class="stat-icon d-none d-sm-flex"><i class="bi bi-cash-stack"></i></div>
                <div>
                    <div class="stat-label">Cash in Hand</div>
                    <div class="stat-value"><?= $symbol ?> <?= money($summary['cash_in_hand']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card stat-purchases">
            <div class="card-body">
                <div class="stat-icon d-none d-sm-flex"><i class="bi bi-calendar-month"></i></div>
                <div>
                    <div class="stat-label">Monthly Profit</div>
                    <div class="stat-value <?= $summary['monthly_profit'] >= 0 ? 'text-profit' : 'text-loss' ?>">
                        <?= $symbol ?> <?= money($summary['monthly_profit']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="quick-actions">
            <a href="<?= url('pos') ?>" class="quick-action-btn"><i class="bi bi-receipt-cutoff"></i> POS Billing</a>
            <a href="<?= url('cash') ?>" class="quick-action-btn"><i class="bi bi-cash-stack"></i> Bill Counter</a>
            <a href="<?= url('daily-balance') ?>" class="quick-action-btn"><i class="bi bi-egg-fried"></i> Daily Balance</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="table-card">
            <h5 class="card-title"><i class="bi bi-bar-chart-line"></i> Last 7 Days Overview</h5>
            <div class="chart-container">
                <canvas id="overviewChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="table-card h-100">
            <h5 class="card-title"><i class="bi bi-exclamation-triangle"></i> Low Stock Alert</h5>
            <?php if (empty($summary['low_stock_items'])): ?>
                <div class="empty-state">
                    <i class="bi bi-check-circle"></i>
                    <p class="mb-0">All items are well stocked!</p>
                </div>
            <?php else: ?>
                <?php foreach ($summary['low_stock_items'] as $item): ?>
                    <div class="stock-list-item">
                        <span><?= e($item['name']) ?></span>
                        <span class="stock-badge low"><?= money($item['current_stock']) ?> left</span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('overviewChart');
    if (!ctx || typeof Chart === 'undefined') return;

    Chart.defaults.font.family = "'DM Sans', sans-serif";
    Chart.defaults.color = '#718096';

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($charts['labels']) ?>,
            datasets: [
                {
                    label: 'Sales',
                    data: <?= json_encode($charts['sales']) ?>,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.08)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointHoverRadius: 6
                },
                {
                    label: 'Purchases',
                    data: <?= json_encode($charts['purchases']) ?>,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.08)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointHoverRadius: 6
                },
                {
                    label: 'Expenses',
                    data: <?= json_encode($charts['expenses']) ?>,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(255, 209, 102, 0.08)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 16, font: { weight: '600', size: 12 } }
                }
            },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, border: { display: false } }
            }
        }
    });
});
</script>
</div>
