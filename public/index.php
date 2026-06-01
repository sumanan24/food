<?php
/**
 * Food Shop Management System - Front Controller
 */

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

require ROOT_PATH . '/core/bootstrap.php';

// Autoloader (folder names are lowercase; namespaces use PascalCase — required on Linux)
spl_autoload_register(function (string $class): void {
    $map = [
        'App\\Controllers\\' => APP_PATH . '/controllers/',
        'App\\Models\\'      => APP_PATH . '/models/',
        'Core\\'             => ROOT_PATH . '/core/',
        'App\\'              => APP_PATH . '/',
    ];
    foreach ($map as $prefix => $base) {
        if (str_starts_with($class, $prefix)) {
            $file = $base . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (is_file($file)) {
                require $file;
                return;
            }
        }
    }
});

require ROOT_PATH . '/core/Security.php';
require ROOT_PATH . '/core/helpers.php';

$config = require ROOT_PATH . '/config/app.php';
date_default_timezone_set($config['timezone']);

use Core\Session;
use App\Models\UserModel;

Session::start();

// Remember me (do not crash if DB is misconfigured)
if (!isLoggedIn() && !empty($_COOKIE['remember_token'])) {
    try {
        $user = (new UserModel())->findByRememberToken($_COOKIE['remember_token']);
        if ($user) {
            Session::set('user_id', $user['id']);
            Session::set('user', ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']]);
        }
    } catch (Throwable $e) {
        error_log('[Food Shop] remember_token: ' . $e->getMessage());
    }
}

// Route path: prefer ?url= from .htaccess, else parse REQUEST_URI
if (isset($_GET['url']) && $_GET['url'] !== '') {
    $uri = '/' . trim($_GET['url'], '/');
} else {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
}
$method = $_SERVER['REQUEST_METHOD'];

require ROOT_PATH . '/routes/web.php';
