<?php use Core\Security; ?>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header">Add Category</div>
            <div class="card-body">
                <form method="post" action="<?= url('categories/store') ?>">
                    <?= Security::csrfField() ?>
                    <div class="mb-2"><input type="text" name="name" class="form-control" placeholder="Name" required></div>
                    <div class="mb-2"><textarea name="description" class="form-control" placeholder="Description" rows="2"></textarea></div>
                    <select name="status" class="form-select mb-2"><option value="1">Active</option><option value="0">Inactive</option></select>
                    <button class="btn btn-primary w-100">Add</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header"><form method="get" class="d-flex gap-2"><input name="search" class="form-control form-control-sm" placeholder="Search..." value="<?= Security::escape($search) ?>"><button class="btn btn-sm btn-primary">Search</button></form></div>
            <table class="table mb-0">
                <thead><tr><th>Name</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($categories as $c): ?>
                <tr>
                    <td><?= Security::escape($c['name']) ?></td>
                    <td><?= Security::escape($c['description'] ?? '') ?></td>
                    <td><?= $c['status'] ? 'Active' : 'Inactive' ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#edit<?= $c['id'] ?>"><i class="bi bi-pencil"></i></button>
                        <form method="post" action="<?= url('categories/delete/' . $c['id']) ?>" class="d-inline" onsubmit="return confirmDelete(this)"><?= Security::csrfField() ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                    </td>
                </tr>
                <div class="modal fade" id="edit<?= $c['id'] ?>" tabindex="-1">
                    <div class="modal-dialog"><div class="modal-content">
                        <form method="post" action="<?= url('categories/update/' . $c['id']) ?>">
                            <?= Security::csrfField() ?>
                            <div class="modal-header"><h5>Edit Category</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">
                                <input name="name" class="form-control mb-2" value="<?= Security::escape($c['name']) ?>" required>
                                <textarea name="description" class="form-control mb-2"><?= Security::escape($c['description'] ?? '') ?></textarea>
                                <select name="status" class="form-select"><option value="1" <?= $c['status']?'selected':'' ?>>Active</option><option value="0" <?= !$c['status']?'selected':'' ?>>Inactive</option></select>
                            </div>
                            <div class="modal-footer"><button class="btn btn-primary">Update</button></div>
                        </form>
                    </div></div>
                </div>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
