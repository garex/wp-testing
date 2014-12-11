-- SHOW TABLE STATUS;

-- DROP SCHEMA IF EXISTS wp_testing2; CREATE SCHEMA wp_testing2;

-- USE wp_testing;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS wp_t_answers;
DROP TABLE IF EXISTS wp_t_answers_parameters;
DROP TABLE IF EXISTS wp_t_parameters;
DROP TABLE IF EXISTS wp_t_questions;
DROP TABLE IF EXISTS wp_t_scales;
DROP TABLE IF EXISTS wp_t_scales_tests;
DROP TABLE IF EXISTS wp_t_schema_migrations;
DROP TABLE IF EXISTS wp_t_tests;

DROP TABLE IF EXISTS wp_t_formulas;
DROP TABLE IF EXISTS wp_t_passing_answers;
DROP TABLE IF EXISTS wp_t_passings;
DROP TABLE IF EXISTS wp_t_scores;

SET FOREIGN_KEY_CHECKS = 1;

DELETE FROM wp_posts WHERE post_title LIKE '%EPI%';
DELETE FROM wp_terms WHERE term_id > 1;
DELETE FROM wp_postmeta WHERE post_id NOT IN (
	SELECT ID FROM wp_posts
);

DELETE FROM wp_term_relationships WHERE object_id NOT IN (
	SELECT ID FROM wp_posts
);
DELETE FROM wp_term_taxonomy where taxonomy like 'wpt\_%';

