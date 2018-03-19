-- All table names here use default table prefix "wp_"
-- It's defined in your wp-config.php by string like:
--    $table_prefix = 'wp_';
-- If your setting not default, then just replace in this script "wp_" onto your setting.

-- !!! WARNING !!!
-- This script will erase completely your's database part, related to wp-testing plugin.
-- So if you alerady has some tests/passings/scores, then you must not run this script.
-- !!! WARNING !!!

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS wp_t_schema_migrations;
DROP TABLE IF EXISTS wp_t_field_values;
DROP TABLE IF EXISTS wp_t_formulas;
DROP TABLE IF EXISTS wp_t_passing_answers;
DROP TABLE IF EXISTS wp_t_passings;
DROP TABLE IF EXISTS wp_t_scores;
DROP TABLE IF EXISTS wp_t_sections;
DROP TABLE IF EXISTS wp_t_answers;
DROP TABLE IF EXISTS wp_t_fields;
DROP TABLE IF EXISTS wp_t_questions;
DROP TABLE IF EXISTS wp_t_computed_variables;

SET FOREIGN_KEY_CHECKS = 1;

DELETE FROM wp_postmeta where meta_key like 'wpt_%';
DELETE FROM wp_posts WHERE post_type = 'wpt_test';
DELETE FROM wp_term_taxonomy where taxonomy like 'wpt_%';
DELETE FROM wp_term_relationships WHERE term_taxonomy_id NOT IN (SELECT term_taxonomy_id FROM wp_term_taxonomy);
DELETE FROM wp_terms WHERE term_id NOT IN (SELECT term_id FROM wp_term_taxonomy);
