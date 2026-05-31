<?php use Core\Security; $isEdit = !empty($expense); ?>
<div class="card shadow-sm col-lg-6 mx-auto">
    <div class="card-body">
        <form method="post" action="<?= url($isEdit ? 'expenses/update/' . $expense['id'] : 'expenses/store') ?>">
            <?= Security::csrfField() ?>
            <div class="mb-3"><label>Title</label><input name="title" class="form-control" required value="<?= Security::escape($expense['title'] ?? '') ?>"></div>
            <div class="mb-3"><label>Category</label>
                <select name="category_id" class="form-select">
                    <option value="">-- Select --</option>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($expense['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= Security::escape($c['name']) ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div class="mb-3"><label>Amount</label><input type="number" step="0.01" name="amount" class="form-control" required value="<?= $expense['amount'] ?? '' ?>"></div>
            <div class="mb-3"><label>Date</label><input type="date" name="expense_date" class="form-control" value="<?= $expense['expense_date'] ?? date('Y-m-d') ?>"></div>
            <div class="mb-3"><label>Notes</label><textarea name="notes" class="form-control"><?= Security::escape($expense['notes'] ?? '') ?></textarea></div>
            <button class="btn btn-primary">Save</button>
            <a href="<?= url('expenses') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
