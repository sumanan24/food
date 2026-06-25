<nav class="mobile-bottom-nav d-lg-none" aria-label="Quick navigation">
    <a href="<?= url() ?>" class="mob-nav-item <?= $currentPath === '' ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i><span>Home</span>
    </a>
    <a href="<?= url('pos') ?>" class="mob-nav-item <?= str_starts_with($currentPath, 'pos') ? 'active' : '' ?>">
        <i class="bi bi-receipt-cutoff"></i><span>POS</span>
    </a>
    <a href="<?= url('cash') ?>" class="mob-nav-item mob-nav-fab" aria-label="Bill Counter">
        <i class="bi bi-cash-stack"></i>
    </a>
    <a href="<?= url('daily-balance') ?>" class="mob-nav-item <?= str_starts_with($currentPath, 'daily-balance') ? 'active' : '' ?>">
        <i class="bi bi-egg-fried"></i><span>Daily</span>
    </a>
    <button type="button" class="mob-nav-item" id="mobileMenuBtn" aria-label="Open menu">
        <i class="bi bi-grid"></i><span>Menu</span>
    </button>
</nav>
