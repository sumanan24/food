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
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="theme-color" content="#ff6b35">
    <title><?= e($title ?? 'Install') ?> - <?= e($config['name']) ?></title>
    <?php require VIEW_PATH . '/partials/head-assets.php'; ?>
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="auth-layout">
            <div class="auth-showcase d-none d-md-block">
                <div class="auth-showcase-icon"><i class="bi bi-gear"></i></div>
                <h1>Setup Wizard</h1>
                <p>Get your food shop inventory system running in just two quick steps.</p>
                <div class="auth-features">
                    <span class="auth-feature-pill"><i class="bi bi-database"></i> Auto DB Setup</span>
                    <span class="auth-feature-pill"><i class="bi bi-shield-check"></i> Secure</span>
                </div>
            </div>
            <div class="auth-card card" style="max-width:520px;">
                <div class="card-body">
                    <div class="text-center d-md-none mb-3">
                        <div class="auth-showcase-icon mx-auto"><i class="bi bi-gear"></i></div>
                    </div>
                    <h2 class="auth-card-title">Installation</h2>
                    <p class="auth-card-subtitle"><?= e($config['name']) ?></p>
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
