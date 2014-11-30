-- new
SELECT
	scale_id,
	SUM(IF(score_value > 0, 0, score_value)) AS total_negative,
	SUM(IF(score_value > 0, score_value, 0)) AS total_positive
FROM wp_t_scores AS s
JOIN wp_t_answers AS a ON s.answer_id = a.answer_id
WHERE TRUE
	AND question_id IN (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,0)
	AND scale_id    IN (107,108,109)
GROUP BY scale_id
HAVING total_negative < total_positive;

-- old
SELECT
	scale_id,
	SUM(IF(score_value > 0, 0, score_value)) AS total_negative,
	SUM(IF(score_value > 0, score_value, 0)) AS total_positive
FROM wp_t_scores
WHERE TRUE
	AND question_id IN (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,0)
	AND scale_id    IN (4,5,6)
GROUP BY scale_id
HAVING total_negative < total_positive;