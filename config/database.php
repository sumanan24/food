<?php
/**
 * Database Configuration (local WAMP defaults).
 * On cPanel: copy config/database.local.php.example → database.local.php with real credentials.
 */
$config = [
    'host'     => 'localhost',
    'dbname'   => 'food_shop',
    'username' => 'root',
    'password' => '1234',
    'charset'  => 'utf8mb4',
];

$local = __DIR__ . '/database.local.php';
if (is_file($local)) {
    $config = array_merge($config, require $local);
}

return $config;
