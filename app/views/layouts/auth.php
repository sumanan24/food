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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Login') ?> - <?= e($config['name']) ?></title>
    <?php require VIEW_PATH . '/partials/head-assets.php'; ?>
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="auth-card card shadow">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="bi bi-shop display-4 text-primary"></i>
                    <h2 class="mt-2"><?= e($config['name']) ?></h2>
                </div>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= e($success) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>
                <?= $content ?>
            </div>
        </div>
    </div>
    <?php require VIEW_PATH . '/partials/footer-scripts.php'; ?>
</body>
</html>
