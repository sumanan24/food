<div class="page-header">
    <h1 class="h3 mb-0">Today's Purchases</h1>
    <div class="page-header-actions">
        <a href="<?= url('purchases/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Record
        </a>
        <a href="<?= url('purchases/history') ?>" class="btn btn-outline-secondary">History</a>
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
                    <th>Unit Cost</th>
                    <th>Total</th>
                    <th>By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($purchases as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= e($p['item_name']) ?></strong></td>
                        <td><?= money($p['quantity']) ?></td>
                        <td><?= money($p['unit_cost']) ?></td>
                        <td><strong><?= money($p['total_cost']) ?></strong></td>
                        <td><?= e($p['user_name']) ?></td>
                        <td><?= e($p['notes'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
