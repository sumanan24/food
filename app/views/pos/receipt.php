<?php $config = require CONFIG_PATH . '/app.php'; $symbol = $config['currency_symbol'] ?? 'Rs.'; ?>

<div class="receipt-page">
    <div class="table-card receipt-card mx-auto">
        <div class="text-center mb-3">
            <h4><?= e($config['name']) ?></h4>
            <p class="text-muted mb-0"><?= e($bill['bill_number']) ?></p>
            <small class="text-muted"><?= e($bill['bill_date']) ?> · Billed by <?= e($bill['user_name']) ?></small>
            <?php if (!empty($session['counter_person_name'])): ?>
                <small class="text-muted d-block">Counter: <?= e($session['counter_person_name']) ?></small>
            <?php endif; ?>
        </div>
        <table class="table table-sm mb-3">
            <thead><tr><th>Item</th><th>Qty</th><th class="text-end">Total</th></tr></thead>
            <tbody>
                <?php foreach ($items as $line): ?>
                    <tr>
                        <td><?= e($line['item_name']) ?></td>
                        <td><?= money($line['quantity']) ?></td>
                        <td class="text-end"><?= money($line['total_price']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="receipt-totals">
            <div class="d-flex justify-content-between"><span>Subtotal</span><span><?= $symbol ?> <?= money($bill['subtotal']) ?></span></div>
            <?php if ((float)$bill['discount'] > 0): ?>
                <div class="d-flex justify-content-between"><span>Discount</span><span>- <?= $symbol ?> <?= money($bill['discount']) ?></span></div>
            <?php endif; ?>
            <div class="d-flex justify-content-between fw-bold fs-5 mt-2"><span>Total</span><span><?= $symbol ?> <?= money($bill['total_amount']) ?></span></div>
            <div class="text-muted small mt-1">Payment: <?= e(ucfirst($bill['payment_method'])) ?></div>
        </div>
        <div class="d-flex gap-2 mt-4 no-print">
            <button type="button" class="btn btn-primary flex-fill" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print</button>
            <a href="<?= url('pos') ?>" class="btn btn-outline-secondary flex-fill">New Bill</a>
        </div>
    </div>
</div>
