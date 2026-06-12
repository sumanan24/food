<div class="page-header">
    <h1 class="h3 mb-0">Edit Item</h1>
    <a href="<?= url('items') ?>" class="btn btn-outline-secondary">Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="table-card">
            <form method="POST" action="<?= url('items/update/' . $item['id']) ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Item Name *</label>
                    <input type="text" name="name" class="form-control" value="<?= e($item['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cost Price *</label>
                    <input type="number" name="cost_price" class="form-control" step="0.01" min="0" value="<?= e($item['cost_price']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Selling Price *</label>
                    <input type="number" name="selling_price" class="form-control" step="0.01" min="0" value="<?= e($item['selling_price']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Current Stock</label>
                    <input type="number" name="current_stock" class="form-control" step="0.01" min="0" value="<?= e($item['current_stock']) ?>">
                </div>
                <button type="submit" class="btn btn-primary">Update Item</button>
            </form>
        </div>
    </div>
</div>
