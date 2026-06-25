<?php $config = require CONFIG_PATH . '/app.php'; $symbol = $config['currency_symbol'] ?? 'Rs.'; ?>

<div class="transaction-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-boxes me-1"></i> Long Use Inventory</span>
            <strong class="list-toolbar-value"><?= e($start) ?> — <?= e($end) ?></strong>
        </div>
    </div>

    <div class="table-card filter-card mb-3">
        <form method="GET" action="<?= url('inventory') ?>" class="list-filters-row row g-2 align-items-end">
            <div class="col-6 col-md-3"><label class="form-label">From</label><input type="date" name="start" class="form-control" value="<?= e($start) ?>"></div>
            <div class="col-6 col-md-3"><label class="form-label">To</label><input type="date" name="end" class="form-control" value="<?= e($end) ?>"></div>
            <div class="col-12 col-md-auto"><button type="submit" class="btn btn-primary w-100">View Report</button></div>
        </form>
    </div>

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
                        <th>Current Stock</th>
                        <th>Valuation</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report as $row): ?>
                        <tr class="<?= $row['low_stock'] ? 'table-warning' : '' ?>">
                            <td><strong><?= e($row['item_name']) ?></strong><?php if ($row['low_stock']): ?> <span class="stock-badge low">Low</span><?php endif; ?></td>
                            <td><?= money($row['opening_stock']) ?></td>
                            <td><?= money($row['purchased']) ?></td>
                            <td><?= money($row['sold']) ?></td>
                            <td><?= money($row['wastage']) ?></td>
                            <td><?= money($row['current_stock']) ?></td>
                            <td><?= $symbol ?> <?= money($row['stock_valuation']) ?></td>
                            <td><a href="<?= url('inventory/ledger/' . $row['item_id'] . '?start=' . urlencode($start) . '&end=' . urlencode($end)) ?>" class="btn btn-sm btn-outline-primary">Ledger</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
