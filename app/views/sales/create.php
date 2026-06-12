<div class="page-header">
    <h1 class="h3 mb-0">Record Sale</h1>
    <a href="<?= url('sales') ?>" class="btn btn-outline-secondary">Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="table-card">
            <form method="POST" action="<?= url('sales/store') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Item *</label>
                    <select name="item_id" id="item_id" class="form-select" required>
                        <option value="">Select Item</option>
                        <?php foreach ($items as $item): ?>
                            <option value="<?= $item['id'] ?>" data-price="<?= $item['selling_price'] ?>" data-stock="<?= $item['current_stock'] ?>">
                                <?= e($item['name']) ?> (Stock: <?= money($item['current_stock']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity *</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" step="0.01" min="0.01" required>
                    <div class="form-text">Available: <span id="stock_hint">-</span></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Unit Price *</label>
                    <input type="number" name="unit_price" id="unit_price" class="form-control" step="0.01" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Total Price</label>
                    <input type="text" id="total_price" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sale Date *</label>
                    <input type="date" name="sale_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Sale</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const itemSelect = document.getElementById('item_id');
    const qty = document.getElementById('quantity');
    const unitPrice = document.getElementById('unit_price');
    const total = document.getElementById('total_price');
    const stockHint = document.getElementById('stock_hint');

    function calcTotal() {
        const q = parseFloat(qty.value) || 0;
        const u = parseFloat(unitPrice.value) || 0;
        total.value = (q * u).toFixed(2);
    }

    itemSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (opt.dataset.price) unitPrice.value = opt.dataset.price;
        if (opt.dataset.stock) stockHint.textContent = opt.dataset.stock;
        calcTotal();
    });

    qty.addEventListener('input', calcTotal);
    unitPrice.addEventListener('input', calcTotal);
});
</script>
