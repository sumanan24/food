<?php
$config = require CONFIG_PATH . '/app.php';
$symbol = $config['currency_symbol'] ?? 'Rs.';
$itemsJson = json_encode(array_map(fn($i) => [
    'id' => (int) $i['id'],
    'name' => $i['name'],
    'type' => $i['item_type'],
    'price' => (float) $i['selling_price'],
    'stock' => (float) $i['current_stock'],
    'unit' => $i['unit'] ?? 'pcs',
], $items));
?>

<div class="transaction-page pos-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-receipt-cutoff me-1"></i> POS Billing</span>
            <strong class="list-toolbar-value"><?= date('d M Y') ?></strong>
        </div>
        <div class="page-header-actions list-toolbar-actions">
            <a href="<?= url('pos/history') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history me-1"></i> Bills
            </a>
        </div>
    </div>

    <?php if (!$counterOpen): ?>
        <div class="alert alert-warning d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3">
            <div>
                <strong><i class="bi bi-exclamation-triangle me-1"></i> Bill counter is not open.</strong>
                <span class="d-block small">Open the counter with hand cash before preparing bills. You can open and close multiple times per day.</span>
            </div>
            <a href="<?= url('cash') ?>" class="btn btn-warning btn-sm flex-shrink-0">Open Bill Counter</a>
        </div>
    <?php else: ?>
        <div class="alert alert-success py-2 mb-3">
            <small><i class="bi bi-check-circle me-1"></i> Counter open · <?= e($session['counter_person_name'] ?: $session['user_name']) ?> · Hand cash <?= $symbol ?> <?= money($session['opening_balance']) ?></small>
        </div>
    <?php endif; ?>

    <div class="row g-3 pos-billing-area<?= $counterOpen ? '' : ' pos-billing-disabled' ?>">
        <div class="col-lg-7">
            <div class="table-card pos-items-card">
                <h5 class="card-title mb-3"><i class="bi bi-grid"></i> Menu Items</h5>
                <div class="pos-item-grid" id="posItemGrid"></div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="table-card pos-cart-card">
                <h5 class="card-title mb-3"><i class="bi bi-cart3"></i> Current Bill</h5>
                <form method="POST" action="<?= url('pos/store') ?>" id="posForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="items" id="posItemsInput">
                    <div class="pos-cart-list" id="posCartList">
                        <div class="empty-state py-4"><p class="mb-0">Tap items to add to bill</p></div>
                    </div>
                    <div class="pos-cart-summary mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <strong id="posSubtotal"><?= $symbol ?> 0.00</strong>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Discount</label>
                            <input type="number" name="discount" id="posDiscount" class="form-control" step="0.01" min="0" value="0">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Payment</label>
                            <select name="payment_method" class="form-select">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="upi">UPI</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="transaction-total-box mb-3">
                            <span class="transaction-total-label">Total</span>
                            <strong class="transaction-total-value" id="posTotal"><?= $symbol ?> 0.00</strong>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" id="posCheckoutBtn" disabled>
                            <i class="bi bi-check-lg me-1"></i> Complete Bill
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const counterOpen = <?= $counterOpen ? 'true' : 'false' ?>;
    const items = <?= $itemsJson ?>;
    const symbol = <?= json_encode($symbol) ?>;
    const grid = document.getElementById('posItemGrid');
    const cartList = document.getElementById('posCartList');
    const itemsInput = document.getElementById('posItemsInput');
    const subtotalEl = document.getElementById('posSubtotal');
    const totalEl = document.getElementById('posTotal');
    const discountEl = document.getElementById('posDiscount');
    const checkoutBtn = document.getElementById('posCheckoutBtn');
    let cart = [];

    items.forEach(function (item) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'pos-item-btn';
        btn.innerHTML = '<strong>' + item.name + '</strong><span>' + symbol + ' ' + item.price.toFixed(2) + '</span><small>' + (item.type === 'long' ? 'Stock: ' + item.stock : 'Daily') + '</small>';
        btn.addEventListener('click', function () { addToCart(item); });
        grid.appendChild(btn);
    });

    function addToCart(item) {
        if (!counterOpen) return;
        const existing = cart.find(function (c) { return c.item_id === item.id; });
        if (existing) {
            if (item.type === 'long' && existing.quantity + 1 > item.stock) return;
            existing.quantity += 1;
        } else {
            if (item.type === 'long' && item.stock < 1) return;
            cart.push({ item_id: item.id, name: item.name, unit_price: item.price, quantity: 1, type: item.type, stock: item.stock });
        }
        renderCart();
    }

    function renderCart() {
        if (cart.length === 0) {
            cartList.innerHTML = '<div class="empty-state py-4"><p class="mb-0">Tap items to add to bill</p></div>';
            checkoutBtn.disabled = true;
        } else {
            cartList.innerHTML = cart.map(function (line, idx) {
                return '<div class="pos-cart-line"><div><strong>' + line.name + '</strong><br><small>' + symbol + ' ' + line.unit_price.toFixed(2) + ' x ' + line.quantity + '</small></div><div class="d-flex align-items-center gap-1"><button type="button" class="btn btn-sm btn-outline-secondary" data-idx="' + idx + '" data-action="minus">-</button><span>' + line.quantity + '</span><button type="button" class="btn btn-sm btn-outline-secondary" data-idx="' + idx + '" data-action="plus">+</button><button type="button" class="btn btn-sm btn-outline-danger" data-idx="' + idx + '" data-action="remove"><i class="bi bi-x"></i></button></div></div>';
            }).join('');
            checkoutBtn.disabled = false;
            cartList.querySelectorAll('button[data-action]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const idx = parseInt(btn.dataset.idx, 10);
                    const action = btn.dataset.action;
                    if (action === 'remove') cart.splice(idx, 1);
                    else if (action === 'plus') {
                        if (cart[idx].type === 'long' && cart[idx].quantity + 1 > cart[idx].stock) return;
                        cart[idx].quantity += 1;
                    } else if (action === 'minus') {
                        cart[idx].quantity -= 1;
                        if (cart[idx].quantity <= 0) cart.splice(idx, 1);
                    }
                    renderCart();
                });
            });
        }
        const subtotal = cart.reduce(function (s, l) { return s + l.quantity * l.unit_price; }, 0);
        const discount = parseFloat(discountEl.value) || 0;
        const total = Math.max(0, subtotal - discount);
        subtotalEl.textContent = symbol + ' ' + subtotal.toFixed(2);
        totalEl.textContent = symbol + ' ' + total.toFixed(2);
        itemsInput.value = JSON.stringify(cart.map(function (l) {
            return { item_id: l.item_id, quantity: l.quantity, unit_price: l.unit_price };
        }));
    }

    discountEl.addEventListener('input', renderCart);
});
</script>
