select * from wp_t_questions where test_id = 5;
/*
# question_id, test_id, question_title
'60', '5', 'q1'
'61', '5', 'q2'
'62', '5', 'q3'
'63', '5', 'q4'
'64', '5', 'q5'
*/

select * from wp_t_answers where question_id in (select question_id from wp_t_questions where test_id = 5);
/*
# answer_id, question_id, global_answer_id, answer_title, answer_sort
'132', '60', '2', NULL, '0'
'133', '60', '3', NULL, '1'
'134', '61', '2', NULL, '0'
'135', '61', '3', NULL, '1'
'136', '62', '2', NULL, '0'
'137', '62', '3', NULL, '1'
'138', '63', '2', NULL, '0'
'139', '63', '3', NULL, '1'
'140', '64', '2', NULL, '0'
'141', '64', '3', NULL, '1'
*/