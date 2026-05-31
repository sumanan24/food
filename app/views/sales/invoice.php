<?php use Core\Security;
if (!$sale) { echo '<p>Invoice not found</p>'; return; }
$shop = $settings['shop_name'] ?? 'Food Shop';
?>
<div class="invoice-box" id="invoicePrint">
    <div class="text-center mb-3">
        <h3><?= Security::escape($shop) ?></h3>
        <p class="mb-0 small"><?= Security::escape($settings['shop_address'] ?? '') ?></p>
        <p class="small"><?= Security::escape($settings['shop_phone'] ?? '') ?></p>
    </div>
    <hr>
    <p><strong>Invoice:</strong> <?= Security::escape($sale['invoice_number']) ?><br>
    <strong>Date:</strong> <?= $sale['sale_date'] ?><br>
    <strong>Customer:</strong> <?= Security::escape($sale['customer_name'] ?? 'Walk-in') ?></p>
    <table class="table table-sm">
        <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
        <tbody>
        <?php foreach ($sale['items'] as $item): ?>
        <tr>
            <td><?= Security::escape($item['product_name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= number_format($item['unit_price'], 2) ?></td>
            <td><?= number_format($item['line_total'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-end">
        <p>Subtotal: <?= number_format($sale['subtotal'], 2) ?></p>
        <p>Discount: <?= number_format($sale['discount'], 2) ?></p>
        <p>Tax: <?= number_format($sale['tax'], 2) ?></p>
        <h5>Grand Total: <?= ($settings['currency'] ?? 'Rs.') . number_format($sale['grand_total'], 2) ?></h5>
        <p>Paid: <?= number_format($sale['paid_amount'], 2) ?></p>
        <?php if ((float)$sale['change_amount'] > 0): ?>
        <p class="text-success fw-bold">Change: <?= ($settings['currency'] ?? 'Rs.') . number_format($sale['change_amount'], 2) ?></p>
        <?php endif; ?>
        <?php $balance = max(0, (float)$sale['grand_total'] - (float)$sale['paid_amount']); ?>
        <?php if ($balance > 0): ?>
        <p class="text-danger">Balance Due: <?= ($settings['currency'] ?? 'Rs.') . number_format($balance, 2) ?></p>
        <?php endif; ?>
        <p>Payment: <?= ucfirst($sale['payment_type']) ?></p>
    </div>
    <div class="text-center mt-3" id="qrcode"></div>
    <p class="text-center small text-muted">Thank you for your purchase!</p>
</div>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
new QRCode(document.getElementById("qrcode"), {
    text: "<?= Security::escape($sale['invoice_number']) ?>|<?= $sale['grand_total'] ?>",
    width: 80, height: 80
});
</script>
