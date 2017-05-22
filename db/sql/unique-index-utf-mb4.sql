set global innodb_large_prefix = 0;

drop table wp_t_computed_variables2;

CREATE TABLE `wp_t_computed_variables2` (
  `computed_variable_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_id` bigint(20) unsigned NOT NULL,
  `computed_variable_name` varchar(190) NOT NULL,
  `computed_variable_source` text NOT NULL,
  `computed_variable_sort` int(11) NOT NULL DEFAULT '100',
  PRIMARY KEY (`computed_variable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

ALTER TABLE wp_t_computed_variables2
	ADD CONSTRAINT wp_t_fk_computed_variable2_test
    FOREIGN KEY (test_id) REFERENCES wp_posts(ID) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD INDEX fk_computed_variable_test2(test_id),
    ADD UNIQUE INDEX uq_computed_variable_name_test2(test_id, computed_variable_name)
;

select length('some_long_variable_name_some_long_variable_name_some_long_variable_name');