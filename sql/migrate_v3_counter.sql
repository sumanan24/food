-- Bill counter fields for cash_sessions (v3)
-- Run once on existing databases.

ALTER TABLE `cash_sessions`
    ADD COLUMN `counter_person_name` VARCHAR(100) NOT NULL DEFAULT '' AFTER `user_id`;

ALTER TABLE `cash_sessions`
    ADD COLUMN `closed_by_name` VARCHAR(100) NULL AFTER `cash_difference`;
