<?php use Core\Security; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Print</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>@media print { .no-print { display: none !important; } }</style>
</head>
<body class="p-4">
<?= $content ?>
<div class="no-print mt-3 text-center">
    <button onclick="window.print()" class="btn btn-primary">Print</button>
    <a href="<?= url('sales/history') ?>" class="btn btn-secondary">Back</a>
</div>
</body>
</html>
