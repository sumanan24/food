<?php use App\Core\Auth; ?>

<div class="page-header">
    <h1 class="h3 mb-0">Expenses</h1>
    <a href="<?= url('expenses/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Expense
    </a>
</div>

<div class="table-card">
    <table class="table table-striped data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Title</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Recorded By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expenses as $i => $exp): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($exp['expense_date']) ?></td>
                    <td><?= e($exp['title']) ?></td>
                    <td><?= e($exp['category_name']) ?></td>
                    <td><?= money($exp['amount']) ?></td>
                    <td><?= e($exp['user_name']) ?></td>
                    <td>
                        <a href="<?= url('expenses/edit/' . $exp['id']) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if (Auth::isAdmin()): ?>
                            <form method="POST" action="<?= url('expenses/delete/' . $exp['id']) ?>" class="d-inline" onsubmit="return confirm('Delete this expense?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
