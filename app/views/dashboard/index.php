<?php use Core\Security;
$config = require dirname(__DIR__, 3) . '/config/app.php';
$cur = $config['currency'];
?>
<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['Today Sales', $todaySales, 'bi-cash-stack', 'primary'],
        ['Today Expenses', $todayExpenses, 'bi-wallet2', 'danger'],
        ['Today Purchases', $todayPurchases, 'bi-truck', 'warning'],
        ['Today Profit', $todayProfit, 'bi-graph-up-arrow', 'success'],
        ['Total Products', $totalProducts, 'bi-box-seam', 'info'],
        ['Low Stock', $lowStock, 'bi-exclamation-triangle', 'danger'],
    ];
    foreach ($cards as [$label, $val, $icon, $color]): ?>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small mb-1"><?= $label ?></p>
                        <h4 class="mb-0"><?= is_numeric($val) && $label !== 'Total Products' && $label !== 'Low Stock' ? $cur . number_format((float)$val, 2) : (int)$val ?></h4>
                    </div>
                    <div class="stat-icon bg-<?= $color ?>-subtle text-<?= $color ?>"><i class="bi <?= $icon ?>"></i></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm"><div class="card-body">
            <h6>Weekly Income</h6>
            <h3 class="text-primary"><?= $cur . number_format($weeklyIncome, 2) ?></h3>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm"><div class="card-body">
            <h6>Monthly Income</h6>
            <h3 class="text-success"><?= $cur . number_format($monthlyIncome, 2) ?></h3>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm"><div class="card-body">
            <h6>Yearly Income</h6>
            <h3 class="text-info"><?= $cur . number_format($yearlyIncome, 2) ?></h3>
        </div></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Income Overview</span>
                <select id="chartPeriod" class="form-select form-select-sm w-auto">
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <div class="card-body"><canvas id="incomeChart" height="120"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">Top Selling Products</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (empty($topProducts)): ?>
                    <li class="list-group-item text-muted">No sales data yet</li>
                    <?php else: foreach ($topProducts as $p): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= Security::escape($p['product_name']) ?></span>
                        <span class="badge bg-primary"><?= (int)$p['total_qty'] ?> sold</span>
                    </li>
                    <?php endforeach; endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php
$pageScript = '<script>
document.addEventListener("DOMContentLoaded", function() {
    let chart;
    function loadChart(period) {
        (window.apiFetch || fetch)(APP_URL + "/api/dashboard/stats?period=" + period)
            .then(r => r.json())
            .then(res => {
                const ctx = document.getElementById("incomeChart");
                if (chart) chart.destroy();
                chart = new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: res.chart.labels,
                        datasets: [
                            { label: "Sales", data: res.chart.sales, borderColor: "#0d6efd", tension: 0.3 },
                            { label: "Expenses", data: res.chart.expenses, borderColor: "#dc3545", tension: 0.3 }
                        ]
                    },
                    options: { responsive: true, plugins: { legend: { position: "bottom" } } }
                });
            });
    }
    loadChart("weekly");
    document.getElementById("chartPeriod").addEventListener("change", e => loadChart(e.target.value));
});
</script>';
?>
