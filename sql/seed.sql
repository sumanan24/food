-- Default admin and seed data
-- Email: admin@foodshop.com | Password: admin123

INSERT IGNORE INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Administrator', 'admin@foodshop.com', '$2y$10$lsseu2k2fv6F8ybRXqY.Zu/QOPSeNU/0WTEtTH9NmQtriq517Z/Hu', 'admin');

INSERT IGNORE INTO `expense_categories` (`name`) VALUES
('Rent'),
('Utilities'),
('Salaries'),
('Transport'),
('Miscellaneous');
