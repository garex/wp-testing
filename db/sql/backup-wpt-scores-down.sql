SELECT 
    a.global_answer_id AS answer_id,
    a.question_id,
    s.scale_id,
    s.score_value
FROM
    wp_testing_3_7.wp_t_scores AS s
        JOIN
    wp_testing_3_7.wp_t_answers AS a ON s.answer_id = a.answer_id
        AND a.global_answer_id IS NOT NULL
;

SELECT 
    *
FROM
    wpti.wp_t_scores;

-- ==========================================================================================
-- ==========================================================================================

SELECT
    a.global_answer_id AS answer_id,
    a.question_id,
    pa.passing_id
FROM
    wp_testing_3_7.wp_t_passing_answers AS pa
        JOIN
    wp_testing_3_7.wp_t_answers AS a ON pa.answer_id = a.answer_id
        AND a.global_answer_id IS NOT NULL
;


SELECT 
    *
FROM
    wpti.wp_t_passing_answers;

