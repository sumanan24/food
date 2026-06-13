<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>404 - Page Not Found</title>
    <?php require VIEW_PATH . '/partials/head-assets.php'; ?>
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="auth-card card error-card">
            <div class="card-body text-center">
                <div class="auth-showcase-icon mx-auto mb-3"><i class="bi bi-compass"></i></div>
                <h1 class="error-code">404</h1>
                <p class="auth-card-subtitle mb-4">The page you are looking for was not found.</p>
                <a href="<?= url() ?>" class="btn btn-primary btn-auth w-100">
                    <i class="bi bi-house me-2"></i> Go to Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>
