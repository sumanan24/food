<?php
use App\Core\Session;
$config = require CONFIG_PATH . '/app.php';
$success = Session::flash('success');
$error = Session::flash('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#ff6b35">
    <title><?= e($title ?? 'Login') ?> - <?= e($config['name']) ?></title>
    <?php require VIEW_PATH . '/partials/head-assets.php'; ?>
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="auth-layout">
            <div class="auth-showcase d-none d-md-block">
                <div class="auth-showcase-icon"><i class="bi bi-shop"></i></div>
                <h1><?= e($config['name']) ?></h1>
                <p>Manage inventory, track sales & purchases, and grow your food business — all in one beautiful dashboard.</p>
                <div class="auth-features">
                    <span class="auth-feature-pill"><i class="bi bi-box-seam"></i> Stock Control</span>
                    <span class="auth-feature-pill"><i class="bi bi-graph-up"></i> Live Reports</span>
                    <span class="auth-feature-pill"><i class="bi bi-phone"></i> Mobile Ready</span>
                </div>
            </div>
            <div class="auth-card card">
                <div class="card-body">
                    <div class="text-center d-md-none mb-4">
                        <div class="auth-showcase-icon mx-auto"><i class="bi bi-shop"></i></div>
                    </div>
                    <h2 class="auth-card-title"><?= e($title ?? 'Welcome back') ?></h2>
                    <p class="auth-card-subtitle">Sign in to your account to continue</p>
                    <?php if ($success): ?>
                        <div class="alert alert-app alert-success"><i class="bi bi-check-circle me-2"></i><?= e($success) ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-app alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= e($error) ?></div>
                    <?php endif; ?>
                    <?= $content ?>
                </div>
            </div>
        </div>
    </div>
    <?php require VIEW_PATH . '/partials/footer-scripts.php'; ?>
</body>
</html>
