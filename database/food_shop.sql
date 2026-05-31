-- Food Shop Management System Database
-- PHP 8+ / MySQL 5.7+

CREATE DATABASE IF NOT EXISTS food_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE food_shop;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS sale_items;
DROP TABLE IF EXISTS sales;
DROP TABLE IF EXISTS purchases;
DROP TABLE IF EXISTS expenses;
DROP TABLE IF EXISTS expense_categories;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS suppliers;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS admins;

SET FOREIGN_KEY_CHECKS = 1;

-- Admins table (legacy / super admin)
CREATE TABLE admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin','admin') DEFAULT 'admin',
    status TINYINT(1) DEFAULT 1,
    remember_token VARCHAR(100) NULL,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_admins_email (email),
    INDEX idx_admins_status (status)
) ENGINE=InnoDB;

-- Users table (staff management)
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    role ENUM('super_admin','admin','cashier','manager') DEFAULT 'cashier',
    status TINYINT(1) DEFAULT 1,
    remember_token VARCHAR(100) NULL,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_email (email),
    INDEX idx_users_role (role)
) ENGINE=InnoDB;

CREATE TABLE password_resets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_reset_email (email),
    INDEX idx_reset_token (token)
) ENGINE=InnoDB;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_categories_name (name),
    INDEX idx_categories_status (status)
) ENGINE=InnoDB;

CREATE TABLE suppliers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    contact_number VARCHAR(20) NULL,
    address TEXT NULL,
    email VARCHAR(150) NULL,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_suppliers_name (name)
) ENGINE=InnoDB;

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    category_id INT UNSIGNED NULL,
    buying_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    selling_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    quantity INT NOT NULL DEFAULT 0,
    barcode VARCHAR(50) NULL UNIQUE,
    image VARCHAR(255) NULL,
    expiry_date DATE NULL,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_products_name (name),
    INDEX idx_products_barcode (barcode),
    INDEX idx_products_category (category_id),
    INDEX idx_products_quantity (quantity),
    INDEX idx_products_status (status)
) ENGINE=InnoDB;

CREATE TABLE sales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) NOT NULL UNIQUE,
    sale_date DATE NOT NULL,
    customer_name VARCHAR(150) NULL,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    discount DECIMAL(12,2) NOT NULL DEFAULT 0,
    tax DECIMAL(12,2) NOT NULL DEFAULT 0,
    grand_total DECIMAL(12,2) NOT NULL DEFAULT 0,
    payment_type ENUM('cash','card','upi','bank','other') DEFAULT 'cash',
    paid_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    change_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    cost_total DECIMAL(12,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    user_id INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_sales_date (sale_date),
    INDEX idx_sales_invoice (invoice_number)
) ENGINE=InnoDB;

CREATE TABLE sale_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    buying_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    line_total DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_sale_items_sale (sale_id),
    INDEX idx_sale_items_product (product_id)
) ENGINE=InnoDB;

CREATE TABLE purchases (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT UNSIGNED NULL,
    supplier_name VARCHAR(150) NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    buying_cost DECIMAL(12,2) NOT NULL,
    total_cost DECIMAL(12,2) NOT NULL,
    purchase_date DATE NOT NULL,
    invoice_number VARCHAR(50) NULL,
    notes TEXT NULL,
    user_id INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_purchases_date (purchase_date),
    INDEX idx_purchases_supplier (supplier_id)
) ENGINE=InnoDB;

CREATE TABLE expense_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE expenses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    category_id INT UNSIGNED NULL,
    category_name VARCHAR(100) NULL,
    amount DECIMAL(12,2) NOT NULL,
    expense_date DATE NOT NULL,
    notes TEXT NULL,
    user_id INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES expense_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_expenses_date (expense_date),
    INDEX idx_expenses_category (category_id)
) ENGINE=InnoDB;

CREATE TABLE settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE activity_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    user_name VARCHAR(100) NULL,
    action VARCHAR(50) NOT NULL,
    module VARCHAR(50) NOT NULL,
    details TEXT NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_logs_user (user_id),
    INDEX idx_logs_module (module),
    INDEX idx_logs_created (created_at)
) ENGINE=InnoDB;

-- Default admin: admin@foodshop.com / Admin@123
INSERT INTO users (name, email, password, role, status) VALUES
('Super Admin', 'admin@foodshop.com', '$2y$10$tyqcNPycW37EH8NIDBlyZeIBY1qpmXFzSDcRIbDGHKgrLKLvUheGu', 'super_admin', 1),
('Manager User', 'manager@foodshop.com', '$2y$10$tyqcNPycW37EH8NIDBlyZeIBY1qpmXFzSDcRIbDGHKgrLKLvUheGu', 'manager', 1),
('Cashier User', 'cashier@foodshop.com', '$2y$10$tyqcNPycW37EH8NIDBlyZeIBY1qpmXFzSDcRIbDGHKgrLKLvUheGu', 'cashier', 1);

INSERT INTO admins (name, email, password, role) VALUES
('System Admin', 'admin@foodshop.com', '$2y$10$tyqcNPycW37EH8NIDBlyZeIBY1qpmXFzSDcRIbDGHKgrLKLvUheGu', 'super_admin');

INSERT INTO categories (name, description, status) VALUES
('Beverages', 'Drinks and juices', 1),
('Snacks', 'Chips, biscuits, etc.', 1),
('Dairy', 'Milk, cheese, yogurt', 1),
('Frozen', 'Frozen food items', 1),
('Grocery', 'General grocery items', 1);

INSERT INTO suppliers (name, contact_number, address, email) VALUES
('Fresh Foods Ltd', '0771234567', '123 Main St, Colombo', 'fresh@supplier.com'),
('Daily Dairy Co', '0779876543', '45 Lake Rd, Kandy', 'dairy@supplier.com');

INSERT INTO expense_categories (name) VALUES
('Rent'), ('Utilities'), ('Salary'), ('Transport'), ('Miscellaneous');

INSERT INTO settings (setting_key, setting_value) VALUES
('shop_name', 'Food Shop'),
('shop_address', '123 Food Street, Colombo'),
('shop_phone', '+94 77 000 0000'),
('shop_email', 'info@foodshop.com'),
('currency', 'Rs.'),
('tax_rate', '0'),
('low_stock_threshold', '10'),
('invoice_prefix', 'INV'),
('theme', 'light');

-- Sample products
INSERT INTO products (name, category_id, buying_price, selling_price, quantity, barcode, status) VALUES
('Coca Cola 400ml', 1, 80, 120, 50, '8901000010001', 1),
('Lays Classic 50g', 2, 60, 100, 30, '8901000010002', 1),
('Fresh Milk 1L', 3, 180, 250, 25, '8901000010003', 1),
('Bread White', 5, 90, 140, 15, '8901000010004', 1),
('Ice Cream Vanilla', 4, 200, 350, 8, '8901000010005', 1);

INSERT INTO expenses (title, category_id, category_name, amount, expense_date, notes) VALUES
('Shop Rent', 1, 'Rent', 25000, CURDATE(), 'Monthly rent'),
('Electricity Bill', 2, 'Utilities', 3500, CURDATE(), 'May bill');
