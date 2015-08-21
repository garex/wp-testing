INSERT into wp_t_passing_answers
SELECT 
    answer_id, wp_t_passings.passing_id
FROM
    wp_t_passing_answers
        JOIN
    wp_t_passings ON wp_t_passings.passing_id != 3;


SELECT 
    answer_id, wp_t_passings.passing_id
FROM
    wp_t_passing_answers
        JOIN
    wp_t_passings ON wp_t_passings.passing_id != 3;

DELETE FROM wp_t_passing_answers 
WHERE
    passing_id != 3;


INSERT INTO `wp_t_passings`
(
`test_id`,
`respondent_id`,
`passing_status`,
`passing_created`,
`passing_modified`,
`passing_ip`,
`passing_device_uuid`,
`passing_user_agent`)
SELECT 
`test_id`,
`respondent_id`,
`passing_status`,
`passing_created`,
`passing_modified`,
`passing_ip`,
`passing_device_uuid`,
`passing_user_agent`
from wp_t_passings
;


SELECT count(*) FROM wp_t_passings;