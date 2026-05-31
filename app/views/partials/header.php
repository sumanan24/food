<?php use Core\Security; ?>
<header class="top-header d-flex justify-content-between align-items-center px-4 py-3">
    <h1 class="h4 mb-0 page-title">
        <?= Security::escape($title ?? '') ?>
        <?php if (isCashier()): ?><span class="badge bg-info ms-2 small">Cashier — POS only</span><?php endif; ?>
    </h1>
    <div class="d-flex align-items-center gap-3">
        <button type="button" class="btn btn-sm btn-outline-secondary" id="themeToggle" title="Toggle theme">
            <i class="bi bi-moon-stars"></i>
        </button>
        <span class="badge bg-primary"><?= Security::escape(ucfirst(currentUser()['role'] ?? '')) ?></span>
    </div>
</header>
