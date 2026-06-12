<div class="page-header">
    <h1 class="h3 mb-0">Record Purchase</h1>
    <a href="<?= url('purchases') ?>" class="btn btn-outline-secondary">Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="table-card">
            <form method="POST" action="<?= url('purchases/store') ?>" id="purchaseForm">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Item *</label>
                    <select name="item_id" id="item_id" class="form-select" required>
                        <option value="">Select Item</option>
                        <?php foreach ($items as $item): ?>
                            <option value="<?= $item['id'] ?>" data-cost="<?= $item['cost_price'] ?>">
                                <?= e($item['name']) ?> (Stock: <?= money($item['current_stock']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity *</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" step="0.01" min="0.01" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Unit Cost *</label>
                    <input type="number" name="unit_cost" id="unit_cost" class="form-control" step="0.01" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Total Cost</label>
                    <input type="text" id="total_cost" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Purchase Date *</label>
                    <input type="date" name="purchase_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Purchase</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const itemSelect = document.getElementById('item_id');
    const qty = document.getElementById('quantity');
    const unitCost = document.getElementById('unit_cost');
    const total = document.getElementById('total_cost');

    function calcTotal() {
        const q = parseFloat(qty.value) || 0;
        const u = parseFloat(unitCost.value) || 0;
        total.value = (q * u).toFixed(2);
    }

    itemSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (opt.dataset.cost) unitCost.value = opt.dataset.cost;
        calcTotal();
    });

    qty.addEventListener('input', calcTotal);
    unitCost.addEventListener('input', calcTotal);
});
</script>
