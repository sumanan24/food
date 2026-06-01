-- cPanel / phpMyAdmin: run once if sale_items has no product_name column
-- Select your database first, then Import or run this SQL.

ALTER TABLE sale_items
    ADD COLUMN product_name VARCHAR(200) NOT NULL DEFAULT '' AFTER product_id;

UPDATE sale_items si
INNER JOIN products p ON p.id = si.product_id
SET si.product_name = p.name
WHERE si.product_name = '';
