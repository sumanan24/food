-- Food Shop Management System - Sample Data

INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Administrator', 'admin@foodshop.com', '$2y$10$cbpwaUcW4maKfOJL53YREuuorMUBGfSfR3vS0cUFuFqB4ufSOblNi', 'admin'),
('Cashier One', 'cashier@foodshop.com', '$2y$10$cbpwaUcW4maKfOJL53YREuuorMUBGfSfR3vS0cUFuFqB4ufSOblNi', 'cashier');
-- Default password for both: admin123

INSERT INTO `items` (`name`, `item_type`, `cost_price`, `selling_price`, `current_stock`, `reorder_level`, `unit`) VALUES
('Vadai', 'daily', 8.00, 15.00, 0, 0, 'pcs'),
('Roll', 'daily', 12.00, 25.00, 0, 0, 'pcs'),
('Samosa', 'daily', 6.00, 12.00, 0, 0, 'pcs'),
('Tea', 'daily', 3.00, 10.00, 0, 0, 'cup'),
('Coffee', 'daily', 5.00, 15.00, 0, 0, 'cup'),
('Biscuits', 'long', 25.00, 40.00, 50, 10, 'pack'),
('Soft Drink', 'long', 20.00, 35.00, 48, 12, 'bottle'),
('Water Bottle', 'long', 10.00, 20.00, 60, 15, 'bottle'),
('Chocolate', 'long', 30.00, 50.00, 25, 8, 'pcs'),
('Packaged Snack', 'long', 15.00, 25.00, 40, 10, 'pack');

INSERT INTO `expense_categories` (`name`, `description`) VALUES
('Electricity', 'Power bills'),
('Water', 'Water supply'),
('Salary', 'Staff salaries'),
('Transport', 'Delivery and transport'),
('Gas', 'Cooking gas'),
('Other', 'Miscellaneous expenses');

INSERT INTO `daily_openings` (`balance_date`, `item_id`, `opening_qty`, `user_id`) VALUES
(CURDATE(), 1, 100, 1),
(CURDATE(), 2, 80, 1),
(CURDATE(), 3, 60, 1),
(CURDATE(), 4, 200, 1),
(CURDATE(), 5, 100, 1);

INSERT INTO `cash_sessions` (`session_date`, `user_id`, `counter_person_name`, `opening_balance`, `status`) VALUES
(CURDATE(), 1, 'Administrator', 5000.00, 'open');
