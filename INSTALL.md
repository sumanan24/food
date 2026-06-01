# Quick Installation Guide

## Step 1: Start WAMP
- Launch WAMP64
- Wait until icon is **green** (Apache + MySQL running)

## Step 2: Import database
1. Open http://localhost/phpmyadmin
2. Click **Import**
3. Choose file: `C:\wamp64\www\food\database\food_shop.sql`
4. Click **Go**

## Step 3: Open application
```
http://localhost/food/public/login
```
(or `http://localhost/food/` after root rewrite)

## Production (cPanel / hirosan.sicodeit.com)

1. Upload the full project into `public_html` (so you have `public_html/public/index.php`, `app/`, `config/`, etc.).
2. In cPanel → **MySQL Databases**, create a database and user; import `database/food_shop.sql`.
3. Edit `config/database.php` with the cPanel DB name, username, and password (not `root` / `1234`).
4. Set PHP **8.0+** in cPanel → **Select PHP Version**.
5. Open **http://yourdomain.com/public/login** (or point the domain document root to the `public` folder and use `/login`).
6. After uploading, if `/public/login` returns **500**, ensure `public/.htaccess` has **no** `RewriteBase /food/public/` line (use the version from this repo).

## Step 4: Login
- **Email:** admin@foodshop.com
- **Password:** Admin@123

## Optional: PHP 8
If using PHP 8 in WAMP, left-click WAMP tray → PHP → select PHP 8.x.

## Virtual host (optional)
Point document root to `C:\wamp64\www\food\public` for cleaner URLs like `http://foodshop.local/login`.

Update `config/app.php`:
```php
'url' => 'http://foodshop.local',
```
