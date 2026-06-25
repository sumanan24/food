-- Allow multiple bill counter open/close cycles per day (v4)
-- Run once on existing databases.

ALTER TABLE `cash_sessions` DROP INDEX `uk_cash_session_date`;

ALTER TABLE `cash_sessions`
    ADD KEY `idx_cash_sessions_date_status` (`session_date`, `status`);

ALTER TABLE `bills`
    ADD COLUMN `cash_session_id` INT UNSIGNED NULL AFTER `user_id`,
    ADD KEY `idx_bills_cash_session` (`cash_session_id`),
    ADD CONSTRAINT `fk_bills_cash_session` FOREIGN KEY (`cash_session_id`) REFERENCES `cash_sessions` (`id`) ON DELETE SET NULL;
