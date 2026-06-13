<?php
use App\Core\Auth;
$config = require CONFIG_PATH . '/app.php';
$symbol = $config['currency_symbol'] ?? 'Rs.';
$count = count($expenses);
$totalAmount = array_sum(array_map(fn($e) => (float) $e['amount'], $expenses));
$expenseCategoryOptions = ['' => 'All categories'];
foreach (array_unique(array_column($expenses, 'category_name')) as $catName) {
    $expenseCategoryOptions[$catName] = $catName;
}
?>

<div class="transaction-page expenses-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-wallet2 me-1"></i> Expenses</span>
            <strong class="list-toolbar-value"><?= $count ?> record<?= $count !== 1 ? 's' : '' ?></strong>
            <?php if ($count > 0): ?>
                <span class="list-toolbar-meta">Total · <?= $symbol ?> <?= money($totalAmount) ?></span>
            <?php endif; ?>
        </div>
        <div class="page-header-actions list-toolbar-actions">
            <a href="<?= url('expenses/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add
            </a>
        </div>
    </div>

    <?php if ($count === 0): ?>
        <div class="table-card">
            <div class="empty-state">
                <i class="bi bi-wallet2"></i>
                <p>No expenses recorded yet.</p>
                <a href="<?= url('expenses/create') ?>" class="btn btn-primary btn-sm">Add Expense</a>
            </div>
        </div>
    <?php else: ?>
        <div data-filter-scope="expenses">
        <?php
        $filterScope = 'expenses';
        $searchPlaceholder = 'Search by title, category, user...';
        $filterDateRange = true;
        $filterSelects = [[
            'name' => 'category',
            'label' => 'Category',
            'attr' => 'data-filter-category',
            'options' => $expenseCategoryOptions,
        ]];
        require VIEW_PATH . '/partials/list-filters.php';
        ?>
        <div class="report-mobile-list d-lg-none" data-filter-mobile>
            <?php foreach ($expenses as $i => $exp): ?>
                <div class="report-item-card transaction-card" data-filter-item data-filter-date="<?= e($exp['expense_date']) ?>" data-filter-category="<?= e($exp['category_name']) ?>">
                    <div class="report-item-top">
                        <div class="transaction-item-main">
                            <span class="transaction-item-no">#<?= $i + 1 ?></span>
                            <strong class="report-item-title"><?= e($exp['title']) ?></strong>
                        </div>
                        <span class="report-item-amount"><?= $symbol ?> <?= money($exp['amount']) ?></span>
                    </div>
                    <div class="transaction-detail-grid">
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">Date</span>
                            <span><?= e($exp['expense_date']) ?></span>
                        </div>
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">Category</span>
                            <span><?= e($exp['category_name']) ?></span>
                        </div>
                        <div class="transaction-detail">
                            <span class="transaction-detail-label">By</span>
                            <span><?= e($exp['user_name']) ?></span>
                        </div>
                    </div>
                    <?php if (!empty($exp['notes'])): ?>
                        <div class="transaction-notes"><i class="bi bi-chat-left-text me-1"></i><?= e($exp['notes']) ?></div>
                    <?php endif; ?>
                    <div class="inventory-item-actions">
                        <a href="<?= url('expenses/edit/' . $exp['id']) ?>" class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                        <?php if (Auth::isAdmin()): ?>
                            <form method="POST" action="<?= url('expenses/delete/' . $exp['id']) ?>" class="flex-fill" onsubmit="return confirm('Delete this expense?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                    <i class="bi bi-trash me-1"></i> Delete
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="table-card d-none d-lg-block">
            <div class="table-responsive-wrap">
                <table class="table table-striped data-table-lg mb-0" data-filter-table>
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
                            <tr data-filter-item data-filter-date="<?= e($exp['expense_date']) ?>" data-filter-category="<?= e($exp['category_name']) ?>">
                                <td><?= $i + 1 ?></td>
                                <td><?= e($exp['expense_date']) ?></td>
                                <td><strong><?= e($exp['title']) ?></strong></td>
                                <td><?= e($exp['category_name']) ?></td>
                                <td><strong><?= money($exp['amount']) ?></strong></td>
                                <td><?= e($exp['user_name']) ?></td>
                                <td>
                                    <div class="gap-actions">
                                        <a href="<?= url('expenses/edit/' . $exp['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if (Auth::isAdmin()): ?>
                                            <form method="POST" action="<?= url('expenses/delete/' . $exp['id']) ?>" class="d-inline" onsubmit="return confirm('Delete this expense?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-card d-none" data-filter-empty>
            <div class="empty-state py-3"><p class="mb-0">No expenses match your filters.</p></div>
        </div>
        </div>
    <?php endif; ?>
</div>
