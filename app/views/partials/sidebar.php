<?php
use App\Core\Auth;

$navItems = [
    ['path' => '', 'label' => 'Dashboard', 'icon' => 'speedometer2', 'match' => fn($p) => $p === ''],
    ['path' => 'pos', 'label' => 'POS Billing', 'icon' => 'receipt-cutoff', 'match' => fn($p) => str_starts_with($p, 'pos'), 'section' => 'Shop'],
    ['path' => 'cash', 'label' => 'Bill Counter', 'icon' => 'cash-stack', 'match' => fn($p) => str_starts_with($p, 'cash')],
    ['path' => 'daily-balance', 'label' => 'Daily Balance', 'icon' => 'egg-fried', 'match' => fn($p) => str_starts_with($p, 'daily-balance')],
    ['path' => 'wastage', 'label' => 'Wastage', 'icon' => 'trash', 'match' => fn($p) => str_starts_with($p, 'wastage')],
    ['path' => 'items', 'label' => 'Items', 'icon' => 'box-seam', 'match' => fn($p) => str_starts_with($p, 'items'), 'section' => 'Inventory'],
    ['path' => 'inventory', 'label' => 'Long Stock', 'icon' => 'boxes', 'match' => fn($p) => str_starts_with($p, 'inventory')],
    ['path' => 'purchases', 'label' => 'Purchases', 'icon' => 'cart-plus', 'match' => fn($p) => str_starts_with($p, 'purchases'), 'section' => 'Transactions'],
    ['path' => 'expenses', 'label' => 'Expenses', 'icon' => 'wallet2', 'match' => fn($p) => str_starts_with($p, 'expenses') && !str_starts_with($p, 'expense-categories')],
];

if (Auth::isAdmin()) {
    $navItems[] = ['path' => 'expense-categories', 'label' => 'Categories', 'icon' => 'tags', 'match' => fn($p) => str_starts_with($p, 'expense-categories')];
    $navItems[] = ['path' => 'users', 'label' => 'Users', 'icon' => 'people', 'match' => fn($p) => str_starts_with($p, 'users')];
}

$navItems[] = ['path' => 'reports', 'label' => 'Reports', 'icon' => 'file-earmark-bar-graph', 'match' => fn($p) => str_starts_with($p, 'reports'), 'section' => 'Analytics'];

$lastSection = null;
?>
<aside id="sidebar" class="sidebar" aria-label="Main navigation">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-shop"></i></div>
        <div class="brand-text">
            <span class="brand-name"><?= e($config['name']) ?></span>
            <span class="brand-tagline">Food Shop POS</span>
        </div>
        <button type="button" class="sidebar-close d-lg-none" id="sidebarClose" aria-label="Close menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($navItems as $item): ?>
            <?php
            $showSection = isset($item['section']) && $item['section'] !== $lastSection;
            if ($showSection) {
                $lastSection = $item['section'];
            }
            $isActive = ($item['match'])($currentPath);
            ?>
            <?php if ($showSection): ?>
                <div class="nav-section"><?= e($item['section']) ?></div>
            <?php endif; ?>
            <a href="<?= url($item['path']) ?>" class="sidebar-link <?= $isActive ? 'active' : '' ?>">
                <span class="link-icon"><i class="bi bi-<?= e($item['icon']) ?>"></i></span>
                <span class="link-label"><?= e($item['label']) ?></span>
                <?php if ($isActive): ?><span class="link-active-dot"></span><?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="user-avatar"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></div>
            <div class="user-info">
                <strong><?= e($user['name'] ?? '') ?></strong>
                <span class="role-badge role-<?= e($user['role'] ?? 'cashier') ?>"><?= e(ucfirst($user['role'] ?? '')) ?></span>
            </div>
        </div>
        <div class="sidebar-actions">
            <a href="<?= url('change-password') ?>" class="sidebar-action-btn" title="Change password"><i class="bi bi-key"></i></a>
            <a href="<?= url('logout') ?>" class="sidebar-action-btn logout" title="Logout"><i class="bi bi-box-arrow-right"></i></a>
        </div>
    </div>
</aside>
<div id="sidebarOverlay" class="sidebar-overlay" aria-hidden="true"></div>
