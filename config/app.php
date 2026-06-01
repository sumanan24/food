<?php
/**
 * Application configuration.
 * URLs are auto-detected (localhost + cPanel). Override in config/app.local.php if needed.
 */
$config = [
    'name'               => 'Food Shop Management',
    'url'                => '', // leave empty — app_url() detects from domain + /public path
    'timezone'           => 'Asia/Colombo',
    'session_name'       => 'FOOD_SHOP_SESSION',
    'session_lifetime'   => 3600,
    'remember_days'      => 30,
    'upload_path'        => dirname(__DIR__) . '/public/uploads/products/',
    'max_upload'         => 2097152,
    'low_stock_threshold'=> 10,
    'currency'           => 'Rs.',
    'tax_rate'           => 0,
    'csrf_token_name'    => '_csrf_token',
    'debug'              => false,
];

$local = __DIR__ . '/app.local.php';
if (is_file($local)) {
    $config = array_merge($config, require $local);
}

return $config;
