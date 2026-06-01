<?php
/**
 * Server diagnostic — open once, then DELETE this file from production.
 */
declare(strict_types=1);

header('Content-Type: text/plain; charset=UTF-8');

echo "PHP version: " . PHP_VERSION . (version_compare(PHP_VERSION, '8.0.0', '>=') ? " (OK)\n" : " (need 8.0+)\n");

$root = dirname(__DIR__);
$configPath = $root . '/config/database.php';
$localPath = $root . '/config/database.local.php';

if (!is_file($configPath)) {
    echo "database.php: MISSING\n";
    exit;
}

$config = require $configPath;
if (is_file($localPath)) {
    $config = array_merge($config, require $localPath);
    echo "database.local.php: loaded\n";
} else {
    echo "database.local.php: not found (create from database.local.php.example on server)\n";
}

echo "DB host: {$config['host']}\n";
echo "DB name: {$config['dbname']}\n";
echo "DB user: {$config['username']}\n";

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    new PDO($dsn, $config['username'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Database: CONNECTED\n";
} catch (Throwable $e) {
    echo "Database: FAILED — " . $e->getMessage() . "\n";
}

$htaccess = __DIR__ . '/.htaccess';
if (is_file($htaccess)) {
    $ht = file_get_contents($htaccess);
    echo strpos($ht, 'RewriteBase /food') !== false
        ? ".htaccess: BAD (still has RewriteBase /food/public — remove it)\n"
        : ".htaccess: OK\n";
} else {
    echo ".htaccess: MISSING\n";
}

echo "\nIf all OK, use: " . (isset($_SERVER['HTTP_HOST']) ? 'http' : '') . "://"
    . ($_SERVER['HTTP_HOST'] ?? 'yourdomain.com')
    . rtrim(dirname(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/public/check.php')), '/')
    . "/login\n";
echo "\nDelete public/check.php after fixing.\n";
