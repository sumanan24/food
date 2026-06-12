# Food Shop Inventory — cPanel Deployment Guide

## Requirements

- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache with `mod_rewrite` enabled
- Composer (for local setup; vendor can be uploaded to server)

## Project Structure

```
food/
├── app/           # MVC application code
├── config/        # Configuration (database.local.php created by installer)
├── public/        # Web document root (point domain here)
├── sql/           # Database schema
├── vendor/        # Composer dependencies (TCPDF)
├── composer.json
└── DEPLOYMENT.md
```

## Option A: Document Root = `public/` (Recommended)

1. Upload all project files to your hosting account (e.g. `/home/username/foodshop/`).
2. In cPanel → **Domains** → select your domain → set **Document Root** to:
   ```
   /home/username/foodshop/public
   ```
3. **Dependencies auto-install on first visit** — upload files without `vendor/` and open your site URL. PHP will download TCPDF automatically (or run Composer if shell access is available).

   Optional manual install via SSH:
   ```bash
   cd /home/username/foodshop
   composer install --no-dev --optimize-autoloader
   ```
4. Set folder permissions:
   ```bash
   chmod 755 public
   chmod 775 config
   chmod 775 storage
   ```
   The project root must be writable so `vendor/` can be created during auto-install.
5. Visit `https://yourdomain.com/install` and complete the wizard.

## Option B: Subfolder in `public_html`

1. Upload the entire `food` folder to `public_html/food/`.
2. Set document root to `public_html/food/public` OR access via:
   ```
   https://yourdomain.com/food/public/
   ```
3. The root `.htaccess` redirects requests to `public/` automatically when using the parent folder URL.

## cPanel Database Setup

1. cPanel → **MySQL Databases**
2. Create a new database (e.g. `username_foodshop`)
3. Create a MySQL user and assign **ALL PRIVILEGES** to the database
4. Use these credentials in the installation wizard

> On shared hosting, create the database manually in cPanel first. The installer will use the existing database.

## Installation Wizard

1. Navigate to `/install`
2. **Step 1:** Enter database host (`localhost`), database name, username, password
3. **Step 2:** Create the admin account
4. Login and start using the system

After installation, delete or protect the install routes by ensuring `config/database.local.php` exists (the wizard redirects to login if already installed).

## Manual SQL Import (Alternative)

If you prefer manual setup:

1. Create database in cPanel/phpMyAdmin
2. Import `sql/schema.sql`
3. Create `config/database.local.php`:

```php
<?php

declare(strict_types=1);

return [
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'your_database_name',
    'username' => 'your_db_user',
    'password' => 'your_db_password',
];
```

4. Insert admin user via phpMyAdmin or run installation step 2 only.

## Local Development (WAMP)

1. Place project in `C:\wamp64\www\food`
2. Run:
   ```bash
   composer install
   ```
3. Visit: `http://localhost/food/public/install`
4. Complete installation wizard

## Security Checklist

- [ ] Set document root to `public/` only (never expose `app/`, `config/`, `sql/`)
- [ ] Ensure `config/database.local.php` is not web-accessible
- [ ] Use HTTPS in production (enables secure session cookies)
- [ ] Use strong admin password
- [ ] Keep PHP updated

## User Roles

| Role  | Permissions |
|-------|-------------|
| Admin | Full access: items CRUD, categories, users, delete expenses |
| Staff | Sales, purchases, expenses (view/create/edit), reports, dashboard |

## Features Summary

- **Dashboard:** Today sales, purchases, expenses, profit, Chart.js graphs
- **Inventory:** Item CRUD with cost/selling price and stock tracking
- **Purchases:** Auto stock increase with history
- **Sales:** Auto stock decrease with insufficient-stock validation
- **Expenses:** Categories and expense CRUD
- **Reports:** Daily, weekly, monthly, yearly with TCPDF download
- **Security:** Password hashing, CSRF tokens, PDO prepared statements, secure sessions

## Troubleshooting

| Issue | Solution |
|-------|----------|
| 404 on all pages | Enable `mod_rewrite`; check `.htaccess` in `public/` |
| Blank page | Enable PHP error log; verify PHP 8.0+ |
| Database connection failed | Verify cPanel DB name includes prefix (e.g. `user_foodshop`) |
| PDF download fails | Refresh the site once (auto-install) or run `composer install`; ensure `vendor/` is writable |
| Dependencies not installed | Set `chmod 775` on project root and `storage/`; enable PHP `zip` and `curl` extensions |
| CSS/JS not loading | Check document root points to `public/` |

## Support Files

- Full schema: `sql/schema.sql`
- App config: `config/app.php` (shop name, timezone, currency)
- Database config: `config/database.local.php` (auto-generated)
