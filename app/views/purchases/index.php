<div class="page-header">
    <h1 class="h3 mb-0">Today's Purchases</h1>
    <div>
        <a href="<?= url('purchases/create') ?>" class="btn btn-primary me-2">
            <i class="bi bi-plus-lg me-1"></i> Record Purchase
        </a>
        <a href="<?= url('purchases/history') ?>" class="btn btn-outline-secondary">History</a>
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
                <th>Unit Cost</th>
                <th>Total</th>
                <th>Recorded By</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($purchases as $i => $p): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($p['item_name']) ?></td>
                    <td><?= money($p['quantity']) ?></td>
                    <td><?= money($p['unit_cost']) ?></td>
                    <td><?= money($p['total_cost']) ?></td>
                    <td><?= e($p['user_name']) ?></td>
                    <td><?= e($p['notes'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
