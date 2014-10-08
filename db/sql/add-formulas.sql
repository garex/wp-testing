DELETE FROM wp_terms WHERE slug LIKE 'result%';
SELECT * FROM wp_term_taxonomy;

SELECT ID FROM wp_posts WHERE post_type = 'wpt_test' AND post_name = 'eysencks-personality-inventory-epi-extroversionintroversion' ORDER BY ID LIMIT 1;

SELECT * FROM wp_t_formulas;

DELETE FROM wp_t_formulas;

/*
'8', '105', '119', 'scale-extraversion-introversion > 50% AND scale-neuroticism-stability <= 50%'
'9', '105', '120', 'scale-extraversion-introversion > 50% AND scale-neuroticism-stability > 50%'
'10', '105', '121', 'scale-extraversion-introversion <= 50% AND scale-neuroticism-stability > 50%'
'11', '105', '122', 'scale-extraversion-introversion <= 50% AND scale-neuroticism-stability <= 50%'
*/