<nav class="mobile-bottom-nav d-lg-none" aria-label="Quick navigation">
    <a href="<?= url() ?>" class="mob-nav-item <?= $currentPath === '' ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i><span>Home</span>
    </a>
    <a href="<?= url('sales/create') ?>" class="mob-nav-item <?= str_starts_with($currentPath, 'sales') ? 'active' : '' ?>">
        <i class="bi bi-cash-coin"></i><span>Sale</span>
    </a>
    <a href="<?= url('purchases/create') ?>" class="mob-nav-item mob-nav-fab" aria-label="New purchase">
        <i class="bi bi-plus-lg"></i>
    </a>
    <a href="<?= url('items') ?>" class="mob-nav-item <?= str_starts_with($currentPath, 'items') ? 'active' : '' ?>">
        <i class="bi bi-box-seam"></i><span>Stock</span>
    </a>
    <button type="button" class="mob-nav-item" id="mobileMenuBtn" aria-label="Open menu">
        <i class="bi bi-grid"></i><span>Menu</span>
    </button>
</nav>
