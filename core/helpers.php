<?php
/**
 * Global helper functions (must not be in a namespace)
 */

function url(string $path = ''): string
{
    $config = require dirname(__DIR__) . '/config/app.php';
    $base = rtrim($config['url'], '/');
    return $base . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function old(string $key, string $default = ''): string
{
    return Core\Security::escape(Core\Session::get('_old')[$key] ?? $default);
}

function isLoggedIn(): bool
{
    return Core\Session::has('user_id');
}

function currentUser(): ?array
{
    return Core\Session::get('user');
}

function hasRole(string ...$roles): bool
{
    $user = currentUser();
    return $user && in_array($user['role'] ?? '', $roles, true);
}

function isCashier(): bool
{
    return hasRole('cashier');
}

/** Manager, admin, super_admin — full management access */
function isStaff(): bool
{
    return hasRole('manager', 'admin', 'super_admin');
}

/** Can edit or delete records (not cashiers) */
function canEditDelete(): bool
{
    return isStaff();
}
