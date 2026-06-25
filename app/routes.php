<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CashController;
use App\Controllers\DailyBalanceController;
use App\Controllers\DashboardController;
use App\Controllers\ExpenseCategoryController;
use App\Controllers\ExpenseController;
use App\Controllers\InstallController;
use App\Controllers\InventoryReportController;
use App\Controllers\ItemController;
use App\Controllers\PosController;
use App\Controllers\PurchaseController;
use App\Controllers\ReportController;
use App\Controllers\SaleController;
use App\Controllers\UserController;
use App\Controllers\WastageController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\RoleMiddleware;

/** @var \App\Core\Router $router */

// Installation
$router->get('/install', [InstallController::class, 'index']);
$router->post('/install/database', [InstallController::class, 'database']);
$router->post('/install/admin', [InstallController::class, 'admin']);

// Auth (guest only)
$router->get('/login', [AuthController::class, 'loginForm'], [GuestMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [GuestMiddleware::class]);

// Authenticated routes
$router->get('/', [DashboardController::class, 'index'], [AuthMiddleware::class]);
$router->get('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);
$router->get('/change-password', [AuthController::class, 'changePasswordForm'], [AuthMiddleware::class]);
$router->post('/change-password', [AuthController::class, 'changePassword'], [AuthMiddleware::class]);

// POS / Billing
$router->get('/pos', [PosController::class, 'index'], [AuthMiddleware::class]);
$router->post('/pos/store', [PosController::class, 'store'], [AuthMiddleware::class]);
$router->get('/pos/receipt/{id}', [PosController::class, 'receipt'], [AuthMiddleware::class]);
$router->get('/pos/history', [PosController::class, 'history'], [AuthMiddleware::class]);

// Cash Open / Close
$router->get('/cash', [CashController::class, 'index'], [AuthMiddleware::class]);
$router->post('/cash/open', [CashController::class, 'open'], [AuthMiddleware::class]);
$router->post('/cash/close', [CashController::class, 'close'], [AuthMiddleware::class]);

// Daily Food Balance
$router->get('/daily-balance', [DailyBalanceController::class, 'index'], [AuthMiddleware::class]);
$router->post('/daily-balance/opening', [DailyBalanceController::class, 'saveOpening'], [AuthMiddleware::class]);

// Wastage
$router->get('/wastage', [WastageController::class, 'index'], [AuthMiddleware::class]);
$router->post('/wastage/store', [WastageController::class, 'store'], [AuthMiddleware::class]);

// Long Use Inventory
$router->get('/inventory', [InventoryReportController::class, 'index'], [AuthMiddleware::class]);
$router->get('/inventory/ledger/{id}', [InventoryReportController::class, 'ledger'], [AuthMiddleware::class]);

// Items
$router->get('/items', [ItemController::class, 'index'], [AuthMiddleware::class]);
$router->get('/items/create', [ItemController::class, 'create'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
$router->post('/items/store', [ItemController::class, 'store'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
$router->get('/items/edit/{id}', [ItemController::class, 'edit'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
$router->post('/items/update/{id}', [ItemController::class, 'update'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
$router->post('/items/delete/{id}', [ItemController::class, 'delete'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);

// Purchases
$router->get('/purchases', [PurchaseController::class, 'index'], [AuthMiddleware::class]);
$router->get('/purchases/create', [PurchaseController::class, 'create'], [AuthMiddleware::class]);
$router->post('/purchases/store', [PurchaseController::class, 'store'], [AuthMiddleware::class]);
$router->get('/purchases/history', [PurchaseController::class, 'history'], [AuthMiddleware::class]);

// Sales (legacy single-item)
$router->get('/sales', [SaleController::class, 'index'], [AuthMiddleware::class]);
$router->get('/sales/create', [SaleController::class, 'create'], [AuthMiddleware::class]);
$router->post('/sales/store', [SaleController::class, 'store'], [AuthMiddleware::class]);
$router->get('/sales/history', [SaleController::class, 'history'], [AuthMiddleware::class]);

// Expense Categories
$router->get('/expense-categories', [ExpenseCategoryController::class, 'index'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
$router->post('/expense-categories/store', [ExpenseCategoryController::class, 'store'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
$router->post('/expense-categories/update/{id}', [ExpenseCategoryController::class, 'update'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
$router->post('/expense-categories/delete/{id}', [ExpenseCategoryController::class, 'delete'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);

// Expenses
$router->get('/expenses', [ExpenseController::class, 'index'], [AuthMiddleware::class]);
$router->get('/expenses/create', [ExpenseController::class, 'create'], [AuthMiddleware::class]);
$router->post('/expenses/store', [ExpenseController::class, 'store'], [AuthMiddleware::class]);
$router->get('/expenses/edit/{id}', [ExpenseController::class, 'edit'], [AuthMiddleware::class]);
$router->post('/expenses/update/{id}', [ExpenseController::class, 'update'], [AuthMiddleware::class]);
$router->post('/expenses/delete/{id}', [ExpenseController::class, 'delete'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);

// Users (admin only)
$router->get('/users', [UserController::class, 'index'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
$router->get('/users/create', [UserController::class, 'create'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
$router->post('/users/store', [UserController::class, 'store'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);

// Reports
$router->get('/reports', [ReportController::class, 'index'], [AuthMiddleware::class]);
$router->get('/reports/daily', [ReportController::class, 'daily'], [AuthMiddleware::class]);
$router->get('/reports/weekly', [ReportController::class, 'weekly'], [AuthMiddleware::class]);
$router->get('/reports/monthly', [ReportController::class, 'monthly'], [AuthMiddleware::class]);
$router->get('/reports/yearly', [ReportController::class, 'yearly'], [AuthMiddleware::class]);
$router->get('/reports/pdf', [ReportController::class, 'pdf'], [AuthMiddleware::class]);
