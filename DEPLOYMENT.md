# Food Shop Inventory — cPanel Deployment Guide

## Requirements

- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache with `mod_rewrite` enabled
- **No Composer required** — TCPDF is bundled in `lib/tcpdf/`

## Project Structure

```
food/
├── app/           # MVC application code
├── config/        # Configuration (database.local.php created by installer)
├── lib/           # Bundled libraries (TCPDF for PDF reports)
├── public/        # Web document root (point domain here)
├── sql/           # Database schema
├── bootstrap.php  # App bootstrap (no Composer)
└── DEPLOYMENT.md
```

## Option A: Document Root = `public/` (Recommended)

1. Upload **all project files** to your hosting account (including the `lib/` folder).
2. In cPanel → **Domains** → select your domain → set **Document Root** to:
   ```
   /home/username/foodshop/public
   ```
3. Set folder permissions:
   ```bash
   chmod 755 public
   chmod 775 config
   ```
4. Visit `https://yourdomain.com/install` and complete the wizard.

## Option B: Subfolder in `public_html`

1. Upload the entire project to e.g. `public_html/hirosan.sicodeit.com/`.
2. Set document root to `.../public` OR access via:
   ```
   https://hirosan.sicodeit.com/public/
   ```
3. The root `.htaccess` redirects requests to `public/` automatically.

## cPanel Database Setup

1. cPanel → **MySQL Databases**
2. Create a new database (e.g. `username_foodshop`)
3. Create a MySQL user and assign **ALL PRIVILEGES** to the database
4. Use these credentials in the installation wizard

> On shared hosting, create the database manually in cPanel first. The installer will use the existing database.

## Installation Wizard

1. Navigate to `/install`
2. **Step 1:** Enter database host (`localhost`), database name, username, password
3. **Step 2:** Login with default admin or create a custom account

**Default admin:** `admin@foodshop.com` / `admin123`

## Manual SQL Import (Alternative)

1. Create database in cPanel/phpMyAdmin
2. Import `sql/schema.sql` and `sql/seed.sql`
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

## Local Development (WAMP)

1. Place project in `C:\wamp64\www\food`
2. Visit: `http://localhost/food/public/install`
3. Complete installation wizard

No `composer install` needed.

## Security Checklist

- [ ] Set document root to `public/` only (never expose `app/`, `config/`, `lib/`, `sql/`)
- [ ] Ensure `config/database.local.php` is not web-accessible
- [ ] Use HTTPS in production (enables secure session cookies)
- [ ] Use strong admin password
- [ ] Keep PHP updated

## User Roles

| Role  | Permissions |
|-------|-------------|
| Admin | Full access: items CRUD, categories, users, delete expenses |
| Staff | Sales, purchases, expenses (view/create/edit), reports, dashboard |

## Troubleshooting

| Issue | Solution |
|-------|----------|
| 404 on all pages | Enable `mod_rewrite`; check `.htaccess` in `public/` |
| Blank page | Enable PHP error log; verify PHP 8.0+ |
| Database connection failed | Verify cPanel DB name includes prefix (e.g. `user_foodshop`) |
| PDF download fails | Ensure `lib/tcpdf/` folder was uploaded with the project |
| CSS/JS not loading | Check document root points to `public/` |

## Support Files

- Full schema: `sql/schema.sql`
- Default admin seed: `sql/seed.sql`
- App config: `config/app.php` (shop name, timezone, currency)
- Database config: `config/database.local.php` (auto-generated)
