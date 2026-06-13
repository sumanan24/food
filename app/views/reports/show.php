<?php
$config = require CONFIG_PATH . '/app.php';
$symbol = $config['currency_symbol'] ?? 'Rs.';
$periodLabel = $report['start'] === $report['end']
    ? $report['start']
    : $report['start'] . ' — ' . $report['end'];
?>

<div class="report-show">
    <div class="report-toolbar">
        <div class="report-period">
            <span class="report-period-label"><i class="bi bi-calendar3 me-1"></i> Period</span>
            <strong class="report-period-value"><?= e($periodLabel) ?></strong>
        </div>
        <div class="page-header-actions report-actions">
            <a href="<?= url('reports/pdf?type=' . $type . '&filter=' . urlencode($filter)) ?>" class="btn btn-danger" target="_blank">
                <i class="bi bi-file-earmark-pdf me-1"></i> PDF
            </a>
            <a href="<?= url('reports') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4 report-summary">
        <div class="col-6 col-xl-3">
            <div class="card stat-card stat-sales">
                <div class="card-body">
                    <div class="stat-icon d-none d-sm-flex"><i class="bi bi-cash-coin"></i></div>
                    <div>
                        <div class="stat-label">Total Sales</div>
                        <div class="stat-value text-profit"><?= $symbol ?> <?= money($report['total_sales']) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card stat-purchases">
                <div class="card-body">
                    <div class="stat-icon d-none d-sm-flex"><i class="bi bi-cart-plus"></i></div>
                    <div>
                        <div class="stat-label">Purchases</div>
                        <div class="stat-value"><?= $symbol ?> <?= money($report['total_purchases']) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card stat-expenses">
                <div class="card-body">
                    <div class="stat-icon d-none d-sm-flex"><i class="bi bi-wallet2"></i></div>
                    <div>
                        <div class="stat-label">Expenses</div>
                        <div class="stat-value"><?= $symbol ?> <?= money($report['total_expenses']) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card stat-profit">
                <div class="card-body">
                    <div class="stat-icon d-none d-sm-flex"><i class="bi bi-graph-up-arrow"></i></div>
                    <div>
                        <div class="stat-label">Net Profit</div>
                        <div class="stat-value <?= $report['profit'] >= 0 ? 'text-profit' : 'text-loss' ?>">
                            <?= $symbol ?> <?= money($report['profit']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales -->
    <div class="table-card report-section">
        <h5 class="card-title"><i class="bi bi-cash-coin"></i> Sales</h5>

        <?php if (empty($report['sales'])): ?>
            <div class="empty-state py-3"><p class="mb-0">No sales in this period.</p></div>
        <?php else: ?>
            <div data-filter-scope="report-sales">
            <?php
            $filterScope = 'report-sales';
            $searchPlaceholder = 'Search sales...';
            require VIEW_PATH . '/partials/list-filters.php';
            ?>
            <div class="report-mobile-list d-lg-none" data-filter-mobile>
                <?php foreach ($report['sales'] as $s): ?>
                    <div class="report-item-card" data-filter-item data-filter-date="<?= e($s['sale_date']) ?>">
                        <div class="report-item-top">
                            <strong class="report-item-title"><?= e($s['item_name']) ?></strong>
                            <span class="report-item-amount text-profit"><?= $symbol ?> <?= money($s['total_price']) ?></span>
                        </div>
                        <div class="report-item-meta">
                            <span><i class="bi bi-calendar3"></i> <?= e($s['sale_date']) ?></span>
                            <span><i class="bi bi-box"></i> Qty <?= money($s['quantity']) ?></span>
                            <span><i class="bi bi-person"></i> <?= e($s['user_name']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="table-responsive-wrap d-none d-lg-block">
                <table class="table table-striped data-table-lg mb-0" data-filter-table>
                    <thead>
                        <tr><th>Date</th><th>Item</th><th>Qty</th><th>Total</th><th>By</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report['sales'] as $s): ?>
                            <tr data-filter-item data-filter-date="<?= e($s['sale_date']) ?>">
                                <td><?= e($s['sale_date']) ?></td>
                                <td><?= e($s['item_name']) ?></td>
                                <td><?= money($s['quantity']) ?></td>
                                <td><strong><?= money($s['total_price']) ?></strong></td>
                                <td><?= e($s['user_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="table-card d-none border-0 shadow-none px-0" data-filter-empty>
                <div class="empty-state py-3"><p class="mb-0">No sales match your filters.</p></div>
            </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Purchases -->
    <div class="table-card report-section">
        <h5 class="card-title"><i class="bi bi-cart-plus"></i> Purchases</h5>

        <?php if (empty($report['purchases'])): ?>
            <div class="empty-state py-3"><p class="mb-0">No purchases in this period.</p></div>
        <?php else: ?>
            <div data-filter-scope="report-purchases">
            <?php
            $filterScope = 'report-purchases';
            $searchPlaceholder = 'Search purchases...';
            require VIEW_PATH . '/partials/list-filters.php';
            ?>
            <div class="report-mobile-list d-lg-none" data-filter-mobile>
                <?php foreach ($report['purchases'] as $p): ?>
                    <div class="report-item-card" data-filter-item data-filter-date="<?= e($p['purchase_date']) ?>">
                        <div class="report-item-top">
                            <strong class="report-item-title"><?= e($p['item_name']) ?></strong>
                            <span class="report-item-amount"><?= $symbol ?> <?= money($p['total_cost']) ?></span>
                        </div>
                        <div class="report-item-meta">
                            <span><i class="bi bi-calendar3"></i> <?= e($p['purchase_date']) ?></span>
                            <span><i class="bi bi-box"></i> Qty <?= money($p['quantity']) ?></span>
                            <span><i class="bi bi-person"></i> <?= e($p['user_name']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="table-responsive-wrap d-none d-lg-block">
                <table class="table table-striped data-table-lg mb-0" data-filter-table>
                    <thead>
                        <tr><th>Date</th><th>Item</th><th>Qty</th><th>Total</th><th>By</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report['purchases'] as $p): ?>
                            <tr data-filter-item data-filter-date="<?= e($p['purchase_date']) ?>">
                                <td><?= e($p['purchase_date']) ?></td>
                                <td><?= e($p['item_name']) ?></td>
                                <td><?= money($p['quantity']) ?></td>
                                <td><strong><?= money($p['total_cost']) ?></strong></td>
                                <td><?= e($p['user_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="table-card d-none border-0 shadow-none px-0" data-filter-empty>
                <div class="empty-state py-3"><p class="mb-0">No purchases match your filters.</p></div>
            </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Expenses -->
    <div class="table-card report-section mb-0">
        <h5 class="card-title"><i class="bi bi-wallet2"></i> Expenses</h5>

        <?php if (empty($report['expenses'])): ?>
            <div class="empty-state py-3"><p class="mb-0">No expenses in this period.</p></div>
        <?php else: ?>
            <div data-filter-scope="report-expenses">
            <?php
            $filterScope = 'report-expenses';
            $searchPlaceholder = 'Search expenses...';
            $reportExpenseCategories = ['' => 'All categories'];
            foreach (array_unique(array_column($report['expenses'], 'category_name')) as $catName) {
                $reportExpenseCategories[$catName] = $catName;
            }
            $filterSelects = [[
                'name' => 'category',
                'label' => 'Category',
                'attr' => 'data-filter-category',
                'options' => $reportExpenseCategories,
            ]];
            require VIEW_PATH . '/partials/list-filters.php';
            ?>
            <div class="report-mobile-list d-lg-none" data-filter-mobile>
                <?php foreach ($report['expenses'] as $exp): ?>
                    <div class="report-item-card" data-filter-item data-filter-date="<?= e($exp['expense_date']) ?>" data-filter-category="<?= e($exp['category_name']) ?>">
                        <div class="report-item-top">
                            <strong class="report-item-title"><?= e($exp['title']) ?></strong>
                            <span class="report-item-amount text-loss"><?= $symbol ?> <?= money($exp['amount']) ?></span>
                        </div>
                        <div class="report-item-meta">
                            <span><i class="bi bi-calendar3"></i> <?= e($exp['expense_date']) ?></span>
                            <span><i class="bi bi-tag"></i> <?= e($exp['category_name']) ?></span>
                            <span><i class="bi bi-person"></i> <?= e($exp['user_name']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="table-responsive-wrap d-none d-lg-block">
                <table class="table table-striped data-table-lg mb-0" data-filter-table>
                    <thead>
                        <tr><th>Date</th><th>Title</th><th>Category</th><th>Amount</th><th>By</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report['expenses'] as $exp): ?>
                            <tr data-filter-item data-filter-date="<?= e($exp['expense_date']) ?>" data-filter-category="<?= e($exp['category_name']) ?>">
                                <td><?= e($exp['expense_date']) ?></td>
                                <td><?= e($exp['title']) ?></td>
                                <td><?= e($exp['category_name']) ?></td>
                                <td><strong><?= money($exp['amount']) ?></strong></td>
                                <td><?= e($exp['user_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="table-card d-none border-0 shadow-none px-0" data-filter-empty>
                <div class="empty-state py-3"><p class="mb-0">No expenses match your filters.</p></div>
            </div>
            </div>
        <?php endif; ?>
    </div>
</div>
