<?php
use App\Core\Auth;
use App\Core\Session;
use App\Core\View;

$config = require CONFIG_PATH . '/app.php';
$user = Auth::user();
$success = Session::flash('success');
$error = Session::flash('error');
$currentPath = trim($_GET['url'] ?? '', '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Dashboard') ?> - <?= e($config['name']) ?></title>
    <?php require VIEW_PATH . '/partials/head-assets.php'; ?>
    <link href="<?= asset('vendor/datatables/css/dataTables.bootstrap5.min.css') ?>" rel="stylesheet">
</head>
<body>
<div class="d-flex" id="wrapper">
    <nav id="sidebar" class="bg-dark text-white">
        <div class="sidebar-heading px-3 py-4 border-bottom border-secondary">
            <i class="bi bi-shop me-2"></i><?= e($config['name']) ?>
        </div>
        <ul class="list-unstyled px-2 py-3">
            <li><a href="<?= url() ?>" class="nav-link <?= $currentPath === '' ? 'active' : '' ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
            <li class="text-uppercase text-muted small px-3 mt-3 mb-1">Inventory</li>
            <li><a href="<?= url('items') ?>" class="nav-link <?= str_starts_with($currentPath, 'items') ? 'active' : '' ?>"><i class="bi bi-box-seam me-2"></i>Items</a></li>
            <li class="text-uppercase text-muted small px-3 mt-3 mb-1">Transactions</li>
            <li><a href="<?= url('purchases') ?>" class="nav-link <?= str_starts_with($currentPath, 'purchases') ? 'active' : '' ?>"><i class="bi bi-cart-plus me-2"></i>Purchases</a></li>
            <li><a href="<?= url('sales') ?>" class="nav-link <?= str_starts_with($currentPath, 'sales') ? 'active' : '' ?>"><i class="bi bi-cash-coin me-2"></i>Sales</a></li>
            <li><a href="<?= url('expenses') ?>" class="nav-link <?= str_starts_with($currentPath, 'expenses') ? 'active' : '' ?>"><i class="bi bi-wallet2 me-2"></i>Expenses</a></li>
            <?php if (Auth::isAdmin()): ?>
            <li><a href="<?= url('expense-categories') ?>" class="nav-link <?= str_starts_with($currentPath, 'expense-categories') ? 'active' : '' ?>"><i class="bi bi-tags me-2"></i>Categories</a></li>
            <li><a href="<?= url('users') ?>" class="nav-link <?= str_starts_with($currentPath, 'users') ? 'active' : '' ?>"><i class="bi bi-people me-2"></i>Users</a></li>
            <?php endif; ?>
            <li class="text-uppercase text-muted small px-3 mt-3 mb-1">Reports</li>
            <li><a href="<?= url('reports') ?>" class="nav-link <?= str_starts_with($currentPath, 'reports') ? 'active' : '' ?>"><i class="bi bi-file-earmark-bar-graph me-2"></i>Reports</a></li>
        </ul>
    </nav>

    <div id="page-content" class="flex-grow-1">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-3">
            <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="text-muted small d-none d-md-inline">
                    <i class="bi bi-person-circle me-1"></i><?= e($user['name'] ?? '') ?>
                    <span class="badge bg-<?= ($user['role'] ?? '') === 'admin' ? 'primary' : 'secondary' ?>"><?= e(ucfirst($user['role'] ?? '')) ?></span>
                </span>
                <a href="<?= url('change-password') ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-key"></i></a>
                <a href="<?= url('logout') ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>

        <main class="container-fluid p-4">
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= e($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= e($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>
</div>

<?php require VIEW_PATH . '/partials/footer-scripts.php'; ?>
<script src="<?= asset('vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= asset('vendor/datatables/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= asset('vendor/datatables/js/dataTables.bootstrap5.min.js') ?>"></script>
<script src="<?= asset('vendor/chartjs/chart.umd.min.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
