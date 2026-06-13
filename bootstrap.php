<?php

declare(strict_types=1);

/**
 * Application bootstrap — no Composer required on the server.
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}

if (!defined('APP_PATH')) {
    define('APP_PATH', ROOT_PATH . '/app');
}

if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', ROOT_PATH . '/config');
}

if (!defined('VIEW_PATH')) {
    define('VIEW_PATH', APP_PATH . '/views');
}

if (!defined('LIB_PATH')) {
    define('LIB_PATH', ROOT_PATH . '/lib');
}

require_once APP_PATH . '/Helpers/functions.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    $baseDir = APP_PATH . '/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
