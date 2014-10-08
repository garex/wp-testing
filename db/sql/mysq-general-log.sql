SET GLOBAL general_log = 0;
DROP TABLE IF EXISTS mysql.general_log;
CREATE TABLE mysql.general_log (
   event_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   user_host MEDIUMTEXT NOT NULL,
   thread_id BIGINT(21) UNSIGNED NOT NULL,
   server_id INT(10) UNSIGNED NOT NULL,
   command_type VARCHAR(64) NOT NULL,
   argument MEDIUMTEXT NOT NULL
) ENGINE=CSV DEFAULT CHARSET=UTF8 COMMENT='General log';

SET GLOBAL general_log = 1;
SET GLOBAL log_output = 'table';

SET GLOBAL general_log = 0;
