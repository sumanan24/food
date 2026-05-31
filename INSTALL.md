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
