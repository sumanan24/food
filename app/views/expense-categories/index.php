<?php $count = count($categories); ?>

<div class="transaction-page expense-categories-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-tags me-1"></i> Expense Categories</span>
            <strong class="list-toolbar-value"><?= $count ?> categor<?= $count !== 1 ? 'ies' : 'y' ?></strong>
        </div>
        <div class="page-header-actions list-toolbar-actions">
            <a href="<?= url('expenses') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-wallet2 me-1"></i> Expenses
            </a>
        </div>
    </div>

    <div class="table-card form-card mb-3">
        <h5 class="mb-3"><i class="bi bi-plus-circle me-1"></i> Add Category</h5>
        <form method="POST" action="<?= url('expense-categories/store') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Name *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Utilities" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Description</label>
                <input type="text" name="description" class="form-control" placeholder="Optional description">
            </div>
            <button type="submit" class="btn btn-primary w-100 w-sm-auto">
                <i class="bi bi-check-lg me-1"></i> Add Category
            </button>
        </form>
    </div>

    <?php if ($count === 0): ?>
        <div class="table-card">
            <div class="empty-state">
                <i class="bi bi-tags"></i>
                <p>No categories yet. Add one above.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="report-mobile-list d-lg-none">
            <?php foreach ($categories as $i => $cat): ?>
                <div class="report-item-card transaction-card">
                    <div class="report-item-top">
                        <div class="transaction-item-main">
                            <span class="transaction-item-no">#<?= $i + 1 ?></span>
                            <strong class="report-item-title"><?= e($cat['name']) ?></strong>
                        </div>
                    </div>
                    <?php if (!empty($cat['description'])): ?>
                        <div class="transaction-notes"><?= e($cat['description']) ?></div>
                    <?php else: ?>
                        <div class="transaction-notes text-muted">No description</div>
                    <?php endif; ?>
                    <div class="inventory-item-actions">
                        <button type="button" class="btn btn-sm btn-outline-primary flex-fill" data-bs-toggle="modal" data-bs-target="#editModal<?= $cat['id'] ?>">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </button>
                        <form method="POST" action="<?= url('expense-categories/delete/' . $cat['id']) ?>" class="flex-fill" onsubmit="return confirm('Delete this category?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="table-card d-none d-lg-block">
            <div class="table-responsive-wrap">
                <table class="table table-striped data-table-lg mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $i => $cat): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><strong><?= e($cat['name']) ?></strong></td>
                                <td><?= e($cat['description'] ?? '-') ?></td>
                                <td>
                                    <div class="gap-actions">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $cat['id'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" action="<?= url('expense-categories/delete/' . $cat['id']) ?>" class="d-inline" onsubmit="return confirm('Delete this category?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php foreach ($categories as $cat): ?>
            <div class="modal fade" id="editModal<?= $cat['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="<?= url('expense-categories/update/' . $cat['id']) ?>">
                            <?= csrf_field() ?>
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Category</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Name *</label>
                                    <input type="text" name="name" class="form-control" value="<?= e($cat['name']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <input type="text" name="description" class="form-control" value="<?= e($cat['description'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="modal-footer flex-column flex-sm-row gap-2">
                                <button type="button" class="btn btn-secondary w-100 w-sm-auto" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary w-100 w-sm-auto">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
