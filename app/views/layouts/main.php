<?php
use App\Core\Auth;
use App\Core\Session;
use App\Core\View;

$config = require CONFIG_PATH . '/app.php';
$user = Auth::user();
$success = Session::flash('success');
$error = Session::flash('error');
$currentPath = trim($_GET['url'] ?? '', '/');
$pageTitle = $title ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#ff6b35">
    <title><?= e($pageTitle) ?> - <?= e($config['name']) ?></title>
    <?php require VIEW_PATH . '/partials/head-assets.php'; ?>
    <link href="<?= asset('vendor/datatables/css/dataTables.bootstrap5.min.css') ?>" rel="stylesheet">
</head>
<body class="app-body">
<div class="app-shell" id="appShell">
    <?php require VIEW_PATH . '/partials/sidebar.php'; ?>

    <div class="main-panel">
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="topbar-menu-btn d-lg-none" id="sidebarToggle" aria-label="Open menu">
                    <i class="bi bi-list"></i>
                </button>
                <div class="topbar-title-wrap">
                    <h1 class="topbar-title"><?= e($pageTitle) ?></h1>
                    <p class="topbar-subtitle d-none d-sm-block"><?= date('l, F j, Y') ?></p>
                </div>
            </div>
            <div class="topbar-right d-none d-md-flex">
                <div class="topbar-user-chip">
                    <span class="chip-avatar"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></span>
                    <span><?= e($user['name'] ?? '') ?></span>
                    <span class="chip-role"><?= e(ucfirst($user['role'] ?? '')) ?></span>
                </div>
            </div>
        </header>

        <main class="main-content">
            <?php if ($success): ?>
                <div class="alert alert-app alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= e($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-app alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= e($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>

    <?php require VIEW_PATH . '/partials/mobile-nav.php'; ?>
</div>

<?php require VIEW_PATH . '/partials/footer-scripts.php'; ?>
<script src="<?= asset('vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= asset('vendor/datatables/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= asset('vendor/datatables/js/dataTables.bootstrap5.min.js') ?>"></script>
<script src="<?= asset('vendor/chartjs/chart.umd.min.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
