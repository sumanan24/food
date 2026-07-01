<?php
$expectedCash = (float) $sessionRow['opening_balance']
    + (float) $sessionRow['total_sales']
    - (float) $sessionRow['total_expenses'];
$modalId = 'editCloseModal' . (int) $sessionRow['id'];
?>
<div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?= url('cash/update-close/' . (int) $sessionRow['id']) ?>" class="cash-edit-close-form">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-1"></i> Edit Close Cash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        <?= e(date('d M Y h:i A', strtotime($sessionRow['opened_at']))) ?>
                        · Expected <?= $symbol ?> <?= money($expectedCash) ?>
                    </p>
                    <div class="mb-3">
                        <label class="form-label">Close Cash Amount *</label>
                        <div class="cash-amount-input-wrap">
                            <span class="cash-amount-prefix"><?= e($symbol) ?></span>
                            <input
                                type="text"
                                name="cash_received"
                                class="form-control cash-amount-input cash-edit-close-input"
                                inputmode="decimal"
                                autocomplete="off"
                                value="<?= e(number_format((float) $sessionRow['cash_received'], 2, '.', '')) ?>"
                                data-expected="<?= e((string) $expectedCash) ?>"
                                data-diff-target="editCloseDiff<?= (int) $sessionRow['id'] ?>"
                                required
                            >
                        </div>
                        <div class="cash-close-preview mt-2">
                            Difference: <strong id="editCloseDiff<?= (int) $sessionRow['id'] ?>" class="<?= (float) $sessionRow['cash_difference'] >= 0 ? 'text-profit' : 'text-loss' ?>"><?= $symbol ?> <?= money($sessionRow['cash_difference']) ?></strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
