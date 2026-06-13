<div class="page-header">
    <h1 class="h3 mb-0">Today's Sales</h1>
    <div class="page-header-actions">
        <a href="<?= url('sales/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Record
        </a>
        <a href="<?= url('sales/history') ?>" class="btn btn-outline-secondary">History</a>
    </div>
</div>

<div class="table-card">
    <p class="text-muted mb-3"><i class="bi bi-calendar3 me-1"></i> <?= e($date) ?></p>
    <div class="table-responsive-wrap">
        <table class="table table-striped data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $i => $s): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= e($s['item_name']) ?></strong></td>
                        <td><?= money($s['quantity']) ?></td>
                        <td><?= money($s['unit_price']) ?></td>
                        <td><strong><?= money($s['total_price']) ?></strong></td>
                        <td><?= e($s['user_name']) ?></td>
                        <td><?= e($s['notes'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
