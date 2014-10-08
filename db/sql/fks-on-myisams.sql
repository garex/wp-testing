truncate wp_t_schema_migrations;
SHOW TABLE STATUS LIKE 'wp_posts';

SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wp_posts';

-- SHOW TABLES;
ALTER TABLE wp_commentmeta ENGINE = MyISAM;
ALTER TABLE wp_comments ENGINE = MyISAM;
ALTER TABLE wp_links ENGINE = MyISAM;
ALTER TABLE wp_options ENGINE = MyISAM;
ALTER TABLE wp_postmeta ENGINE = MyISAM;
ALTER TABLE wp_posts ENGINE = MyISAM;
ALTER TABLE wp_term_relationships ENGINE = MyISAM;
ALTER TABLE wp_term_taxonomy ENGINE = MyISAM;
ALTER TABLE wp_terms ENGINE = MyISAM;
ALTER TABLE wp_usermeta ENGINE = MyISAM;
ALTER TABLE wp_users ENGINE = MyISAM;

SELECT 
    thread_id, command_type, argument
FROM
    mysql.general_log
WHERE
    argument LIKE '%questions%'
ORDER BY event_time;

CREATE TABLE `wp_t_questions` (
`question_id` bigint UNSIGNED auto_increment NOT NULL,
`test_id` bigint UNSIGNED NOT NULL,
`question_title` text NOT NULL,
 PRIMARY KEY (`question_id`))  DEFAULT CHARSET=utf8 ENGINE=MyISAM;

ALTER TABLE wp_t_questions
ADD CONSTRAINT fk_question_test
FOREIGN KEY (test_id)
REFERENCES wp_posts (ID)
ON DELETE CASCADE
ON UPDATE CASCADE,
ADD INDEX fk_question_test (test_id);