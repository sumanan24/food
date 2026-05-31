<?php use Core\Security; $isEdit = !empty($product); ?>
<div class="card shadow-sm col-lg-8 mx-auto">
    <div class="card-body">
        <form method="post" action="<?= url($isEdit ? 'products/update/' . $product['id'] : 'products/store') ?>" enctype="multipart/form-data">
            <?= Security::csrfField() ?>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Product Name *</label>
                    <input type="text" name="name" class="form-control" required value="<?= Security::escape($product['name'] ?? '') ?>"></div>
                <div class="col-md-6"><label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Select --</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($product['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= Security::escape($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div class="col-md-4"><label class="form-label">Buying Price *</label>
                    <input type="number" step="0.01" name="buying_price" class="form-control" required value="<?= $product['buying_price'] ?? '' ?>"></div>
                <div class="col-md-4"><label class="form-label">Selling Price *</label>
                    <input type="number" step="0.01" name="selling_price" class="form-control" required value="<?= $product['selling_price'] ?? '' ?>"></div>
                <div class="col-md-4"><label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" value="<?= $product['quantity'] ?? 0 ?>"></div>
                <div class="col-md-6"><label class="form-label">Barcode</label>
                    <input type="text" name="barcode" class="form-control" value="<?= Security::escape($product['barcode'] ?? '') ?>"></div>
                <div class="col-md-6"><label class="form-label">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control" value="<?= $product['expiry_date'] ?? '' ?>"></div>
                <div class="col-md-6"><label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*"></div>
                <div class="col-md-6"><label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= ($product['status'] ?? 1) ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= isset($product['status']) && !$product['status'] ? 'selected' : '' ?>>Inactive</option>
                    </select></div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="<?= url('products') ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
