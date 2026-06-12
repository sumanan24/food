<div class="page-header">
    <h1 class="h3 mb-0">Expense Categories</h1>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="table-card">
            <h5 class="mb-3">Add Category</h5>
            <form method="POST" action="<?= url('expense-categories/store') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Add Category</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="table-card">
            <table class="table table-striped data-table">
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
                            <td><?= e($cat['name']) ?></td>
                            <td><?= e($cat['description'] ?? '-') ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $cat['id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="<?= url('expense-categories/delete/' . $cat['id']) ?>" class="d-inline" onsubmit="return confirm('Delete this category?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal<?= $cat['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="<?= url('expense-categories/update/' . $cat['id']) ?>">
                                        <?= csrf_field() ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Category</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Name</label>
                                                <input type="text" name="name" class="form-control" value="<?= e($cat['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <input type="text" name="description" class="form-control" value="<?= e($cat['description'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
