-- Food Shop Management System v2
-- MySQL 5.7+ / MariaDB 10.3+

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'cashier') NOT NULL DEFAULT 'cashier',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `items` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(150) NOT NULL,
    `item_type` ENUM('daily', 'long') NOT NULL DEFAULT 'long',
    `cost_price` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `selling_price` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `current_stock` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `reorder_level` DECIMAL(12, 2) NOT NULL DEFAULT 10.00,
    `unit` VARCHAR(20) NOT NULL DEFAULT 'pcs',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_items_name` (`name`),
    KEY `idx_items_type` (`item_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `daily_openings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `balance_date` DATE NOT NULL,
    `item_id` INT UNSIGNED NOT NULL,
    `opening_qty` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `user_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_daily_opening` (`balance_date`, `item_id`),
    KEY `idx_daily_openings_date` (`balance_date`),
    CONSTRAINT `fk_daily_openings_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_daily_openings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `purchases` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `item_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `supplier_name` VARCHAR(150) NULL,
    `quantity` DECIMAL(12, 2) NOT NULL,
    `unit_cost` DECIMAL(12, 2) NOT NULL,
    `total_cost` DECIMAL(12, 2) NOT NULL,
    `purchase_date` DATE NOT NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_purchases_date` (`purchase_date`),
    KEY `idx_purchases_item` (`item_id`),
    CONSTRAINT `fk_purchases_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_purchases_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `bills` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `bill_number` VARCHAR(30) NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `cash_session_id` INT UNSIGNED NULL,
    `subtotal` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `discount` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `total_amount` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `payment_method` ENUM('cash', 'card', 'upi', 'other') NOT NULL DEFAULT 'cash',
    `bill_date` DATE NOT NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_bills_number` (`bill_number`),
    KEY `idx_bills_date` (`bill_date`),
    KEY `idx_bills_cash_session` (`cash_session_id`),
    CONSTRAINT `fk_bills_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `bill_items` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `bill_id` INT UNSIGNED NOT NULL,
    `item_id` INT UNSIGNED NOT NULL,
    `quantity` DECIMAL(12, 2) NOT NULL,
    `unit_price` DECIMAL(12, 2) NOT NULL,
    `total_price` DECIMAL(12, 2) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_bill_items_bill` (`bill_id`),
    CONSTRAINT `fk_bill_items_bill` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bill_items_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sales` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `item_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `bill_id` INT UNSIGNED NULL,
    `quantity` DECIMAL(12, 2) NOT NULL,
    `unit_price` DECIMAL(12, 2) NOT NULL,
    `total_price` DECIMAL(12, 2) NOT NULL,
    `sale_date` DATE NOT NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_sales_date` (`sale_date`),
    KEY `idx_sales_bill` (`bill_id`),
    CONSTRAINT `fk_sales_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_sales_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_sales_bill` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `wastage` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `item_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `quantity` DECIMAL(12, 2) NOT NULL,
    `wastage_date` DATE NOT NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_wastage_date` (`wastage_date`),
    CONSTRAINT `fk_wastage_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_wastage_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `stock_ledger` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `item_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `transaction_type` ENUM('opening', 'purchase', 'sale', 'wastage', 'adjustment') NOT NULL,
    `reference_id` INT UNSIGNED NULL,
    `quantity_in` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `quantity_out` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `balance_after` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `ledger_date` DATE NOT NULL,
    `notes` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_stock_ledger_item_date` (`item_id`, `ledger_date`),
    CONSTRAINT `fk_stock_ledger_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_stock_ledger_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cash_sessions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `session_date` DATE NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `counter_person_name` VARCHAR(100) NOT NULL DEFAULT '',
    `opening_balance` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `total_sales` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `total_expenses` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `cash_received` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `closing_balance` DECIMAL(12, 2) NULL,
    `cash_difference` DECIMAL(12, 2) NULL,
    `closed_by_name` VARCHAR(100) NULL,
    `status` ENUM('open', 'closed') NOT NULL DEFAULT 'open',
    `opened_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `closed_at` TIMESTAMP NULL,
    `notes` TEXT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_cash_sessions_date_status` (`session_date`, `status`),
    CONSTRAINT `fk_cash_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `bills`
    ADD CONSTRAINT `fk_bills_cash_session` FOREIGN KEY (`cash_session_id`) REFERENCES `cash_sessions` (`id`) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS `expense_categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_expense_categories_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `expenses` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `amount` DECIMAL(12, 2) NOT NULL,
    `expense_date` DATE NOT NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_expenses_date` (`expense_date`),
    CONSTRAINT `fk_expenses_category` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_expenses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
