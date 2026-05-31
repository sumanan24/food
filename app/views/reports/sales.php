<?php use Core\Security; include __DIR__ . '/_filter.php'; ?>
<div class="card shadow-sm">
    <div class="card-header">Total: <?= number_format($total, 2) ?>
        <a href="<?= url('reports/export?type=sales&from=' . $from . '&to=' . $to) ?>" class="btn btn-sm btn-success float-end">Export</a>
    </div>
    <table class="table mb-0">
        <thead><tr><th>Invoice</th><th>Date</th><th>Customer</th><th>Total</th><th>Payment</th></tr></thead>
        <tbody>
        <?php foreach ($sales as $s): ?>
        <tr><td><?= Security::escape($s['invoice_number']) ?></td><td><?= $s['sale_date'] ?></td><td><?= Security::escape($s['customer_name'] ?? '-') ?></td><td><?= number_format($s['grand_total'], 2) ?></td><td><?= $s['payment_type'] ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
