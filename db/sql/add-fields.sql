SELECT 
    @LAST_POST_ID := ID
FROM
    wp_posts
WHERE
    post_status != 'auto-draft'
        AND post_type = 'wpt_test'
ORDER BY ID DESC
LIMIT 1;

INSERT INTO `wp_t_fields` (`field_id`, `test_id`, `field_title`, `field_type`, `field_is_required`, `field_sort`, `field_clarification`)
VALUES
	('1', @LAST_POST_ID, 'Name1', 'text',  '1', '1', 'We should know how we can appeal to you'),
	('2', @LAST_POST_ID, 'Email', 'email', '0', '2', 'We will use it only for sending results')
;

INSERT INTO `wp_t_field_values` (`field_id`, `passing_id`, `field_value`) VALUES ('1', '1', '1');
