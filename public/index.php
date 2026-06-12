<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('VIEW_PATH', APP_PATH . '/views');
define('PUBLIC_PATH', __DIR__);

$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
define('BASE_URL', rtrim($basePath, '/'));

$autoload = ROOT_PATH . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    http_response_code(500);
    echo '<h1>Dependencies not installed</h1>';
    echo '<p>Run <code>composer install</code> in the project folder:</p>';
    echo '<pre>cd ' . htmlspecialchars(ROOT_PATH, ENT_QUOTES, 'UTF-8') . "\ncomposer install</pre>";
    exit;
}
require $autoload;
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
