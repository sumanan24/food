<?php use Core\Security; ?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between flex-wrap gap-2">
        <form method="get" class="d-flex gap-2 flex-wrap">
            <input type="date" name="from" class="form-control form-control-sm" value="<?= Security::escape($filters['from']) ?>">
            <input type="date" name="to" class="form-control form-control-sm" value="<?= Security::escape($filters['to']) ?>">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search" value="<?= Security::escape($filters['search']) ?>">
            <button class="btn btn-sm btn-primary">Filter</button>
        </form>
        <a href="<?= url('expenses/create') ?>" class="btn btn-sm btn-success">Add Expense</a>
    </div>
    <table class="table">
        <thead><tr><th>Title</th><th>Category</th><th>Amount</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($expenses as $e): ?>
        <tr>
            <td><?= Security::escape($e['title']) ?></td>
            <td><?= Security::escape($e['category_name'] ?? '-') ?></td>
            <td><?= number_format($e['amount'], 2) ?></td>
            <td><?= $e['expense_date'] ?></td>
            <td>
                <a href="<?= url('expenses/edit/' . $e['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <form method="post" action="<?= url('expenses/delete/' . $e['id']) ?>" class="d-inline" onsubmit="return confirmDelete(this)"><?= Security::csrfField() ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
