UPDATE wp_posts SET post_content = CONCAT(post_content, "\n[wptlist]")
WHERE id = 1 AND post_content NOT LIKE "%[wptlist]%";

SELECT * FROM wp_posts WHERE id = 1;