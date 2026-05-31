<?php
/**
 * Fallback when mod_rewrite is disabled — use: /food/public/login.php
 */
$_GET['url'] = 'login';
require __DIR__ . '/index.php';
