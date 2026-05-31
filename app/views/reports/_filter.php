<?php use Core\Security; ?>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="get" class="row g-2 align-items-end" id="reportFilter">
            <div class="col-auto">
                <label class="form-label small">Period</label>
                <select name="period" class="form-select form-select-sm">
                    <?php foreach (['daily','weekly','monthly','yearly'] as $p): ?>
                    <option value="<?= $p ?>" <?= ($period ?? 'daily') === $p ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto"><label class="form-label small">From</label><input type="date" name="from" class="form-control form-control-sm" value="<?= Security::escape($from ?? '') ?>"></div>
            <div class="col-auto"><label class="form-label small">To</label><input type="date" name="to" class="form-control form-control-sm" value="<?= Security::escape($to ?? date('Y-m-d')) ?>"></div>
            <div class="col-auto"><button class="btn btn-sm btn-primary">Apply</button></div>
        </form>
    </div>
</div>
