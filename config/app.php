<?php
/**
 * Application Configuration
 */
return [
    'name'          => 'Food Shop Management',
    // Overridden at runtime by app_url() from HTTP_HOST; set APP_URL env on server if needed
    'url'           => 'http://localhost/food/public',
    'timezone'      => 'Asia/Colombo',
    'session_name'  => 'FOOD_SHOP_SESSION',
    'session_lifetime' => 3600, // 1 hour
    'remember_days' => 30,
    'upload_path'   => dirname(__DIR__) . '/public/uploads/products/',
    'upload_url'    => '/food/public/uploads/products/',
    'max_upload'    => 2097152, // 2MB
    'low_stock_threshold' => 10,
    'currency'      => 'Rs.',
    'tax_rate'      => 0, // percentage, configurable in settings
    'csrf_token_name' => '_csrf_token',
];
