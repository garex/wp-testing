/*
-- title                                now should be
wpt_result_page_show_test_description   1   0
wpt_test_page_show_progress_meter       0   1
wpt_result_page_show_scales_diagram     0   1
*/

SELECT 
    ID
FROM
    wp_posts
WHERE
    post_type = 'wpt_test'
        AND post_title = 'Eysenck’s Personality Inventory (EPI) (Extroversion/Introversion)'
ORDER BY ID LIMIT 1;

SELECT 
    *
FROM
    wp_postmeta
WHERE
    meta_key IN ('wpt_test_page_show_progress_meter' , 'wpt_result_page_show_scales_diagram',
        'wpt_result_page_show_test_description')
	AND post_id = 4
;


UPDATE wp_posts AS p, wp_postmeta AS m
SET m.meta_value = 1
WHERE TRUE
    AND p.post_type  = 'wpt_test'
    AND p.post_title = 'Eysenck’s Personality Inventory (EPI) (Extroversion/Introversion)'
	AND m.post_id = p.ID
	AND m.meta_key IN ('wpt_test_page_show_progress_meter' , 'wpt_result_page_show_scales_diagram', 'wpt_result_page_show_test_description')
;

SELECT
    ID, wp_postmeta.*
FROM
    wp_posts, wp_postmeta
WHERE TRUE
    AND post_type  = 'wpt_test'
    AND post_title = 'Eysenck’s Personality Inventory (EPI) (Extroversion/Introversion)'
	AND wp_postmeta.post_id = wp_posts.ID
	AND meta_key IN ('wpt_test_page_show_progress_meter' , 'wpt_result_page_show_scales_diagram', 'wpt_result_page_show_test_description')
;
