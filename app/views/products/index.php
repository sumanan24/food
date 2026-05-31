<?php use Core\Security;
$config = require dirname(__DIR__, 3) . '/config/app.php';
$threshold = (int)$config['low_stock_threshold'];
?>
<div class="card shadow-sm">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <form class="d-flex flex-wrap gap-2" method="get">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="<?= Security::escape($filters['search']) ?>" id="productSearch">
            <select name="category_id" class="form-select form-select-sm">
                <option value="">All Categories</option>
                <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $filters['category_id'] == $c['id'] ? 'selected' : '' ?>><?= Security::escape($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            <a href="<?= url('products?low_stock=1') ?>" class="btn btn-sm btn-warning">Low Stock</a>
        </form>
        <a href="<?= url('products/create') ?>" class="btn btn-sm btn-success"><i class="bi bi-plus"></i> Add Product</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>ID</th><th>Name</th><th>Category</th><th>Buy</th><th>Sell</th><th>Qty</th><th>Barcode</th><th>Status</th><th>Actions</th>
            </tr></thead>
            <tbody>
            <?php foreach ($products as $p): ?>
            <tr class="<?= $p['quantity'] <= $threshold ? 'table-warning' : '' ?>">
                <td><?= $p['id'] ?></td>
                <td><?= Security::escape($p['name']) ?></td>
                <td><?= Security::escape($p['category_name'] ?? '-') ?></td>
                <td><?= number_format($p['buying_price'], 2) ?></td>
                <td><?= number_format($p['selling_price'], 2) ?></td>
                <td><span class="badge bg-<?= $p['quantity'] <= $threshold ? 'danger' : 'secondary' ?>"><?= $p['quantity'] ?></span></td>
                <td><?= Security::escape($p['barcode'] ?? '-') ?></td>
                <td><?= $p['status'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td>
                <td>
                    <a href="<?= url('products/view/' . $p['id']) ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                    <a href="<?= url('products/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form method="post" action="<?= url('products/delete/' . $p['id']) ?>" class="d-inline" onsubmit="return confirmDelete(this)">
                        <?= Security::csrfField() ?>
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
