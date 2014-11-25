<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class SwitchToIndividualAnswers extends BaseMigration
{
    public function up()
    {
        // create wp_t_answers
        $table = $this->create_table(WPT_DB_PREFIX . 'answers', array(
            'id'      => false,
            'options' => 'ENGINE=' . $this->get_wp_table_engine(),
        ));
        $table->column('answer_id', 'biginteger', array(
            'unsigned'       => true,
            'null'           => false,
            'primary_key'    => true,
            'auto_increment' => true,
        ));
        $table->column('question_id', 'biginteger', array(
            'unsigned' => true,
            'null'     => false,
        ));
        $table->column('global_answer_id', 'biginteger', array(
            'unsigned' => true,
        ));
        $table->column('answer_title', 'text', array(
            'null'    => true,
            'default' => '',
        ));
        $table->finish();

        $global_prefix = WP_DB_PREFIX;
        $plugin_prefix = WPT_DB_PREFIX;
        $this->execute("
            ALTER TABLE {$plugin_prefix}answers

            ADD CONSTRAINT fk_answer_question
            FOREIGN KEY (question_id)
            REFERENCES {$plugin_prefix}questions (question_id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD INDEX fk_answer_question (question_id),

            ADD CONSTRAINT fk_answer_global_answer
            FOREIGN KEY (global_answer_id)
            REFERENCES {$global_prefix}terms (term_id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD INDEX fk_answer_global_answer (global_answer_id)
        ");

        // fill-up wp_t_answers from tests and global answers
        $this->execute("
            INSERT INTO {$plugin_prefix}answers
            SELECT DISTINCT
                NULL           AS answer_id,
                q.question_id  AS question_id,
                tt.term_id     AS global_answer_id,
                ''             AS answer_title
            FROM {$global_prefix}term_taxonomy      AS tt
            JOIN {$global_prefix}term_relationships AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                                                   AND tt.taxonomy = 'wpt_answer'
            JOIN {$global_prefix}posts              AS t ON tr.object_id = t.id
            JOIN {$plugin_prefix}questions          AS q ON q.test_id = t.id
            ORDER BY q.question_id, tt.term_id
        ");

        // backup wp_t_scores as a future table structure
        $this->execute("
            DROP TABLE IF EXISTS {$plugin_prefix}scores_backup;
            CREATE TABLE {$plugin_prefix}scores_backup AS
            SELECT
                a.answer_id    AS answer_id,
                scale_id       AS scale_id,
                score_value    AS score_value
            FROM
                {$plugin_prefix}scores  AS s,
                {$plugin_prefix}answers AS a
            WHERE TRUE
                AND s.answer_id   = a.global_answer_id
                AND s.question_id = a.question_id
            ;
        ");

        // truncate scores
        $this->execute("TRUNCATE TABLE {$plugin_prefix}scores");

        // switch both scores and passing answers to wp_t_answers
        $this->execute("
            ALTER TABLE {$plugin_prefix}passing_answers
                DROP FOREIGN KEY fk_passing_answer_question,
                DROP FOREIGN KEY fk_passing_answer_answer
            ;
            ALTER TABLE {$plugin_prefix}passing_answers
                DROP COLUMN question_id,
                DROP PRIMARY KEY,
                ADD PRIMARY KEY (answer_id, passing_id),
                DROP INDEX fk_passing_answer_question,
                DROP INDEX fk_passing_answer_answer
            ;
            ALTER TABLE {$plugin_prefix}passing_answers
            ADD CONSTRAINT fk_passing_answer_answer
            FOREIGN KEY (answer_id)
            REFERENCES {$plugin_prefix}answers (answer_id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD INDEX fk_passing_answer_answer (answer_id)
        ");
        $this->execute("
            ALTER TABLE {$plugin_prefix}scores
                DROP FOREIGN KEY fk_score_question,
                DROP FOREIGN KEY fk_score_answer
            ;
            ALTER TABLE {$plugin_prefix}scores
                DROP COLUMN question_id,
                DROP PRIMARY KEY,
                ADD PRIMARY KEY (answer_id, scale_id),
                DROP INDEX fk_score_question,
                DROP INDEX fk_score_answer
            ;
            ALTER TABLE {$plugin_prefix}scores
                ADD CONSTRAINT fk_score_answer
                FOREIGN KEY (answer_id)
                REFERENCES {$plugin_prefix}answers (answer_id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
                ADD INDEX fk_score_answer (answer_id)
        ");

        // fill-up wp_t_scores from backup
        $this->execute("
            INSERT INTO {$plugin_prefix}scores
            SELECT * FROM {$plugin_prefix}scores_backup;
            DROP TABLE {$plugin_prefix}scores_backup;
        ");
    }

    public function down()
    {
        $global_prefix   = WP_DB_PREFIX;
        $plugin_prefix   = WPT_DB_PREFIX;
        $questionOptions = array(
            'unsigned'   => true,
            'null'       => false,
            'after'      => 'answer_id'
        );

        // backup wp_t_scores (for global answers) as an old table structure
        $this->execute("
            DROP TABLE IF EXISTS {$plugin_prefix}scores_backup;
            CREATE TABLE {$plugin_prefix}scores_backup AS
            SELECT
                a.global_answer_id AS answer_id,
                a.question_id,
                s.scale_id,
                s.score_value
            FROM
                {$plugin_prefix}scores AS s
                    JOIN
                {$plugin_prefix}answers AS a ON s.answer_id = a.answer_id
                    AND a.global_answer_id IS NOT NULL
        ");

        // backup wp_t_passing_answers (for global answers) as an old table structure
        $this->execute("
            DROP TABLE IF EXISTS {$plugin_prefix}passing_answers_backup;
            CREATE TABLE {$plugin_prefix}passing_answers_backup AS
            SELECT
                a.global_answer_id AS answer_id,
                a.question_id,
                pa.passing_id
            FROM
                {$plugin_prefix}passing_answers AS pa
            JOIN
                {$plugin_prefix}answers AS a ON pa.answer_id = a.answer_id
            AND a.global_answer_id IS NOT NULL
        ");

        // truncate scores and passing_answers
        $this->execute("TRUNCATE TABLE {$plugin_prefix}scores");
        $this->execute("TRUNCATE TABLE {$plugin_prefix}passing_answers");

        // switch both scores and passing answers to global answers
        $this->execute("ALTER TABLE {$plugin_prefix}scores DROP FOREIGN KEY fk_score_answer");
        $this->execute("ALTER TABLE {$plugin_prefix}scores DROP INDEX fk_score_answer");
        $this->add_column("{$plugin_prefix}scores", 'question_id', 'biginteger', $questionOptions);
        $this->execute("
            ALTER TABLE {$plugin_prefix}scores

            DROP PRIMARY KEY,
            ADD PRIMARY KEY(answer_id, question_id, scale_id),

            ADD CONSTRAINT fk_score_answer
                FOREIGN KEY (answer_id)
                REFERENCES {$global_prefix}terms (term_id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            ADD INDEX fk_score_answer (answer_id),

            ADD CONSTRAINT fk_score_question
                FOREIGN KEY (question_id)
                REFERENCES {$plugin_prefix}questions (question_id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            ADD INDEX fk_score_question (question_id)
        ");

        $this->execute("ALTER TABLE {$plugin_prefix}passing_answers DROP FOREIGN KEY fk_passing_answer_answer");
        $this->execute("ALTER TABLE {$plugin_prefix}passing_answers DROP INDEX fk_passing_answer_answer");
        $this->add_column("{$plugin_prefix}passing_answers", 'question_id', 'biginteger', $questionOptions);
        $this->execute("
            ALTER TABLE {$plugin_prefix}passing_answers

            DROP PRIMARY KEY,
            ADD PRIMARY KEY(answer_id, question_id, passing_id),

            ADD CONSTRAINT fk_passing_answer_answer
                FOREIGN KEY (answer_id)
                REFERENCES {$global_prefix}terms (term_id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            ADD INDEX fk_passing_answer_answer (answer_id),

            ADD CONSTRAINT fk_passing_answer_question
                FOREIGN KEY (question_id)
                REFERENCES {$plugin_prefix}questions (question_id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            ADD INDEX fk_passing_answer_question (question_id)
        ");

        // drop wp_t_answers
        $this->execute("DROP TABLE {$plugin_prefix}answers");

        // fill-up wp_t_scores/wp_t_passing_answers from backups
        $this->execute("
            INSERT INTO {$plugin_prefix}scores
            SELECT * FROM {$plugin_prefix}scores_backup;
            DROP TABLE {$plugin_prefix}scores_backup;
        ");
        $this->execute("
            INSERT INTO {$plugin_prefix}passing_answers
            SELECT * FROM {$plugin_prefix}passing_answers_backup;
            DROP TABLE {$plugin_prefix}passing_answers_backup;
        ");
    }
}
