-- 1 try delete
BEGIN;
DELETE FROM wp_t_fields;
SELECT COUNT(*) FROM wp_t_fields;
ROLLBACK;
SELECT COUNT(*) FROM wp_t_fields;

-- 2 revert migration
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE wp_t_field_values;
TRUNCATE TABLE wp_t_fields;
SET FOREIGN_KEY_CHECKS = 1;

DROP TABLE wp_t_field_values;
DROP TABLE wp_t_fields;
DELETE FROM wp_t_schema_migrations where version = '20150426194928';
