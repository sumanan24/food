<?php
$filterScope = $filterScope ?? 'list';
$searchPlaceholder = $searchPlaceholder ?? 'Search records...';
$filterSelects = $filterSelects ?? [];
$filterDateRange = $filterDateRange ?? false;
$selectCount = count($filterSelects);
$searchCol = $filterDateRange || $selectCount > 0 ? 'col-12 col-md-6 col-lg-4' : 'col-12';
?>
<div class="table-card filter-card list-filters mb-3">
    <div class="list-filters-head">
        <span class="list-filters-title"><i class="bi bi-funnel me-1"></i> Filter records</span>
    </div>
    <div class="list-filters-row row g-2 g-md-3 align-items-end">
        <div class="<?= $searchCol ?> list-filter-search">
            <label class="form-label" for="filter-search-<?= e($filterScope) ?>">Search</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input
                    type="search"
                    id="filter-search-<?= e($filterScope) ?>"
                    class="form-control"
                    placeholder="<?= e($searchPlaceholder) ?>"
                    data-filter-search
                    autocomplete="off"
                >
            </div>
        </div>

        <?php if ($filterDateRange): ?>
            <div class="col-6 col-md-3 col-lg-2 list-filter-field">
                <label class="form-label" for="filter-from-<?= e($filterScope) ?>">From</label>
                <input type="date" id="filter-from-<?= e($filterScope) ?>" class="form-control" data-filter-date-from>
            </div>
            <div class="col-6 col-md-3 col-lg-2 list-filter-field">
                <label class="form-label" for="filter-to-<?= e($filterScope) ?>">To</label>
                <input type="date" id="filter-to-<?= e($filterScope) ?>" class="form-control" data-filter-date-to>
            </div>
        <?php endif; ?>

        <?php foreach ($filterSelects as $select): ?>
            <div class="col-12 col-sm-6 col-lg-2 list-filter-field">
                <label class="form-label" for="filter-<?= e($filterScope) ?>-<?= e($select['name']) ?>"><?= e($select['label']) ?></label>
                <select
                    id="filter-<?= e($filterScope) ?>-<?= e($select['name']) ?>"
                    class="form-select"
                    data-filter-select
                    data-filter-attribute="<?= e($select['attr']) ?>"
                >
                    <?php foreach ($select['options'] as $value => $label): ?>
                        <option value="<?= e((string) $value) ?>"><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endforeach; ?>

        <div class="col-12 col-sm-auto list-filter-actions">
            <label class="form-label d-none d-sm-block">&nbsp;</label>
            <button type="button" class="btn btn-outline-secondary w-100 w-sm-auto" data-filter-clear>
                <i class="bi bi-x-lg me-1"></i> Clear
            </button>
        </div>
    </div>
    <div class="list-filter-status text-muted small mt-2 d-none" data-filter-status></div>
</div>
