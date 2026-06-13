<?php use App\Core\Auth; ?>

<div class="page-header">
    <h1 class="h3 mb-0">Inventory Items</h1>
    <?php if (Auth::isAdmin()): ?>
        <div class="page-header-actions">
            <a href="<?= url('items/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Item
            </a>
        </div>
    <?php endif; ?>
</div>

<div class="table-card">
    <div class="table-responsive-wrap">
        <table class="table table-striped data-table">
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
