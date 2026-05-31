<?php use Core\Security; ?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between">
        <span>Product Stock Report</span>
        <button onclick="window.print()" class="btn btn-sm btn-primary">Print</button>
    </div>
    <table class="table">
        <thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Buy</th><th>Sell</th><th>Qty</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($products as $p): ?>
        <tr><td><?= $p['id'] ?></td><td><?= Security::escape($p['name']) ?></td><td><?= Security::escape($p['category_name'] ?? '') ?></td><td><?= number_format($p['buying_price'],2) ?></td><td><?= number_format($p['selling_price'],2) ?></td><td><?= $p['quantity'] ?></td><td><?= $p['status']?'Active':'Inactive' ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
