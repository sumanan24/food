<?php
use Core\Router;

$router = new Router();

// Auth (no staff middleware)
$router->get('/', 'AuthController@loginForm');
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout', ['auth']);
$router->get('/forgot-password', 'AuthController@forgotForm');
$router->post('/forgot-password', 'AuthController@forgotSend');
$router->get('/reset-password/{token}', 'AuthController@resetForm');
$router->post('/reset-password', 'AuthController@resetPassword');

// Dashboard — staff only (cashiers use POS)
$router->get('/dashboard', 'DashboardController@index', ['auth', 'staff']);
$router->get('/api/dashboard/stats', 'DashboardController@stats', ['auth', 'staff']);

// Products — staff only
$router->get('/products', 'ProductController@index', ['auth', 'staff']);
$router->get('/products/create', 'ProductController@create', ['auth', 'staff']);
$router->post('/products/store', 'ProductController@store', ['auth', 'staff']);
$router->get('/products/edit/{id}', 'ProductController@edit', ['auth', 'staff']);
$router->post('/products/update/{id}', 'ProductController@update', ['auth', 'staff']);
$router->post('/products/delete/{id}', 'ProductController@delete', ['auth', 'staff']);
$router->get('/products/view/{id}', 'ProductController@show', ['auth', 'staff']);
$router->get('/api/products/search', 'ProductController@search', ['auth', 'staff']);

// Barcode API — cashiers need this for POS
$router->get('/api/products/barcode/{code}', 'ProductController@barcode', ['auth']);

// Categories — staff only
$router->get('/categories', 'CategoryController@index', ['auth', 'staff']);
$router->post('/categories/store', 'CategoryController@store', ['auth', 'staff']);
$router->post('/categories/update/{id}', 'CategoryController@update', ['auth', 'staff']);
$router->post('/categories/delete/{id}', 'CategoryController@delete', ['auth', 'staff']);

// Sales / POS — all authenticated users (cashiers: add bills only)
$router->get('/sales', 'SaleController@pos', ['auth']);
$router->get('/sales/history', 'SaleController@history', ['auth']);
$router->get('/sales/view/{id}', 'SaleController@show', ['auth']);
$router->post('/sales/checkout', 'SaleController@checkout', ['auth']);
$router->get('/sales/invoice/{id}', 'SaleController@invoice', ['auth']);
$router->get('/sales/print/{id}', 'SaleController@printBill', ['auth']);

// Purchases — staff only
$router->get('/purchases', 'PurchaseController@index', ['auth', 'staff']);
$router->get('/purchases/create', 'PurchaseController@create', ['auth', 'staff']);
$router->post('/purchases/store', 'PurchaseController@store', ['auth', 'staff']);
$router->get('/purchases/edit/{id}', 'PurchaseController@edit', ['auth', 'staff']);
$router->post('/purchases/update/{id}', 'PurchaseController@update', ['auth', 'staff']);
$router->post('/purchases/delete/{id}', 'PurchaseController@delete', ['auth', 'staff']);

// Suppliers — staff only
$router->get('/suppliers', 'SupplierController@index', ['auth', 'staff']);
$router->post('/suppliers/store', 'SupplierController@store', ['auth', 'staff']);
$router->post('/suppliers/update/{id}', 'SupplierController@update', ['auth', 'staff']);
$router->post('/suppliers/delete/{id}', 'SupplierController@delete', ['auth', 'staff']);

// Expenses — staff only
$router->get('/expenses', 'ExpenseController@index', ['auth', 'staff']);
$router->get('/expenses/create', 'ExpenseController@create', ['auth', 'staff']);
$router->post('/expenses/store', 'ExpenseController@store', ['auth', 'staff']);
$router->get('/expenses/edit/{id}', 'ExpenseController@edit', ['auth', 'staff']);
$router->post('/expenses/update/{id}', 'ExpenseController@update', ['auth', 'staff']);
$router->post('/expenses/delete/{id}', 'ExpenseController@delete', ['auth', 'staff']);

// Reports — staff only
$router->get('/reports', 'ReportController@index', ['auth', 'staff']);
$router->get('/reports/profit', 'ReportController@profit', ['auth', 'staff']);
$router->get('/reports/sales', 'ReportController@sales', ['auth', 'staff']);
$router->get('/reports/expenses', 'ReportController@expenses', ['auth', 'staff']);
$router->get('/reports/purchases', 'ReportController@purchases', ['auth', 'staff']);
$router->get('/reports/stock', 'ReportController@stock', ['auth', 'staff']);
$router->get('/reports/export', 'ReportController@export', ['auth', 'staff']);

// Users — admin only
$router->get('/users', 'UserController@index', ['auth', 'admin']);
$router->get('/users/create', 'UserController@create', ['auth', 'admin']);
$router->post('/users/store', 'UserController@store', ['auth', 'admin']);
$router->get('/users/edit/{id}', 'UserController@edit', ['auth', 'admin']);
$router->post('/users/update/{id}', 'UserController@update', ['auth', 'admin']);
$router->post('/users/delete/{id}', 'UserController@delete', ['auth', 'admin']);

// Settings — admin only
$router->get('/settings', 'SettingController@index', ['auth', 'admin']);
$router->post('/settings/update', 'SettingController@update', ['auth', 'admin']);
$router->post('/settings/backup', 'SettingController@backup', ['auth', 'admin']);

// Activity logs — admin only
$router->get('/activity-logs', 'ActivityController@index', ['auth', 'admin']);

$router->dispatch($uri, $method);
