<?php
use Core\Security;
use Core\Session;
$config = require dirname(__DIR__, 3) . '/config/app.php';
$theme = (isset($settings) ? ($settings['theme'] ?? null) : null) ?? Session::get('theme', 'light');
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="<?= Security::escape($theme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Security::escape($title ?? 'Food Shop') ?> | <?= Security::escape($config['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
    <script>window.APP_URL = '<?= url('') ?>'; window.CSRF_TOKEN = '<?= Security::csrfToken() ?>';</script>
</head>
<body class="app-body">
<?php require dirname(__DIR__) . '/partials/sidebar.php'; ?>
<div class="main-content">
    <?php require dirname(__DIR__) . '/partials/header.php'; ?>
    <main class="container-fluid py-4">
        <?php require dirname(__DIR__) . '/partials/alerts.php'; ?>
        <?= $content ?>
    </main>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="<?= asset('js/app.js') ?>"></script>
<?php if (!empty($pageScript)): ?><?= $pageScript ?><?php endif; ?>
</body>
</html>
