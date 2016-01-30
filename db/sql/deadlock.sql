SET @@innodb_lock_wait_timeout = 1;
BEGIN;
SET @id := (SELECT passing_id FROM wp_t_passings ORDER BY RAND() LIMIT 1);
DELETE FROM wp_t_passing_answers WHERE passing_id = @id;
INSERT INTO wp_t_passing_answers (passing_id, answer_id) VALUES (@id, 832);
COMMIT;
