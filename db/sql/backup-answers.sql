SELECT DISTINCT
	NULL           AS answer_id,
	q.question_id  AS question_id,
    tt.term_id     AS global_answer_id,
	''             AS answer_title
FROM wp_term_taxonomy AS tt
JOIN wp_term_relationships AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id AND tt.taxonomy = 'wpt_answer'
JOIN wp_posts AS t ON tr.object_id = t.id
JOIN wp_t_questions AS q ON q.test_id = t.id
ORDER BY q.question_id, tt.term_id
;


SELECT DISTINCT
	NULL        AS answer_id,
	question_id AS question_id,
	answer_id   AS global_answer_id,
	''          AS answer_title
FROM
	wp_t_scores;

