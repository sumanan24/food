<div class="page-header">
    <h1 class="h3 mb-0">Add Item</h1>
    <a href="<?= url('items') ?>" class="btn btn-outline-secondary">Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="table-card">
            <form method="POST" action="<?= url('items/store') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Item Name *</label>
                    <input type="text" name="name" class="form-control" value="<?= e(old('name')) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cost Price *</label>
                    <input type="number" name="cost_price" class="form-control" step="0.01" min="0" value="<?= e(old('cost_price', '0')) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Selling Price *</label>
                    <input type="number" name="selling_price" class="form-control" step="0.01" min="0" value="<?= e(old('selling_price', '0')) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Current Stock</label>
                    <input type="number" name="current_stock" class="form-control" step="0.01" min="0" value="<?= e(old('current_stock', '0')) ?>">
                </div>
                <button type="submit" class="btn btn-primary">Save Item</button>
            </form>
        </div>
    </div>
</div>
