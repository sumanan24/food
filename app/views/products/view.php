<?php use Core\Security; ?>
<div class="card shadow-sm col-lg-6">
    <div class="card-body">
        <h5><?= Security::escape($product['name']) ?></h5>
        <table class="table">
            <tr><th>Category</th><td><?= Security::escape($product['category_name']) ?></td></tr>
            <tr><th>Buying Price</th><td><?= number_format($product['buying_price'], 2) ?></td></tr>
            <tr><th>Selling Price</th><td><?= number_format($product['selling_price'], 2) ?></td></tr>
            <tr><th>Quantity</th><td><?= $product['quantity'] ?></td></tr>
            <tr><th>Barcode</th><td><?= Security::escape($product['barcode'] ?? '-') ?></td></tr>
            <tr><th>Expiry</th><td><?= $product['expiry_date'] ?? '-' ?></td></tr>
            <tr><th>Created</th><td><?= $product['created_at'] ?></td></tr>
        </table>
        <a href="<?= url('products/edit/' . $product['id']) ?>" class="btn btn-primary">Edit</a>
        <a href="<?= url('products') ?>" class="btn btn-secondary">Back</a>
    </div>
</div>
