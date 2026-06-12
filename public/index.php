<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('VIEW_PATH', APP_PATH . '/views');

require ROOT_PATH . '/vendor/autoload.php';
require_once APP_PATH . '/Helpers/functions.php';

spl_autoload_register(function (string $class): void {
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

use App\Core\Router;
use App\Core\Session;

Session::start();

if (file_exists(CONFIG_PATH . '/app.php')) {
    $appConfig = require CONFIG_PATH . '/app.php';
    date_default_timezone_set($appConfig['timezone'] ?? 'UTC');
}

$installed = file_exists(CONFIG_PATH . '/database.local.php');
$requestUrl = trim((string) ($_GET['url'] ?? ''), '/');

if (!$installed && !str_starts_with($requestUrl, 'install')) {
    header('Location: ' . url('install'));
    exit;
}

$router = new Router();
require APP_PATH . '/routes.php';
$router->dispatch();
