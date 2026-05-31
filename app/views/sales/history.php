<?php use Core\Security; ?>
<div class="card shadow-sm">
    <div class="card-header">
        <form method="get" class="row g-2">
            <div class="col-auto"><input type="date" name="from" class="form-control form-control-sm" value="<?= Security::escape($filters['from']) ?>"></div>
            <div class="col-auto"><input type="date" name="to" class="form-control form-control-sm" value="<?= Security::escape($filters['to']) ?>"></div>
            <div class="col-auto"><input type="text" name="search" class="form-control form-control-sm" placeholder="Invoice / Customer" value="<?= Security::escape($filters['search']) ?>"></div>
            <div class="col-auto"><button class="btn btn-sm btn-primary">Filter</button></div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Invoice</th><th>Date</th><th>Customer</th><th>Total</th><th>Payment</th><th>User</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($sales as $s): ?>
            <tr>
                <td><?= Security::escape($s['invoice_number']) ?></td>
                <td><?= $s['sale_date'] ?></td>
                <td><?= Security::escape($s['customer_name'] ?? '-') ?></td>
                <td><?= number_format($s['grand_total'], 2) ?></td>
                <td><?= ucfirst($s['payment_type']) ?></td>
                <td><?= Security::escape($s['user_name'] ?? '-') ?></td>
                <td>
                    <a href="<?= url('sales/view/' . $s['id']) ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                    <a href="<?= url('sales/print/' . $s['id']) ?>" class="btn btn-sm btn-outline-primary" target="_blank"><i class="bi bi-printer"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
