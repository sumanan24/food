-- Migration v2: Food Shop Management System upgrade
-- Run this ONCE on existing v1 databases before using new features.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Update user roles: staff -> cashier
ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin', 'cashier', 'staff') NOT NULL DEFAULT 'cashier';
UPDATE `users` SET `role` = 'cashier' WHERE `role` = 'staff';
ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin', 'cashier') NOT NULL DEFAULT 'cashier';

-- Extend items table
ALTER TABLE `items`
    ADD COLUMN IF NOT EXISTS `item_type` ENUM('daily', 'long') NOT NULL DEFAULT 'long' AFTER `name`,
    ADD COLUMN IF NOT EXISTS `reorder_level` DECIMAL(12, 2) NOT NULL DEFAULT 10.00 AFTER `current_stock`,
    ADD COLUMN IF NOT EXISTS `unit` VARCHAR(20) NOT NULL DEFAULT 'pcs' AFTER `reorder_level`;

-- Extend purchases
ALTER TABLE `purchases`
    ADD COLUMN IF NOT EXISTS `supplier_name` VARCHAR(150) NULL AFTER `user_id`;

-- Extend sales with bill reference
ALTER TABLE `sales`
    ADD COLUMN IF NOT EXISTS `bill_id` INT UNSIGNED NULL AFTER `user_id`,
    ADD KEY IF NOT EXISTS `idx_sales_bill` (`bill_id`);

CREATE TABLE IF NOT EXISTS `daily_openings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `balance_date` DATE NOT NULL,
    `item_id` INT UNSIGNED NOT NULL,
    `opening_qty` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `user_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_daily_opening` (`balance_date`, `item_id`),
    CONSTRAINT `fk_daily_openings_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_daily_openings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `bills` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `bill_number` VARCHAR(30) NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `subtotal` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `discount` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `total_amount` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `payment_method` ENUM('cash', 'card', 'upi', 'other') NOT NULL DEFAULT 'cash',
    `bill_date` DATE NOT NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_bills_number` (`bill_number`),
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
    CONSTRAINT `fk_bill_items_bill` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bill_items_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT
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
    CONSTRAINT `fk_stock_ledger_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_stock_ledger_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cash_sessions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `session_date` DATE NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `opening_balance` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `total_sales` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `total_expenses` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `cash_received` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `closing_balance` DECIMAL(12, 2) NULL,
    `cash_difference` DECIMAL(12, 2) NULL,
    `status` ENUM('open', 'closed') NOT NULL DEFAULT 'open',
    `opened_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `closed_at` TIMESTAMP NULL,
    `notes` TEXT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_cash_session_date` (`session_date`),
    CONSTRAINT `fk_cash_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
