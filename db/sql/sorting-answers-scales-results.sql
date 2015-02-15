-- update wp_t_answers set answer_sort = 100;

SELECT * FROM wp_t_answers;

SELECT 
    p.post_title, tt.taxonomy, t.name, t.term_id, tr.term_order
FROM
    wp_term_relationships tr
        join
    wp_posts p ON tr.object_id = p.ID
        join
    wp_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
        join
    wp_terms t ON tt.term_id = t.term_id
order by tr.object_id , tt.taxonomy , tr.term_order
;

SELECT 
    wp_term_taxonomy . *
FROM
    wp_term_taxonomy
        LEFT JOIN
    wp_term_relationships ON wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
        LEFT JOIN
    wp_posts ON wp_term_relationships.object_id = wp_posts.id
WHERE
    wp_posts.id = 18
GROUP BY wp_term_taxonomy.term_taxonomy_id , wp_term_taxonomy.term_id , wp_term_taxonomy.taxonomy , wp_term_taxonomy.description , wp_term_taxonomy.parent , wp_term_taxonomy.count
ORDER BY wp_term_taxonomy.term_taxonomy_id ASC;