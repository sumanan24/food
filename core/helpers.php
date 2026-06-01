<?php
/**
 * Global helper functions (must not be in a namespace)
 */

function request_scheme(): string
{
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (!empty($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);
    return $secure ? 'https' : 'http';
}

function app_url(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    $env = getenv('APP_URL');
    if ($env !== false && $env !== '') {
        return $base = rtrim($env, '/');
    }
    $config = require dirname(__DIR__) . '/config/app.php';
    if (!empty($config['url'])) {
        return $base = rtrim($config['url'], '/');
    }
    if (!empty($_SERVER['HTTP_HOST'])) {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
        $dir = rtrim(dirname($script), '/');
        return $base = request_scheme() . '://' . $_SERVER['HTTP_HOST']
            . ($dir === '' || $dir === '.' ? '' : $dir);
    }
    return $base = 'http://localhost/food/public';
}

function upload_url(string $file = ''): string
{
    return url('uploads/products/' . ltrim($file, '/'));
}

function url(string $path = ''): string
{
    if ($path === '') {
        return app_url();
    }
    // Already absolute — do not prepend base (avoids /public/http://other-site/...)
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    $path = ltrim($path, '/');
    // Avoid /food/public/food/ when path is mistakenly the project folder name
    $basePath = parse_url(app_url(), PHP_URL_PATH) ?: '';
    $projectFolder = $basePath !== '' ? basename(dirname($basePath)) : '';
    if ($projectFolder !== '' && $projectFolder !== 'public' && $path === $projectFolder) {
        $path = '';
    }
    return app_url() . ($path !== '' ? '/' . $path : '');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/** Safe path for redirects after CSRF failure (same host only). */
function safe_redirect_target(string $fallback = 'login'): string
{
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if ($referer === '') {
        return $fallback;
    }
    if (!preg_match('#^https?://#i', $referer)) {
        return ltrim($referer, '/') ?: $fallback;
    }
    $refHost = parse_url($referer, PHP_URL_HOST);
    $appHost = parse_url(app_url(), PHP_URL_HOST);
    if (!$refHost || !$appHost || strcasecmp($refHost, $appHost) !== 0) {
        return $fallback;
    }
    $refPath = parse_url($referer, PHP_URL_PATH) ?: '/';
    $basePath = parse_url(app_url(), PHP_URL_PATH) ?: '';
    if ($basePath !== '' && str_starts_with($refPath, $basePath)) {
        $refPath = substr($refPath, strlen($basePath)) ?: '/';
    }
    $path = ltrim($refPath, '/');
    return $path !== '' ? $path : $fallback;
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
