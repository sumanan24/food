<?php use Core\Security; ?>
<div class="card shadow-sm">
    <table class="table table-sm mb-0">
        <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Module</th><th>Details</th><th>IP</th></tr></thead>
        <tbody>
        <?php foreach ($logs as $log): ?>
        <tr>
            <td><?= $log['created_at'] ?></td>
            <td><?= Security::escape($log['user_name'] ?? '') ?></td>
            <td><?= Security::escape($log['action']) ?></td>
            <td><?= Security::escape($log['module']) ?></td>
            <td><?= Security::escape($log['details'] ?? '') ?></td>
            <td><?= Security::escape($log['ip_address'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
