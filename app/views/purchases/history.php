<div class="page-header">
    <h1 class="h3 mb-0">Purchase History</h1>
    <a href="<?= url('purchases') ?>" class="btn btn-outline-secondary">Today</a>
</div>

<div class="table-card">
    <form method="GET" action="<?= url('purchases/history') ?>" class="row g-2 mb-3">
        <div class="col-auto">
            <input type="date" name="date" class="form-control" value="<?= e($filterDate ?? '') ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="<?= url('purchases/history') ?>" class="btn btn-outline-secondary">Clear</a>
        </div>
    </form>

    <table class="table table-striped data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Unit Cost</th>
                <th>Total</th>
                <th>Recorded By</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($purchases as $i => $p): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($p['purchase_date']) ?></td>
                    <td><?= e($p['item_name']) ?></td>
                    <td><?= money($p['quantity']) ?></td>
                    <td><?= money($p['unit_cost']) ?></td>
                    <td><?= money($p['total_cost']) ?></td>
                    <td><?= e($p['user_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
