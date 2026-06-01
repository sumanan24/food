<?php
/**
 * Error handling and config helpers for web requests.
 */

function app_config(): array
{
    static $config = null;
    if ($config === null) {
        $config = require dirname(__DIR__) . '/config/app.php';
    }
    return $config;
}

function app_debug(): bool
{
    $env = getenv('APP_DEBUG');
    if ($env !== false) {
        return filter_var($env, FILTER_VALIDATE_BOOLEAN);
    }
    return (bool) (app_config()['debug'] ?? false);
}

function db_config(): array
{
    static $config = null;
    if ($config === null) {
        $config = require dirname(__DIR__) . '/config/database.php';
        $local = dirname(__DIR__) . '/config/database.local.php';
        if (is_file($local)) {
            $config = array_merge($config, require $local);
        }
    }
    return $config;
}

function app_handle_exception(Throwable $e): void
{
    error_log('[Food Shop] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
    }

    if (app_debug()) {
        echo '<h1>Application Error</h1><pre style="white-space:pre-wrap">';
        echo htmlspecialchars($e->getMessage() . "\n\n" . $e->getFile() . ':' . $e->getLine() . "\n\n" . $e->getTraceAsString());
        echo '</pre>';
        return;
    }

    echo '<!DOCTYPE html><html><head><title>Error</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>';
    echo '<body class="d-flex align-items-center justify-content-center min-vh-100"><div class="text-center p-4">';
    echo '<h1 class="text-danger">Something went wrong</h1>';
    echo '<p class="text-muted">The server could not complete your request.</p>';
    if (str_contains($e->getMessage(), 'Column not found') || str_contains($e->getMessage(), '42S22')) {
        echo '<p class="small">Database schema is outdated. In phpMyAdmin run <strong>database/cpanel_upgrade.sql</strong> or re-import <strong>database/food_shop.sql</strong>.</p>';
    } else {
        echo '<p class="small">Check <strong>config/database.local.php</strong> in cPanel, or run <strong>public/check.php</strong> for details.</p>';
    }
    echo '<a href="' . htmlspecialchars(app_url_safe() . '/login') . '" class="btn btn-primary">Back to Login</a>';
    echo '</div></body></html>';
}

/** app_url() without loading helpers.php (used during fatal errors). */
function app_url_safe(): string
{
    if (function_exists('app_url')) {
        return app_url();
    }
    if (!empty($_SERVER['HTTP_HOST'])) {
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
        $dir = rtrim(dirname($script), '/');
        $scheme = $secure ? 'https' : 'http';
        return $scheme . '://' . $_SERVER['HTTP_HOST'] . ($dir === '' || $dir === '.' ? '' : $dir);
    }
    return rtrim((app_config()['url'] ?? ''), '/');
}

set_exception_handler('app_handle_exception');

set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    // Do not turn notices/deprecations into fatal 500 pages
    if (in_array($severity, [E_NOTICE, E_USER_NOTICE, E_STRICT, E_DEPRECATED, E_USER_DEPRECATED], true)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});
