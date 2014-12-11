SELECT DISTINCT
    NULL AS answer_id,
    question_id AS question_id,
    answer_id AS global_answer_id,
    '' AS answer_title
FROM
    wp_t_scores;

DROP TABLE IF EXISTS wp_t_scores_backup;
CREATE TABLE IF NOT EXISTS wp_t_scores_backup;
SELECT 
    wp_t_answer.answer_id AS answer_id,
    scale_id              AS scale_id,
    score_value           AS score_value
FROM
    wp_t_scores,
    wp_t_answer
WHERE TRUE
	AND wp_t_scores.answer_id = wp_t_answer.global_answer_id
	AND wp_t_scores.question_id = wp_t_answer.question_id
;