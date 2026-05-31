<?php use Core\Security;
$uri = $_SERVER['REQUEST_URI'] ?? '';
$active = fn($path) => str_contains($uri, $path) ? 'active' : '';
$user = currentUser();
$cashier = isCashier();
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-shop"></i>
        <span>Food Shop</span>
    </div>
    <nav class="sidebar-nav">
        <?php if ($cashier): ?>
        <a href="<?= url('sales') ?>" class="<?= $active('/sales') && !str_contains($uri,'history') ? 'active' : '' ?>"><i class="bi bi-cart-check"></i> POS Billing</a>
        <a href="<?= url('sales/history') ?>" class="<?= $active('history') ?>"><i class="bi bi-receipt"></i> Sales History</a>
        <?php else: ?>
        <a href="<?= url('dashboard') ?>" class="<?= $active('dashboard') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="<?= url('sales') ?>" class="<?= $active('/sales') && !str_contains($uri,'history') ? 'active' : '' ?>"><i class="bi bi-cart-check"></i> POS / Sales</a>
        <a href="<?= url('sales/history') ?>" class="<?= $active('history') ?>"><i class="bi bi-receipt"></i> Sales History</a>
        <a href="<?= url('products') ?>" class="<?= $active('products') ?>"><i class="bi bi-box-seam"></i> Products</a>
        <a href="<?= url('categories') ?>" class="<?= $active('categories') ?>"><i class="bi bi-tags"></i> Categories</a>
        <a href="<?= url('purchases') ?>" class="<?= $active('purchases') ?>"><i class="bi bi-truck"></i> Purchases</a>
        <a href="<?= url('suppliers') ?>" class="<?= $active('suppliers') ?>"><i class="bi bi-people"></i> Suppliers</a>
        <a href="<?= url('expenses') ?>" class="<?= $active('expenses') ?>"><i class="bi bi-wallet2"></i> Expenses</a>
        <a href="<?= url('reports') ?>" class="<?= $active('reports') ?>"><i class="bi bi-graph-up"></i> Reports</a>
        <?php if (hasRole('admin', 'super_admin')): ?>
        <hr>
        <a href="<?= url('users') ?>" class="<?= $active('users') ?>"><i class="bi bi-person-gear"></i> Users</a>
        <a href="<?= url('settings') ?>" class="<?= $active('settings') ?>"><i class="bi bi-gear"></i> Settings</a>
        <a href="<?= url('activity-logs') ?>" class="<?= $active('activity') ?>"><i class="bi bi-journal-text"></i> Activity Logs</a>
        <?php endif; ?>
        <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
        <small><?= Security::escape($user['name'] ?? '') ?> (<?= Security::escape(ucfirst(str_replace('_', ' ', $user['role'] ?? ''))) ?>)</small>
        <a href="<?= url('logout') ?>" class="text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</aside>
<button class="btn btn-primary sidebar-toggle d-lg-none" type="button" onclick="document.getElementById('sidebar').classList.toggle('show')">
    <i class="bi bi-list"></i>
</button>
