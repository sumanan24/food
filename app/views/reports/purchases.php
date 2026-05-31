<?php use Core\Security; include __DIR__ . '/_filter.php'; ?>
<div class="card shadow-sm">
    <div class="card-header">Total Purchases: <?= number_format($total, 2) ?></div>
    <table class="table"><thead><tr><th>Date</th><th>Supplier</th><th>Product</th><th>Qty</th><th>Total</th></tr></thead>
    <tbody><?php foreach ($purchases as $p): ?><tr><td><?= $p['purchase_date'] ?></td><td><?= Security::escape($p['supplier_name']) ?></td><td><?= Security::escape($p['product_name']) ?></td><td><?= $p['quantity'] ?></td><td><?= number_format($p['total_cost'],2) ?></td></tr><?php endforeach; ?></tbody></table>
</div>
