<?php use Core\Security; $isEdit = !empty($purchase); ?>
<div class="card shadow-sm col-lg-8 mx-auto">
    <div class="card-body">
        <form method="post" action="<?= url($isEdit ? 'purchases/update/' . $purchase['id'] : 'purchases/store') ?>">
            <?= Security::csrfField() ?>
            <div class="row g-3">
                <div class="col-md-6"><label>Supplier</label>
                    <select name="supplier_id" class="form-select" id="supplierSelect">
                        <option value="">-- Custom --</option>
                        <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?>" data-name="<?= Security::escape($s['name']) ?>" <?= ($purchase['supplier_id'] ?? '') == $s['id'] ? 'selected' : '' ?>><?= Security::escape($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div class="col-md-6"><label>Supplier Name *</label>
                    <input type="text" name="supplier_name" id="supplierName" class="form-control" required value="<?= Security::escape($purchase['supplier_name'] ?? '') ?>"></div>
                <div class="col-md-6"><label>Product *</label>
                    <select name="product_id" class="form-select" required>
                        <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($purchase['product_id'] ?? '') == $p['id'] ? 'selected' : '' ?>><?= Security::escape($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div class="col-md-3"><label>Quantity</label><input type="number" name="quantity" class="form-control" required value="<?= $purchase['quantity'] ?? 1 ?>"></div>
                <div class="col-md-3"><label>Buying Cost</label><input type="number" step="0.01" name="buying_cost" class="form-control" required value="<?= $purchase['buying_cost'] ?? '' ?>"></div>
                <div class="col-md-4"><label>Date</label><input type="date" name="purchase_date" class="form-control" value="<?= $purchase['purchase_date'] ?? date('Y-m-d') ?>"></div>
                <div class="col-md-4"><label>Invoice #</label><input type="text" name="invoice_number" class="form-control" value="<?= Security::escape($purchase['invoice_number'] ?? '') ?>"></div>
                <div class="col-12"><label>Notes</label><textarea name="notes" class="form-control"><?= Security::escape($purchase['notes'] ?? '') ?></textarea></div>
            </div>
            <button class="btn btn-primary mt-3">Save</button>
            <a href="<?= url('purchases') ?>" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
</div>
<script>
document.getElementById('supplierSelect')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (opt.dataset.name) document.getElementById('supplierName').value = opt.dataset.name;
});
</script>
