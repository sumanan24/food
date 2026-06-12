<div class="page-header">
    <h1 class="h3 mb-0">Today's Sales</h1>
    <div>
        <a href="<?= url('sales/create') ?>" class="btn btn-primary me-2">
            <i class="bi bi-plus-lg me-1"></i> Record Sale
        </a>
        <a href="<?= url('sales/history') ?>" class="btn btn-outline-secondary">History</a>
    </div>
</div>

<div class="table-card">
    <p class="text-muted">Date: <strong><?= e($date) ?></strong></p>
    <table class="table table-striped data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
                <th>Recorded By</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sales as $i => $s): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($s['item_name']) ?></td>
                    <td><?= money($s['quantity']) ?></td>
                    <td><?= money($s['unit_price']) ?></td>
                    <td><?= money($s['total_price']) ?></td>
                    <td><?= e($s['user_name']) ?></td>
                    <td><?= e($s['notes'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
