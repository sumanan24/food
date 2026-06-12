<div class="page-header">
    <h1 class="h3 mb-0">Sales History</h1>
    <a href="<?= url('sales') ?>" class="btn btn-outline-secondary">Today</a>
</div>

<div class="table-card">
    <form method="GET" action="<?= url('sales/history') ?>" class="row g-2 mb-3">
        <div class="col-auto">
            <input type="date" name="date" class="form-control" value="<?= e($filterDate ?? '') ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="<?= url('sales/history') ?>" class="btn btn-outline-secondary">Clear</a>
        </div>
    </form>

    <table class="table table-striped data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
                <th>Recorded By</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sales as $i => $s): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($s['sale_date']) ?></td>
                    <td><?= e($s['item_name']) ?></td>
                    <td><?= money($s['quantity']) ?></td>
                    <td><?= money($s['unit_price']) ?></td>
                    <td><?= money($s['total_price']) ?></td>
                    <td><?= e($s['user_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
