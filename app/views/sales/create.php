<?php $config = require CONFIG_PATH . '/app.php'; $symbol = $config['currency_symbol'] ?? 'Rs.'; ?>

<div class="transaction-page sales-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-receipt me-1"></i> New Sale</span>
            <strong class="list-toolbar-value">Record stock out</strong>
        </div>
        <div class="page-header-actions list-toolbar-actions">
            <a href="<?= url('sales') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="table-card form-card">
        <form method="POST" action="<?= url('sales/store') ?>" id="saleForm">
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
            <div class="row g-3">
                <div class="col-6">
                    <label class="form-label">Quantity *</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" step="0.01" min="0.01" required>
                    <div class="form-text">Available: <span id="stock_hint">-</span></div>
                </div>
                <div class="col-6">
                    <label class="form-label">Unit Price *</label>
                    <input type="number" name="unit_price" id="unit_price" class="form-control" step="0.01" min="0" required>
                </div>
            </div>
            <div class="transaction-total-box mt-3 mb-3">
                <span class="transaction-total-label">Total Price</span>
                <strong class="transaction-total-value" id="total_price_display"><?= $symbol ?> 0.00</strong>
                <input type="hidden" id="total_price" value="0">
            </div>
            <div class="mb-3">
                <label class="form-label">Sale Date *</label>
                <input type="date" name="sale_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes"></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100 w-sm-auto">
                <i class="bi bi-check-lg me-1"></i> Save Sale
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const itemSelect = document.getElementById('item_id');
    const qty = document.getElementById('quantity');
    const unitPrice = document.getElementById('unit_price');
    const totalDisplay = document.getElementById('total_price_display');
    const totalHidden = document.getElementById('total_price');
    const stockHint = document.getElementById('stock_hint');
    const symbol = <?= json_encode($symbol) ?>;

    function calcTotal() {
        const q = parseFloat(qty.value) || 0;
        const u = parseFloat(unitPrice.value) || 0;
        const t = (q * u).toFixed(2);
        totalDisplay.textContent = symbol + ' ' + t;
        totalHidden.value = t;
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
