<div class="inventory-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-plus-circle me-1"></i> New Item</span>
            <strong class="list-toolbar-value">Add to inventory</strong>
        </div>
        <div class="page-header-actions list-toolbar-actions">
            <a href="<?= url('items') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="table-card form-card">
        <form method="POST" action="<?= url('items/store') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Item Name *</label>
                <input type="text" name="name" class="form-control" value="<?= e(old('name')) ?>" placeholder="e.g. Rice 5kg" required>
            </div>
            <div class="row g-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label">Cost Price *</label>
                    <input type="number" name="cost_price" class="form-control" step="0.01" min="0" value="<?= e(old('cost_price', '0')) ?>" required>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label">Selling Price *</label>
                    <input type="number" name="selling_price" class="form-control" step="0.01" min="0" value="<?= e(old('selling_price', '0')) ?>" required>
                </div>
            </div>
            <div class="mb-4 mt-3">
                <label class="form-label">Current Stock</label>
                <input type="number" name="current_stock" class="form-control" step="0.01" min="0" value="<?= e(old('current_stock', '0')) ?>">
            </div>
            <button type="submit" class="btn btn-primary w-100 w-sm-auto">
                <i class="bi bi-check-lg me-1"></i> Save Item
            </button>
        </form>
    </div>
</div>
