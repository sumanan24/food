# Food Shop Management System — Deployment Guide

## Requirements
- PHP 8.0+ with PDO MySQL extension
- MySQL 5.7+ or MariaDB 10.3+
- Apache with `mod_rewrite` (or Nginx equivalent)
- cPanel shared hosting compatible (no Composer required)

## cPanel Installation

1. Upload the entire project to your hosting account (e.g. `public_html/food/`).
2. Point the domain/subdomain document root to the **`public/`** folder.
3. Visit `https://yourdomain.com/install` and complete the wizard:
   - Step 1: Database credentials (host, name, user, password)
   - Step 2: Login with default admin or create custom admin
4. Default login after fresh install:
   - **Admin:** `admin@foodshop.com` / `admin123`
   - **Cashier:** `cashier@foodshop.com` / `admin123`

## Fresh Install (SQL)
The installer runs `sql/schema.sql` and `sql/seed.sql` automatically.

## Upgrade from v1 Inventory System
If upgrading an existing installation, import `sql/migrate_v2.sql` via phpMyAdmin before using new modules.

## Module Overview

| Module | URL | Description |
|--------|-----|-------------|
| Dashboard | `/` | Sales, purchases, expenses, profit, cash in hand |
| POS Billing | `/pos` | Multi-item bills, auto bill number, print receipt |
| Cash | `/cash` | Open/close daily cash drawer |
| Daily Balance | `/daily-balance` | Opening, purchased, sold, wastage, balance |
| Wastage | `/wastage` | End-of-day food spoilage |
| Long Stock | `/inventory` | Stock ledger, valuation, reorder alerts |
| Purchases | `/purchases` | Daily purchases with supplier name |
| Expenses | `/expenses` | Electricity, salary, transport, etc. |
| Reports | `/reports` | Daily/weekly/monthly/yearly + PDF |

## Item Types

**Daily Use** (Vadai, Roll, Samosa, Tea, Coffee)
- Opening quantity per day
- Purchased + Sold + Wastage tracked
- Balance = Opening + Purchased − Sold − Wastage
- No long-term stock

**Long Use** (Biscuits, Soft Drinks, Water Bottles)
- Stock in/out with ledger
- Reorder level alerts
- Stock valuation

## Security
- PDO prepared statements
- `password_hash()` / `password_verify()`
- CSRF tokens on all forms
- Session-based authentication
- Admin vs Cashier role separation

## File Structure
```
food/
├── app/           MVC (Controllers, Models, Views, Core)
├── config/        app.php, database.local.php
├── lib/tcpdf/     PDF generation
├── public/        Web root (index.php, assets)
├── sql/           schema.sql, seed.sql, migrate_v2.sql
└── bootstrap.php
```

## PDF Reports
Reports → Daily/Monthly/Yearly → **PDF** button generates TCPDF report (bundled, no Composer).

## Troubleshooting
- **404 errors:** Ensure `.htaccess` in `public/` is uploaded and `mod_rewrite` is enabled.
- **Database error:** Verify `config/database.local.php` exists with correct credentials.
- **Blank CSS:** Hard refresh browser; check `public/assets/` folder uploaded.
