<!DOCTYPE html>
<html><head><title>404</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
<div class="text-center"><h1>404</h1><p>Page not found</p><a href="<?= function_exists('url') ? url('login') : '/food/public/login' ?>" class="btn btn-primary">Go Home</a></div>
</body></html>
