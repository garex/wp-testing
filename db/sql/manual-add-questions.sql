select * from wp_posts where ID = 40;
select * from wp_posts where ID = 68;
select * from wp_t_questions;

TRUNCATE `wp_t_questions`;
INSERT INTO `wp_t_questions` (`test_id`, `question_title`) VALUES ('40', 'one');
INSERT INTO `wp_t_questions` (`test_id`, `question_title`) VALUES ('40', 'two');
INSERT INTO `wp_t_questions` (`test_id`, `question_title`) VALUES ('40', 'three');
