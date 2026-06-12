<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <?php require VIEW_PATH . '/partials/head-assets.php'; ?>
</head>
<body class="bg-light">
    <div class="container text-center py-5">
        <h1 class="display-1 text-muted">404</h1>
        <p class="lead">The page you are looking for was not found.</p>
        <a href="<?= url() ?>" class="btn btn-primary">Go to Dashboard</a>
    </div>
</body>
</html>
