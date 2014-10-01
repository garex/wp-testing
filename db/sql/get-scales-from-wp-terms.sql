SELECT * FROM wp_terms
WHERE term_id IN (
	SELECT term_id FROM wp_term_taxonomy WHERE taxonomy = 'wpt_scale'
)
;

SELECT * FROM wp_term_relationships where object_id = 40 order by term_order;

SELECT * FROM wp_testing.wp_term_taxonomy;
SELECT * FROM wp_testing.wp_term_relationships;

SELECT 
    *
FROM wp_terms
LEFT JOIN wp_term_relationships ON wp_terms.term_id = wp_term_relationships.term_taxonomy_id
-- LEFT JOIN wp_posts ON wp_term_relationships.object_id = wp_posts.id
WHERE wp_terms.term_id = 24
-- WHERE wp_posts.id = 40
-- GROUP BY wp_terms.term_id , wp_terms.name , wp_terms.slug , wp_terms.term_group
ORDER BY wp_terms.term_id ASC;


SELECT 
    wp_term_taxonomy . *
FROM
    wp_term_taxonomy
        LEFT JOIN
    wp_term_relationships ON wp_term_taxonomy.term_id = wp_term_relationships.term_taxonomy_id
        LEFT JOIN
    wp_posts ON wp_term_relationships.object_id = wp_posts.id
WHERE
    wp_posts.id = 40
-- GROUP BY wp_term_taxonomy.term_taxonomy_id , wp_term_taxonomy.term_id , wp_term_taxonomy.taxonomy , wp_term_taxonomy.description , wp_term_taxonomy.parent , wp_term_taxonomy.count
ORDER BY wp_term_taxonomy.term_taxonomy_id ASC;