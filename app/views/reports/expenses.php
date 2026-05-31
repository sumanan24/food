<?php use Core\Security; include __DIR__ . '/_filter.php'; ?>
<div class="card shadow-sm">
    <div class="card-header">Total Expenses: <?= number_format($total, 2) ?></div>
    <table class="table"><thead><tr><th>Title</th><th>Category</th><th>Amount</th><th>Date</th></tr></thead>
    <tbody><?php foreach ($expenses as $e): ?><tr><td><?= Security::escape($e['title']) ?></td><td><?= Security::escape($e['category_name'] ?? '') ?></td><td><?= number_format($e['amount'],2) ?></td><td><?= $e['expense_date'] ?></td></tr><?php endforeach; ?></tbody></table>
</div>
