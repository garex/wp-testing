SELECT 
	q.*,
    a.*,
    s.*    
FROM
    wp_t_scores s
        JOIN
    wp_t_answers a USING (answer_id)
        JOIN
    wp_t_questions q USING (question_id)
WHERE
    q.test_id = 8;