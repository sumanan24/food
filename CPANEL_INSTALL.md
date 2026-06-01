# Install Food Shop on cPanel

## Requirements

- PHP **8.0+** (8.1 or 8.3 recommended)
- MySQL / MariaDB
- Apache `mod_rewrite` enabled (default on cPanel)

---

## Method A — Recommended (clean URLs)

Point the domain **document root** to the `public` folder.

### 1. Upload files

Upload the **entire project** to your account, for example:

```
/home/username/food/
├── app/
├── config/
├── core/
├── database/
├── public/          ← document root will point here
├── routes/
└── ...
```

Do **not** put only `public/` inside `public_html` unless you also upload `app`, `config`, and `core` **above** `public_html` and point the domain outside `public_html` (see cPanel “Domains” → document root).

**Typical setup:** upload to `public_html` so you have:

```
public_html/
├── app/
├── config/
├── core/
├── database/
├── public/
│   ├── index.php
│   └── .htaccess
└── .htaccess
```

### 2. Document root (best URLs)

cPanel → **Domains** → your domain → **Document Root** → set to:

```
public_html/public
```

Then your site opens as:

- `https://yourdomain.com/login`
- `https://yourdomain.com/dashboard`

### 3. MySQL database

1. cPanel → **MySQL® Databases**
2. Create database (e.g. `username_foodshop`)
3. Create user with a strong password
4. **Add user to database** → ALL PRIVILEGES
5. **phpMyAdmin** → select database → **Import** → `database/food_shop.sql`

### 4. Database config

Copy on the server:

```
config/database.local.php.example  →  config/database.local.php
```

Edit `config/database.local.php`:

```php
<?php
return [
    'host'     => 'localhost',
    'dbname'   => 'username_foodshop',
    'username' => 'username_fooduser',
    'password' => 'your_password_here',
    'charset'  => 'utf8mb4',
];
```

### 5. PHP version

cPanel → **Select PHP Version** (or **MultiPHP Manager**) → choose **8.1** or **8.3**.

### 6. Permissions

```text
public/uploads/products/   → 755 or 775 (writable by PHP)
```

### 7. Test

1. Open `https://yourdomain.com/check.php` (if document root is `public/`)
2. Confirm: `Database: CONNECTED`, `Dashboard query: OK`
3. **Delete** `public/check.php`
4. Login: `https://yourdomain.com/login`  
   - Email: `admin@foodshop.com`  
   - Password: `Admin@123`

---

## Method B — Without changing document root

Leave document root as `public_html` (project root).

URLs will include `/public/`:

- `https://yourdomain.com/public/login`

Steps 3–7 are the same. Root `.htaccess` sends `/` to `public/`.

---

## Git deploy (cPanel)

1. cPanel → **Git™ Version Control** → clone your repository into `public_html` or `food/`
2. Ensure `config/database.local.php` exists on the server (create manually; never commit passwords)
3. Import database once in phpMyAdmin
4. Set PHP 8+ and document root to `public/` if possible

---

## Files you must configure on the server

| File | Purpose |
|------|---------|
| `config/database.local.php` | cPanel MySQL name, user, password |
| `config/app.local.php` | optional (`debug`, fixed `url`) |

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| 500 on `/public/login` | Remove `RewriteBase /food/public/` from `public/.htaccess`; use repo version |
| “Something went wrong” after login | Upload latest `app/models/ProductModel.php`; in phpMyAdmin run `database/cpanel_upgrade.sql` if `check.php` says `product_name` is missing |
| Controller not found | Upload latest `public/index.php` (Linux case-sensitive autoload) |
| Database connection failed | Fix `database.local.php`; check user added to database |
| 404 on all pages | Enable `mod_rewrite`; check `public/.htaccess` exists |

Temporary debug in `config/app.local.php`:

```php
<?php
return ['debug' => true];
```

Remove or set `false` after fixing.

---

## Security checklist

- [ ] Delete `public/check.php` after testing
- [ ] Use `database.local.php` (not plain `root` password in `database.php`)
- [ ] Set `'debug' => false` in production
- [ ] Change default admin password after first login

---

## Local WAMP (Windows)

See [INSTALL.md](INSTALL.md) — use `http://localhost/food/public/login`.
