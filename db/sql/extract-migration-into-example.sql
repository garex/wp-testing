SELECT * FROM wp_terms WHERE term_id IN (
	SELECT term_id FROM wp_term_taxonomy WHERE taxonomy LIKE 'wpt\_%'
);
SELECT * FROM wp_term_relationships WHERE term_taxonomy_id IN (
	SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE taxonomy LIKE 'wpt\_%'
);
SELECT * FROM wp_term_taxonomy WHERE taxonomy LIKE 'wpt\_%';

DELETE FROM wp_term_relationships WHERE term_taxonomy_id NOT IN (
	SELECT term_taxonomy_id FROM wp_term_taxonomy
);

SELECT ID FROM wp_posts WHERE post_type = 'wpt_test' ORDER BY ID DESC LIMIT 1;

DELETE FROM wp_posts WHERE post_parent IN (
	SELECT ID FROM wp_posts WHERE post_type = 'wpt_test'
);

-- @see prev. SQL for:
-- 	wp_t_questions
-- 	wp_t_scores