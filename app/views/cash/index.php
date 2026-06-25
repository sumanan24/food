<?php $config = require CONFIG_PATH . '/app.php'; $symbol = $config['currency_symbol'] ?? 'Rs.'; ?>

<div class="transaction-page cash-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-cash-stack me-1"></i> Cash Open / Close</span>
            <strong class="list-toolbar-value"><?= date('d M Y') ?></strong>
        </div>
        <div class="page-header-actions list-toolbar-actions">
            <a href="<?= url('pos') ?>" class="btn btn-outline-primary">
                <i class="bi bi-receipt-cutoff me-1"></i> POS
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><div class="card stat-card stat-sales"><div class="card-body"><div class="stat-label">Today Sales</div><div class="stat-value"><?= $symbol ?> <?= money($todaySales) ?></div></div></div></div>
        <div class="col-6 col-md-3"><div class="card stat-card stat-expenses"><div class="card-body"><div class="stat-label">Today Expenses</div><div class="stat-value"><?= $symbol ?> <?= money($todayExpenses) ?></div></div></div></div>
        <div class="col-6 col-md-3"><div class="card stat-card stat-profit"><div class="card-body"><div class="stat-label">Expected Cash</div><div class="stat-value" id="cashExpectedValue"><?= $symbol ?> <?= money($expectedCash) ?></div></div></div></div>
        <div class="col-6 col-md-3"><div class="card stat-card stat-purchases"><div class="card-body"><div class="stat-label">Status</div><div class="stat-value"><?= $session ? e(ucfirst($session['status'])) : 'Not Open' ?></div></div></div></div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <?php if (!$session || $session['status'] === 'closed'): ?>
                <?php if ($session && $session['status'] === 'closed'): ?>
                    <div class="alert alert-info mb-3">
                        Today's cash is already closed. Open again tomorrow morning.
                    </div>
                <?php endif; ?>

                <?php if (!$session): ?>
                    <div class="table-card form-card cash-action-card">
                        <h5 class="mb-1"><i class="bi bi-unlock me-1"></i> Open Cash</h5>
                        <p class="text-muted small mb-3">Type the hand cash amount in the drawer to start today's billing.</p>
                        <form method="POST" action="<?= url('cash/open') ?>" class="cash-open-form">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label">Open Cash Amount *</label>
                                <div class="cash-amount-input-wrap">
                                    <span class="cash-amount-prefix"><?= e($symbol) ?></span>
                                    <input
                                        type="text"
                                        name="opening_balance"
                                        class="form-control cash-amount-input"
                                        inputmode="decimal"
                                        autocomplete="off"
                                        placeholder="0.00"
                                        required
                                    >
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Counter Person (optional)</label>
                                <input
                                    type="text"
                                    name="counter_person_name"
                                    class="form-control"
                                    maxlength="100"
                                    value="<?= e($defaultPersonName) ?>"
                                    placeholder="Name at counter"
                                >
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                <i class="bi bi-unlock me-1"></i> Open Cash
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

                <?php if ($session && $session['status'] === 'closed'): ?>
                    <div class="table-card">
                        <h5 class="mb-3">Closed Summary</h5>
                        <div class="transaction-detail-grid">
                            <div class="transaction-detail"><span class="transaction-detail-label">Open Cash</span><span><?= $symbol ?> <?= money($session['opening_balance']) ?></span></div>
                            <div class="transaction-detail"><span class="transaction-detail-label">Close Cash</span><span><?= $symbol ?> <?= money($session['cash_received']) ?></span></div>
                            <div class="transaction-detail"><span class="transaction-detail-label">Difference</span><span class="<?= (float)$session['cash_difference'] >= 0 ? 'text-profit' : 'text-loss' ?>"><?= $symbol ?> <?= money($session['cash_difference']) ?></span></div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="table-card form-card cash-action-card cash-open-summary mb-3">
                    <h5 class="mb-3"><i class="bi bi-check-circle text-success me-1"></i> Cash Open</h5>
                    <div class="transaction-detail-grid">
                        <div class="transaction-detail"><span class="transaction-detail-label">Open Cash</span><span><?= $symbol ?> <?= money($session['opening_balance']) ?></span></div>
                        <div class="transaction-detail"><span class="transaction-detail-label">Counter Person</span><span><?= e($session['counter_person_name'] ?: $session['user_name']) ?></span></div>
                        <div class="transaction-detail"><span class="transaction-detail-label">Opened At</span><span><?= e(date('h:i A', strtotime($session['opened_at']))) ?></span></div>
                        <div class="transaction-detail"><span class="transaction-detail-label">Expected Now</span><span><?= $symbol ?> <?= money($expectedCash) ?></span></div>
                    </div>
                </div>

                <div class="table-card form-card cash-action-card">
                    <h5 class="mb-1"><i class="bi bi-lock me-1"></i> Close Cash</h5>
                    <p class="text-muted small mb-3">Count cash in drawer and type the total amount to close for today.</p>
                    <form method="POST" action="<?= url('cash/close') ?>" class="cash-close-form">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Close Cash Amount *</label>
                            <div class="cash-amount-input-wrap">
                                <span class="cash-amount-prefix"><?= e($symbol) ?></span>
                                <input
                                    type="text"
                                    name="cash_received"
                                    id="closeCashInput"
                                    class="form-control cash-amount-input"
                                    inputmode="decimal"
                                    autocomplete="off"
                                    placeholder="Type counted cash"
                                    required
                                >
                            </div>
                            <div class="cash-close-preview mt-2" id="cashClosePreview">
                                Difference: <strong id="cashDiffValue"><?= $symbol ?> 0.00</strong>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Closed By (optional)</label>
                            <input
                                type="text"
                                name="closed_by_name"
                                class="form-control"
                                maxlength="100"
                                value="<?= e($defaultPersonName) ?>"
                                placeholder="Who closed the counter?"
                            >
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes (optional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Shortage, extra cash, etc."></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100 btn-lg">
                            <i class="bi bi-lock me-1"></i> Close Cash
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-6">
            <div class="table-card">
                <h5 class="card-title"><i class="bi bi-clock-history"></i> Recent Sessions</h5>
                <?php if (empty($history)): ?>
                    <p class="text-muted mb-0">No cash sessions yet.</p>
                <?php else: ?>
                    <?php foreach ($history as $h): ?>
                        <div class="stock-list-item">
                            <span>
                                <?= e($h['session_date']) ?>
                                · Open <?= $symbol ?> <?= money($h['opening_balance']) ?>
                                <?php if ($h['status'] === 'closed'): ?>
                                    · Close <?= $symbol ?> <?= money($h['cash_received']) ?>
                                <?php endif; ?>
                            </span>
                            <span class="stock-badge <?= $h['status'] === 'closed' ? 'ok' : 'low' ?>"><?= e(ucfirst($h['status'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($session && $session['status'] === 'open'): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const symbol = <?= json_encode($symbol) ?>;
    const expected = <?= json_encode((float) $expectedCash) ?>;
    const input = document.getElementById('closeCashInput');
    const diffEl = document.getElementById('cashDiffValue');

    function parseAmount(value) {
        const clean = String(value).replace(/[^\d.]/g, '');
        return clean === '' || isNaN(clean) ? 0 : parseFloat(clean);
    }

    function updateDiff() {
        const counted = parseAmount(input.value);
        const diff = counted - expected;
        diffEl.textContent = symbol + ' ' + diff.toFixed(2);
        diffEl.className = diff >= 0 ? 'text-profit' : 'text-loss';
    }

    input.addEventListener('input', updateDiff);
    updateDiff();
});
</script>
<?php endif; ?>
