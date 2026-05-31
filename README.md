# Food Shop Management System

A complete **PHP MVC** food shop / grocery management application with POS billing, inventory, purchases, expenses, profit reports, and admin panel. Built for **WAMP / XAMPP** (Apache + MySQL + PHP 8+).

![PHP](https://img.shields.io/badge/PHP-8%2B-blue) ![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange) ![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)

## Features

| Module | Features |
|--------|----------|
| **Authentication** | Login, logout, forgot/reset password, bcrypt hashing, session timeout, remember me, role-based access |
| **Dashboard** | Today sales/expenses/purchases/profit, low stock, income charts (Chart.js), top products |
| **Products** | Full CRUD, search, filter, barcode, image upload, stock alerts |
| **Categories** | CRUD with search |
| **POS / Sales** | Cart, barcode scan, discount, tax, payment methods, auto stock deduction, print invoice, QR code |
| **Purchases** | CRUD, stock increase, supplier link |
| **Suppliers** | CRUD |
| **Expenses** | CRUD, categorized expenses |
| **Reports** | Daily/weekly/monthly/yearly profit, sales, expenses, purchases, stock; export CSV; print |
| **Users** | Admin user management (roles: super_admin, admin, manager, cashier) |
| **Settings** | Shop info, tax, theme, database backup |
| **Security** | PDO prepared statements, CSRF, XSS escape, input validation |
| **UI** | Bootstrap 5, responsive sidebar, dark/light mode, modals, toast notifications |

## Screenshots

> Place screenshots in `docs/screenshots/` after installation.

| Screen | Description |
|--------|-------------|
| Login | Modern gradient login page |
| Dashboard | Stats cards + Chart.js graphs |
| POS | Product grid + cart checkout |
| Products | Data table with filters |
| Reports | Profit breakdown with date filters |

Example paths after capture:
- `docs/screenshots/login.png`
- `docs/screenshots/dashboard.png`
- `docs/screenshots/pos.png`

## Requirements

- PHP 8.0+ (works on PHP 7.4 with minor adjustments)
- MySQL 5.7+ / MariaDB
- Apache with `mod_rewrite` enabled
- WAMP64 or XAMPP

## Installation (WAMP)

### 1. Copy project

Place the project in:

```
C:\wamp64\www\food\
```

### 2. Create database

1. Start **WAMP** (Apache + MySQL green).
2. Open **phpMyAdmin**: http://localhost/phpmyadmin
3. Import the SQL file:

```
database/food_shop.sql
```

Or run in MySQL console:

```sql
SOURCE C:/wamp64/www/food/database/food_shop.sql;
```

### 3. Configure database (if needed)

Edit `config/database.php`:

```php
'host'     => 'localhost',
'dbname'   => 'food_shop',
'username' => 'root',
'password' => '',  // your MySQL password
```

### 4. Configure base URL (if needed)

Edit `config/app.php` if your URL differs:

```php
'url' => 'http://localhost/food/public',
```

### 5. Enable Apache rewrite

Ensure `mod_rewrite` is on. The project includes `.htaccess` in root and `public/`.

### 6. Access the application

Open in browser:

```
http://localhost/food/public/login
```

Or:

```
http://localhost/food/public/
```

## Default login credentials

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@foodshop.com | Admin@123 |
| Manager | manager@foodshop.com | Admin@123 |
| Cashier | cashier@foodshop.com | Admin@123 |

### Role permissions

| Role | Access |
|------|--------|
| **Cashier** | POS billing only (create bills). Can view/print sales history. **No** edit or delete. **No** products, purchases, expenses, reports, or settings. |
| **Manager** | Full shop operations except user/settings admin |
| **Admin / Super Admin** | Full access including users and settings |

**Change passwords after first login** via Users module (admin only).

## Folder structure

```
food/
├── app/
│   ├── controllers/     # MVC Controllers
│   ├── models/          # Database models
│   └── views/           # PHP views + layouts
├── assets/              # (optional legacy)
├── backups/             # SQL backups
├── config/              # app.php, database.php
├── core/                # Framework (Router, DB, Session, Security)
├── database/            # food_shop.sql
├── public/              # Web root (index.php, assets)
├── routes/              # web.php routes
├── uploads/             # Product images (via public/uploads)
└── README.md
```

## Routes (REST-style)

| URL | Description |
|-----|-------------|
| `/login` | Admin login |
| `/dashboard` | Main dashboard |
| `/products` | Product management |
| `/categories` | Categories |
| `/sales` | POS billing |
| `/sales/history` | Sales history |
| `/purchases` | Purchases |
| `/suppliers` | Suppliers |
| `/expenses` | Expenses |
| `/reports` | Reports hub |
| `/users` | User management (admin) |
| `/settings` | Shop settings |
| `/activity-logs` | Activity logs |

## Profit formula

```
Profit = Total Sales − Product Buying Cost (from sales) − Expenses
```

Reports support **daily**, **weekly**, **monthly**, and **yearly** filters.

## Security notes

- Passwords hashed with `password_hash()` (bcrypt)
- All DB queries use **PDO prepared statements**
- Forms protected with **CSRF tokens**
- Output escaped with `htmlspecialchars()`
- Session timeout: 1 hour (configurable in `config/app.php`)

## Database backup

Super admins can create backups from **Settings → Database Backup**. Files are stored in `/backups/`.

To import data, use phpMyAdmin and import `database/food_shop.sql` or a backup file.

## Troubleshooting

| Issue | Solution |
|-------|----------|
| 404 on all pages | Enable `mod_rewrite`, check AllowOverride in Apache |
| Database connection error | Verify MySQL running, import SQL, check `config/database.php` |
| CSS/JS not loading | Confirm `config/app.php` URL matches your install path |
| Login fails | Re-import SQL or reset password via phpMyAdmin with new bcrypt hash |

Generate new password hash (WAMP PHP):

```bash
C:\wamp64\bin\php\php8.x.x\php.exe -r "echo password_hash('YourPassword', PASSWORD_BCRYPT);"
```

## Tech stack

- **Backend:** PHP 8+, OOP, MVC, PDO, Sessions
- **Frontend:** HTML5, CSS3, Bootstrap 5, jQuery, AJAX, Chart.js
- **Database:** MySQL

## License

Open source – free to use and modify for learning and commercial projects.

---

**Food Shop Management System** – Production-ready MVC application for small food shops and grocery stores.
