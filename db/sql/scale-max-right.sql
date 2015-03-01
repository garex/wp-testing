SELECT
	scale_id,
	MIN(minimum_in_row) AS minimum_in_column,
	SUM(maximum_in_row) AS maximum_in_column,
	SUM(sum_in_row)     AS sum_in_column
FROM (
	SELECT
		scale_id,
		MIN(IF(score_value > 0, 0, score_value)) AS minimum_in_row,
		MAX(IF(score_value > 0, score_value, 0)) AS maximum_in_row,
		SUM(score_value)                         AS sum_in_row
	FROM wp_t_scores AS s
	JOIN wp_t_answers AS a ON s.answer_id = a.answer_id
	WHERE TRUE
		AND question_id IN (59,60,61)
		AND scale_id    IN (6)
	GROUP BY scale_id, question_id, question_id
	HAVING minimum_in_row < maximum_in_row
) AS groupped
GROUP BY scale_id
;

SELECT
	scale_id,
	question_id,
	score_value
FROM wp_t_scores AS s
JOIN wp_t_answers AS a ON s.answer_id = a.answer_id
WHERE TRUE
	AND question_id IN (59,60,61)
	AND scale_id    IN (6)
;

SELECT
	scale_id,
	SUM(IF(score_value > 0, 0, score_value)) AS total_negative,
	SUM(IF(score_value > 0, score_value, 0)) AS total_positive
FROM wp_t_scores AS s
JOIN wp_t_answers AS a ON s.answer_id = a.answer_id
WHERE TRUE
	AND question_id IN (59,60,61)
	AND scale_id    IN (6)
GROUP BY scale_id
HAVING total_negative < total_positive;
