<?php use Core\Security;
$taxRate = (float)($settings['tax_rate'] ?? 0);
$cur = $settings['currency'] ?? 'Rs.';
?>
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header">
                <input type="text" id="barcodeInput" class="form-control" placeholder="Scan barcode or search product...">
            </div>
            <div class="card-body product-grid" style="max-height:400px;overflow-y:auto">
                <div class="row g-2" id="productList">
                <?php foreach ($products as $p): if ($p['quantity'] <= 0) continue; ?>
                <div class="col-6 col-md-4">
                    <button type="button" class="btn btn-outline-secondary w-100 text-start product-btn"
                        data-id="<?= $p['id'] ?>" data-name="<?= Security::escape($p['name']) ?>"
                        data-price="<?= $p['selling_price'] ?>" data-stock="<?= $p['quantity'] ?>"
                        data-barcode="<?= Security::escape($p['barcode'] ?? '') ?>">
                        <strong><?= Security::escape($p['name']) ?></strong><br>
                        <small><?= $cur . number_format($p['selling_price'], 2) ?> | Stock: <?= $p['quantity'] ?></small>
                    </button>
                </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header"><strong>Cart</strong></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0" id="cartTable">
                    <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th></th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="mb-2"><label>Customer</label><input type="text" id="customerName" class="form-control form-control-sm"></div>
                <div class="row g-2 mb-2">
                    <div class="col-6"><label>Discount</label><input type="number" id="discount" class="form-control form-control-sm" value="0" step="0.01"></div>
                    <div class="col-6"><label>Tax %</label><input type="number" id="taxRate" class="form-control form-control-sm" value="<?= $taxRate ?>" readonly></div>
                </div>
                <div class="mb-2"><label>Payment</label>
                    <select id="paymentType" class="form-select form-select-sm">
                        <option value="cash">Cash</option><option value="card">Card</option>
                        <option value="upi">UPI</option><option value="bank">Bank</option>
                    </select>
                </div>
                <div class="payment-summary border rounded p-2 mb-2 bg-light">
                    <div class="d-flex justify-content-between small"><span>Subtotal</span><span id="subtotalDisplay"><?= $cur ?>0.00</span></div>
                    <div class="d-flex justify-content-between small text-danger"><span>Discount</span><span id="discountDisplay">-<?= $cur ?>0.00</span></div>
                    <div class="d-flex justify-content-between small"><span>Tax</span><span id="taxDisplay"><?= $cur ?>0.00</span></div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fw-bold"><span>Grand Total</span><span id="grandTotal"><?= $cur ?>0.00</span></div>
                </div>
                <div class="mb-2">
                    <label class="form-label mb-1">Paid Amount</label>
                    <input type="number" id="paidAmount" class="form-control" step="0.01" min="0" value="0.00" placeholder="0.00">
                </div>
                <div class="d-flex justify-content-between mb-1 d-none" id="changeRow">
                    <span class="text-success fw-semibold">Change (Return)</span>
                    <span class="text-success fw-bold" id="changeAmount"><?= $cur ?>0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2 d-none" id="balanceRow">
                    <span class="text-danger fw-semibold">Balance Due</span>
                    <span class="text-danger fw-bold" id="balanceDue"><?= $cur ?>0.00</span>
                </div>
                <input type="hidden" id="grandTotalValue" value="0">
                <form id="checkoutForm">
                    <?= Security::csrfField() ?>
                    <input type="hidden" name="cart" id="cartData">
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-printer"></i> Checkout & Print</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$pageScript = '<script>window.CURRENCY = ' . json_encode($cur) . ';</script><script src="' . asset('js/pos.js') . '"></script>';
?>
