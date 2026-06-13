<div class="transaction-page expenses-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-wallet2 me-1"></i> New Expense</span>
            <strong class="list-toolbar-value">Record spending</strong>
        </div>
        <div class="page-header-actions list-toolbar-actions">
            <a href="<?= url('expenses') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="table-card form-card">
        <?php if (empty($categories)): ?>
            <div class="alert alert-warning mb-0">No expense categories found. Ask an admin to create categories first.</div>
        <?php else: ?>
            <form method="POST" action="<?= url('expenses/store') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Category *</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Electricity bill" required>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Amount *</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0.01" placeholder="0.00" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Expense Date *</label>
                        <input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="mb-4 mt-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 w-sm-auto">
                    <i class="bi bi-check-lg me-1"></i> Save Expense
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
