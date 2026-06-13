<div class="inventory-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-pencil me-1"></i> Edit Item</span>
            <strong class="list-toolbar-value"><?= e($item['name']) ?></strong>
        </div>
        <div class="page-header-actions list-toolbar-actions">
            <a href="<?= url('items') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="table-card form-card">
        <form method="POST" action="<?= url('items/update/' . $item['id']) ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Item Name *</label>
                <input type="text" name="name" class="form-control" value="<?= e($item['name']) ?>" required>
            </div>
            <div class="row g-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label">Cost Price *</label>
                    <input type="number" name="cost_price" class="form-control" step="0.01" min="0" value="<?= e($item['cost_price']) ?>" required>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label">Selling Price *</label>
                    <input type="number" name="selling_price" class="form-control" step="0.01" min="0" value="<?= e($item['selling_price']) ?>" required>
                </div>
            </div>
            <div class="mb-4 mt-3">
                <label class="form-label">Current Stock</label>
                <input type="number" name="current_stock" class="form-control" step="0.01" min="0" value="<?= e($item['current_stock']) ?>">
            </div>
            <button type="submit" class="btn btn-primary w-100 w-sm-auto">
                <i class="bi bi-check-lg me-1"></i> Update Item
            </button>
        </form>
    </div>
</div>
