<?php
use App\Core\Auth;
$config = require CONFIG_PATH . '/app.php';
$symbol = $config['currency_symbol'] ?? 'Rs.';
$itemCount = count($items);
?>

<div class="inventory-page">
    <div class="list-toolbar">
        <div class="list-toolbar-info">
            <span class="list-toolbar-label"><i class="bi bi-box-seam me-1"></i> Total Items</span>
            <strong class="list-toolbar-value"><?= $itemCount ?></strong>
        </div>
        <?php if (Auth::isAdmin()): ?>
            <div class="page-header-actions list-toolbar-actions">
                <a href="<?= url('items/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Add Item
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($itemCount === 0): ?>
        <div class="table-card">
            <div class="empty-state">
                <i class="bi bi-box"></i>
                <p>No items yet.</p>
                <?php if (Auth::isAdmin()): ?>
                    <a href="<?= url('items/create') ?>" class="btn btn-primary btn-sm">Add First Item</a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Mobile cards -->
        <div class="inventory-mobile-list d-lg-none">
            <?php foreach ($items as $i => $item): ?>
                <?php
                $lowStock = (float) $item['current_stock'] <= 10;
                $profit = (float) $item['selling_price'] - (float) $item['cost_price'];
                ?>
                <div class="inventory-item-card">
                    <div class="inventory-item-header">
                        <div class="inventory-item-no">#<?= $i + 1 ?></div>
                        <strong class="inventory-item-name"><?= e($item['name']) ?></strong>
                        <span class="stock-badge <?= $lowStock ? 'low' : 'ok' ?>">
                            <?= money($item['current_stock']) ?> stock
                        </span>
                    </div>
                    <div class="inventory-item-prices">
                        <div class="inventory-price-box">
                            <span class="inventory-price-label">Cost</span>
                            <span class="inventory-price-value"><?= $symbol ?> <?= money($item['cost_price']) ?></span>
                        </div>
                        <div class="inventory-price-box">
                            <span class="inventory-price-label">Selling</span>
                            <span class="inventory-price-value text-profit"><?= $symbol ?> <?= money($item['selling_price']) ?></span>
                        </div>
                        <div class="inventory-price-box">
                            <span class="inventory-price-label">Margin</span>
                            <span class="inventory-price-value <?= $profit >= 0 ? 'text-profit' : 'text-loss' ?>">
                                <?= $symbol ?> <?= money($profit) ?>
                            </span>
                        </div>
                    </div>
                    <?php if (Auth::isAdmin()): ?>
                        <div class="inventory-item-actions">
                            <a href="<?= url('items/edit/' . $item['id']) ?>" class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                            <form method="POST" action="<?= url('items/delete/' . $item['id']) ?>" class="flex-fill" onsubmit="return confirm('Delete this item?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                    <i class="bi bi-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Desktop table -->
        <div class="table-card d-none d-lg-block">
            <div class="table-responsive-wrap">
                <table class="table table-striped data-table-lg mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item Name</th>
                            <th>Cost</th>
                            <th>Selling</th>
                            <th>Stock</th>
                            <?php if (Auth::isAdmin()): ?><th>Actions</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $i => $item): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><strong><?= e($item['name']) ?></strong></td>
                                <td><?= money($item['cost_price']) ?></td>
                                <td><?= money($item['selling_price']) ?></td>
                                <td>
                                    <span class="stock-badge <?= (float)$item['current_stock'] <= 10 ? 'low' : 'ok' ?>">
                                        <?= money($item['current_stock']) ?>
                                    </span>
                                </td>
                                <?php if (Auth::isAdmin()): ?>
                                    <td>
                                        <div class="gap-actions">
                                            <a href="<?= url('items/edit/' . $item['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="<?= url('items/delete/' . $item['id']) ?>" class="d-inline" onsubmit="return confirm('Delete this item?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
