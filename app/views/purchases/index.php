<?php use Core\Security; ?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between">
        <form method="get" class="d-flex gap-2">
            <input type="date" name="from" class="form-control form-control-sm" value="<?= Security::escape($filters['from']) ?>">
            <input type="date" name="to" class="form-control form-control-sm" value="<?= Security::escape($filters['to']) ?>">
            <button class="btn btn-sm btn-primary">Filter</button>
        </form>
        <a href="<?= url('purchases/create') ?>" class="btn btn-sm btn-success">Add Purchase</a>
    </div>
    <table class="table">
        <thead><tr><th>Date</th><th>Supplier</th><th>Product</th><th>Qty</th><th>Cost</th><th>Total</th><th>Invoice</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($purchases as $p): ?>
        <tr>
            <td><?= $p['purchase_date'] ?></td>
            <td><?= Security::escape($p['supplier_name']) ?></td>
            <td><?= Security::escape($p['product_name']) ?></td>
            <td><?= $p['quantity'] ?></td>
            <td><?= number_format($p['buying_cost'], 2) ?></td>
            <td><?= number_format($p['total_cost'], 2) ?></td>
            <td><?= Security::escape($p['invoice_number'] ?? '-') ?></td>
            <td>
                <a href="<?= url('purchases/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <form method="post" action="<?= url('purchases/delete/' . $p['id']) ?>" class="d-inline" onsubmit="return confirmDelete(this)"><?= Security::csrfField() ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
