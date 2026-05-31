<?php use Core\Security; ?>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm"><div class="card-header">Add Supplier</div><div class="card-body">
            <form method="post" action="<?= url('suppliers/store') ?>">
                <?= Security::csrfField() ?>
                <input name="name" class="form-control mb-2" placeholder="Name" required>
                <input name="contact_number" class="form-control mb-2" placeholder="Phone">
                <input name="email" class="form-control mb-2" placeholder="Email">
                <textarea name="address" class="form-control mb-2" placeholder="Address"></textarea>
                <button class="btn btn-primary w-100">Add</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm">
            <table class="table mb-0">
                <thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($suppliers as $s): ?>
                <tr>
                    <td><?= Security::escape($s['name']) ?></td>
                    <td><?= Security::escape($s['contact_number'] ?? '') ?></td>
                    <td><?= Security::escape($s['email'] ?? '') ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#sup<?= $s['id'] ?>"><i class="bi bi-pencil"></i></button>
                        <form method="post" action="<?= url('suppliers/delete/' . $s['id']) ?>" class="d-inline" onsubmit="return confirmDelete(this)"><?= Security::csrfField() ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php foreach ($suppliers as $s): ?>
<div class="modal fade" id="sup<?= $s['id'] ?>"><div class="modal-dialog"><div class="modal-content">
<form method="post" action="<?= url('suppliers/update/' . $s['id']) ?>"><?= Security::csrfField() ?>
<div class="modal-header"><h5>Edit Supplier</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
    <input name="name" class="form-control mb-2" value="<?= Security::escape($s['name']) ?>" required>
    <input name="contact_number" class="form-control mb-2" value="<?= Security::escape($s['contact_number'] ?? '') ?>">
    <input name="email" class="form-control mb-2" value="<?= Security::escape($s['email'] ?? '') ?>">
    <textarea name="address" class="form-control"><?= Security::escape($s['address'] ?? '') ?></textarea>
</div>
<div class="modal-footer"><button class="btn btn-primary">Update</button></div>
</form></div></div></div>
<?php endforeach; ?>
