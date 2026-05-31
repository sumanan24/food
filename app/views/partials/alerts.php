<?php
use Core\Session;
use Core\Security;
foreach (['success', 'error', 'warning'] as $type):
    $msg = Session::flash($type);
    if ($msg):
        $class = $type === 'error' ? 'danger' : $type;
?>
<div class="alert alert-<?= $class ?> alert-dismissible fade show" role="alert">
    <?= Security::escape($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; endforeach;
if (!empty($_GET['timeout'])): ?>
<div class="alert alert-warning">Session expired. Please login again.</div>
<?php endif;
if (Session::get('reset_link')): ?>
<div class="alert alert-info">
    <strong>Demo reset link:</strong>
    <a href="<?= Security::escape(Session::get('reset_link')) ?>"><?= Security::escape(Session::get('reset_link')) ?></a>
</div>
<?php Session::remove('reset_link'); endif; ?>
