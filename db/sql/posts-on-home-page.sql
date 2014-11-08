SELECT SQL_CALC_FOUND_ROWS
    wp_posts.post_title,
	wp_postmeta.*
FROM
    wp_posts
        INNER JOIN
    wp_postmeta ON (wp_posts.ID = wp_postmeta.post_id)
WHERE
    1 = 1
        AND wp_posts.post_type IN ('post' , 'wpt_test')
        AND (wp_posts.post_status = 'publish')
        AND ((wp_postmeta.meta_key = 'wpt_publish_on_home'
        AND CAST(wp_postmeta.meta_value AS CHAR) != '0'))
GROUP BY wp_posts.ID
ORDER BY wp_posts.post_date DESC
LIMIT 0 , 10;


DELETE FROM wp_postmeta WHERE meta_key = 'wpt_publish_on_home';

INSERT INTO wp_postmeta(post_id, meta_key, meta_value)
SELECT ID, 'wpt_publish_on_home', 1
FROM wp_posts WHERE post_type = 'wpt_test';

SELECT * FROM wp_postmeta WHERE meta_key = 'wpt_publish_on_home';
