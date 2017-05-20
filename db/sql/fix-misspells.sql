SELECT REPLACE(question_title, 'otherpeople', 'other people') FROM wp_t_questions WHERE test_id = 4 AND question_title LIKE '%otherpeople%';
SELECT REPLACE(question_title, 'feelingyou', 'feeling you') FROM wp_t_questions WHERE test_id = 4 AND question_title LIKE '%feelingyou%';
SELECT REPLACE(question_title, 'upin', 'up in') FROM wp_t_questions WHERE test_id = 4 AND question_title LIKE '%upin%';
SELECT REPLACE(question_title, 'knewyou', 'knew you') FROM wp_t_questions WHERE test_id = 4 AND question_title LIKE '%knewyou%';
SELECT REPLACE(question_title, 'toa', 'to a') FROM wp_t_questions WHERE test_id = 4 AND question_title LIKE '%toa%';
SELECT REPLACE(question_title, 'hear?', 'heart?') FROM wp_t_questions WHERE test_id = 4 AND question_title LIKE '%hear?%';