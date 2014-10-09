select id from wp_posts order by id desc; -- 107
select * from wp_terms where name = 'Sanguine'; -- 119

INSERT INTO wp_t_formulas (test_id, result_id, formula_source) VALUES ('107', '119', 'scale-lie > 50%');
INSERT INTO wp_t_formulas (test_id, result_id, formula_source) VALUES ('107', '120', 'scale-lie < 50%');


truncate table wp_t_formulas;

SELECT * FROM wp_t_formulas;
