<div class="page-header">
    <h1 class="h3 mb-0">Edit Expense</h1>
    <a href="<?= url('expenses') ?>" class="btn btn-outline-secondary">Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="table-card">
            <form method="POST" action="<?= url('expenses/update/' . $expense['id']) ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Category *</label>
                    <select name="category_id" class="form-select" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (int)$cat['id'] === (int)$expense['category_id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" value="<?= e($expense['title']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount *</label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="0.01" value="<?= e($expense['amount']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Expense Date *</label>
                    <input type="date" name="expense_date" class="form-control" value="<?= e($expense['expense_date']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"><?= e($expense['notes'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Expense</button>
            </form>
        </div>
    </div>
</div>
