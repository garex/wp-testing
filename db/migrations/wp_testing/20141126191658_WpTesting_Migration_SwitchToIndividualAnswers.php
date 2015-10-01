<?php

class WpTesting_Migration_SwitchToIndividualAnswers extends WpTesting_Migration_MigrateTable
{

    public function up()
    {
        // create wp_t_answers
        $this->createTable('answers')
            ->addPrimaryKey('answer_id')
            ->addForeignKey('question_id', array(
                'keyName'         => 'fk_answer_question',
                'referencedTable' => "{$this->pluginPrefix}questions",
            ))
            ->addNullableForeignKey('global_answer_id', array(
                'keyName'         => 'fk_answer_global_answer',
                'referencedTable' => "{$this->globalPrefix}terms",
                'referencedKey'   => 'term_id',
            ))
            ->addColumnText('answer_title', array('null' => true))
            ->finish();

        // fill-up wp_t_answers from tests and global answers
        $this->execute("
            INSERT INTO {$this->pluginPrefix}answers
            SELECT DISTINCT
                NULL           AS answer_id,
                q.question_id  AS question_id,
                tt.term_id     AS global_answer_id,
                ''             AS answer_title
            FROM {$this->globalPrefix}term_taxonomy      AS tt
            JOIN {$this->globalPrefix}term_relationships AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                                                   AND tt.taxonomy = 'wpt_answer'
            JOIN {$this->globalPrefix}posts              AS t ON tr.object_id = t.id
            JOIN {$this->pluginPrefix}questions          AS q ON q.test_id = t.id
            ORDER BY q.question_id, tt.term_id
        ");

        // backup wp_t_scores as a future table structure
        $this->execute("
            DROP TABLE IF EXISTS {$this->pluginPrefix}scores_backup;
            CREATE TABLE {$this->pluginPrefix}scores_backup AS
            SELECT
                a.answer_id    AS answer_id,
                scale_id       AS scale_id,
                score_value    AS score_value
            FROM
                {$this->pluginPrefix}scores  AS s,
                {$this->pluginPrefix}answers AS a
            WHERE TRUE
                AND s.answer_id   = a.global_answer_id
                AND s.question_id = a.question_id
            ;
        ");

        // truncate scores
        $this->execute("TRUNCATE TABLE {$this->pluginPrefix}scores");

        // switch both scores and passing answers to wp_t_answers
        $this->execute("
            ALTER TABLE {$this->pluginPrefix}passing_answers
                DROP FOREIGN KEY {$this->pluginPrefix}fk_passing_answer_question,
                DROP FOREIGN KEY {$this->pluginPrefix}fk_passing_answer_answer
            ;
            ALTER TABLE {$this->pluginPrefix}passing_answers
                DROP COLUMN question_id,
                DROP PRIMARY KEY,
                ADD PRIMARY KEY (answer_id, passing_id),
                DROP INDEX fk_passing_answer_question,
                DROP INDEX fk_passing_answer_answer
            ;
            ALTER TABLE {$this->pluginPrefix}passing_answers
            ADD CONSTRAINT {$this->pluginPrefix}fk_passing_answer_answer
            FOREIGN KEY (answer_id)
            REFERENCES {$this->pluginPrefix}answers (answer_id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD INDEX fk_passing_answer_answer (answer_id)
        ");
        $this->execute("
            ALTER TABLE {$this->pluginPrefix}scores
                DROP FOREIGN KEY {$this->pluginPrefix}fk_score_question,
                DROP FOREIGN KEY {$this->pluginPrefix}fk_score_answer
            ;
            ALTER TABLE {$this->pluginPrefix}scores
                DROP COLUMN question_id,
                DROP PRIMARY KEY,
                ADD PRIMARY KEY (answer_id, scale_id),
                DROP INDEX fk_score_question,
                DROP INDEX fk_score_answer
            ;
            ALTER TABLE {$this->pluginPrefix}scores
                ADD CONSTRAINT {$this->pluginPrefix}fk_score_answer
                FOREIGN KEY (answer_id)
                REFERENCES {$this->pluginPrefix}answers (answer_id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
                ADD INDEX fk_score_answer (answer_id)
        ");

        // fill-up wp_t_scores from backup
        $this->execute("
            INSERT INTO {$this->pluginPrefix}scores
            SELECT * FROM {$this->pluginPrefix}scores_backup;
            DROP TABLE {$this->pluginPrefix}scores_backup;
        ");
    }

    public function down()
    {
        $questionOptions = array(
            'unsigned'   => true,
            'null'       => false,
            'after'      => 'answer_id'
        );

        // backup wp_t_scores (for global answers) as an old table structure
        $this->execute("
            DROP TABLE IF EXISTS {$this->pluginPrefix}scores_backup;
            CREATE TABLE {$this->pluginPrefix}scores_backup AS
            SELECT
                a.global_answer_id AS answer_id,
                a.question_id,
                s.scale_id,
                s.score_value
            FROM
                {$this->pluginPrefix}scores AS s
                    JOIN
                {$this->pluginPrefix}answers AS a ON s.answer_id = a.answer_id
                    AND a.global_answer_id IS NOT NULL
        ");

        // backup wp_t_passing_answers (for global answers) as an old table structure
        $this->execute("
            DROP TABLE IF EXISTS {$this->pluginPrefix}passing_answers_backup;
            CREATE TABLE {$this->pluginPrefix}passing_answers_backup AS
            SELECT
                a.global_answer_id AS answer_id,
                a.question_id,
                pa.passing_id
            FROM
                {$this->pluginPrefix}passing_answers AS pa
            JOIN
                {$this->pluginPrefix}answers AS a ON pa.answer_id = a.answer_id
            AND a.global_answer_id IS NOT NULL
        ");

        // truncate scores and passing_answers
        $this->execute("TRUNCATE TABLE {$this->pluginPrefix}scores");
        $this->execute("TRUNCATE TABLE {$this->pluginPrefix}passing_answers");

        // switch both scores and passing answers to global answers
        $this->execute("ALTER TABLE {$this->pluginPrefix}scores DROP FOREIGN KEY {$this->pluginPrefix}fk_score_answer");
        $this->execute("ALTER TABLE {$this->pluginPrefix}scores DROP INDEX fk_score_answer");
        $this->addColumn("{$this->pluginPrefix}scores", 'question_id', 'biginteger', $questionOptions);
        $this->execute("
            ALTER TABLE {$this->pluginPrefix}scores

            DROP PRIMARY KEY,
            ADD PRIMARY KEY(answer_id, question_id, scale_id),

            ADD CONSTRAINT {$this->pluginPrefix}fk_score_answer
                FOREIGN KEY (answer_id)
                REFERENCES {$this->globalPrefix}terms (term_id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            ADD INDEX fk_score_answer (answer_id),

            ADD CONSTRAINT {$this->pluginPrefix}fk_score_question
                FOREIGN KEY (question_id)
                REFERENCES {$this->pluginPrefix}questions (question_id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            ADD INDEX fk_score_question (question_id)
        ");

        $this->execute("ALTER TABLE {$this->pluginPrefix}passing_answers DROP FOREIGN KEY {$this->pluginPrefix}fk_passing_answer_answer");
        $this->execute("ALTER TABLE {$this->pluginPrefix}passing_answers DROP INDEX fk_passing_answer_answer");
        $this->addColumn("{$this->pluginPrefix}passing_answers", 'question_id', 'biginteger', $questionOptions);
        $this->execute("
            ALTER TABLE {$this->pluginPrefix}passing_answers

            DROP PRIMARY KEY,
            ADD PRIMARY KEY(answer_id, question_id, passing_id),

            ADD CONSTRAINT {$this->pluginPrefix}fk_passing_answer_answer
                FOREIGN KEY (answer_id)
                REFERENCES {$this->globalPrefix}terms (term_id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            ADD INDEX fk_passing_answer_answer (answer_id),

            ADD CONSTRAINT {$this->pluginPrefix}fk_passing_answer_question
                FOREIGN KEY (question_id)
                REFERENCES {$this->pluginPrefix}questions (question_id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            ADD INDEX fk_passing_answer_question (question_id)
        ");

        // drop wp_t_answers
        $this->execute("DROP TABLE {$this->pluginPrefix}answers");

        // fill-up wp_t_scores/wp_t_passing_answers from backups
        $this->execute("
            INSERT INTO {$this->pluginPrefix}scores
            SELECT * FROM {$this->pluginPrefix}scores_backup;
            DROP TABLE {$this->pluginPrefix}scores_backup;
        ");
        $this->execute("
            INSERT INTO {$this->pluginPrefix}passing_answers
            SELECT * FROM {$this->pluginPrefix}passing_answers_backup;
            DROP TABLE {$this->pluginPrefix}passing_answers_backup;
        ");
    }
}
