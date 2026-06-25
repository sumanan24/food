<?php
use App\Core\Auth;
$config = require CONFIG_PATH . '/app.php';
$symbol = $config['currency_symbol'] ?? 'Rs.';
$totalProfit = array_sum(array_column($report, 'daily_profit'));
?>

<div class="transaction-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-egg-fried me-1"></i> Daily Food Balance</span>
            <strong class="list-toolbar-value"><?= e($date) ?></strong>
            <?php if ($totalProfit > 0): ?>
                <span class="list-toolbar-meta">Profit · <?= $symbol ?> <?= money($totalProfit) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="table-card filter-card mb-3">
        <form method="GET" action="<?= url('daily-balance') ?>" class="filter-form-mobile">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-sm"><label class="form-label">Report Date</label><input type="date" name="date" class="form-control" value="<?= e($date) ?>"></div>
                <div class="col-12 col-sm-auto"><button type="submit" class="btn btn-primary w-100">View</button></div>
            </div>
        </form>
    </div>

    <?php if (!empty($dailyItems)): ?>
    <div class="table-card form-card mb-3">
        <h5 class="mb-3"><i class="bi bi-sunrise me-1"></i> Set Opening Quantities</h5>
        <form method="POST" action="<?= url('daily-balance/opening') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="balance_date" value="<?= e($date) ?>">
            <div class="row g-2">
                <?php foreach ($dailyItems as $item): ?>
                    <?php
                    $opening = 0;
                    foreach ($report as $row) {
                        if ((int)$row['item_id'] === (int)$item['id']) { $opening = $row['opening_qty']; break; }
                    }
                    ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label"><?= e($item['name']) ?></label>
                        <input type="number" name="opening[<?= $item['id'] ?>]" class="form-control" step="0.01" min="0" value="<?= money($opening) ?>">
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="btn btn-primary mt-3 w-100 w-sm-auto">Save Opening Qty</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="table-card">
        <div class="table-responsive-wrap">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Opening</th>
                        <th>Purchased</th>
                        <th>Sold</th>
                        <th>Wastage</th>
                        <th>Balance</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report as $row): ?>
                        <tr>
                            <td><strong><?= e($row['item_name']) ?></strong></td>
                            <td><?= money($row['opening_qty']) ?></td>
                            <td><?= money($row['purchased_qty']) ?></td>
                            <td><?= money($row['sold_qty']) ?></td>
                            <td><?= money($row['wastage_qty']) ?></td>
                            <td><span class="stock-badge <?= $row['balance_qty'] <= 5 ? 'low' : 'ok' ?>"><?= money($row['balance_qty']) ?></span></td>
                            <td class="text-profit"><?= $symbol ?> <?= money($row['daily_profit']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
