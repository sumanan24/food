<?php

declare(strict_types=1);

define('PUBLIC_PATH', __DIR__);

require dirname(__DIR__) . '/bootstrap.php';

$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
define('BASE_URL', rtrim($basePath, '/'));

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
