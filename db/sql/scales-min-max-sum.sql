SELECT 
	SUM(IF(score_value > 0, 0, score_value)) AS total_negative,
	SUM(IF(score_value > 0, score_value, 0)) AS total_positive
FROM
    wp_t_scores
WHERE
    question_id IN (84 , 85, 0)
        AND scale_id = 15
GROUP BY scale_id
HAVING total_positive > total_negative;