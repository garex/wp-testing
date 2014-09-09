<?php

class AddHelloWorldTest extends Ruckusing_Migration_Base
{
    public function up()
    {
        $this->execute('
            INSERT INTO ' . WPT_DB_PREFIX . 'scales (id, title, created, modified)
            VALUES (1, "Correct?", NOW(), NOW());

            INSERT INTO ' . WPT_DB_PREFIX . 'parameters (id, title, created, modified, scale_id)
            VALUES (1, "Yes", NOW(), NOW(), 1);
            INSERT INTO ' . WPT_DB_PREFIX . 'parameters (id, title, created, modified, scale_id)
            VALUES (2, "No",  NOW(), NOW(), 1);

            INSERT INTO ' . WPT_DB_PREFIX . 'tests (id, title, created, modified)
            VALUES (1, "Hello world!", NOW(), NOW());

            INSERT INTO ' . WPT_DB_PREFIX . 'scales_tests (scale_id, test_id)
            VALUES (1, 1);

            INSERT INTO ' . WPT_DB_PREFIX . 'questions (id, title, created, modified, test_id)
            VALUES (1, "2 + 2 = 5", NOW(), NOW(), 1);
            INSERT INTO ' . WPT_DB_PREFIX . 'questions (id, title, created, modified, test_id)
            VALUES (2, "Sun in the sky", NOW(), NOW(), 1);
            INSERT INTO ' . WPT_DB_PREFIX . 'questions (id, title, created, modified, test_id)
            VALUES (3, "The world is mine", NOW(), NOW(), 1);

            INSERT INTO ' . WPT_DB_PREFIX . 'answers (id, title, created, modified, question_id)
            VALUES (1, "Yes", NOW(), NOW(), 1);
            INSERT INTO ' . WPT_DB_PREFIX . 'answers (id, title, created, modified, question_id)
            VALUES (2, "No", NOW(), NOW(),  1);
            INSERT INTO ' . WPT_DB_PREFIX . 'answers (id, title, created, modified, question_id)
            VALUES (3, "Yes", NOW(), NOW(), 2);
            INSERT INTO ' . WPT_DB_PREFIX . 'answers (id, title, created, modified, question_id)
            VALUES (4, "No", NOW(), NOW(),  2);
            INSERT INTO ' . WPT_DB_PREFIX . 'answers (id, title, created, modified, question_id)
            VALUES (5, "Yes", NOW(), NOW(), 3);
            INSERT INTO ' . WPT_DB_PREFIX . 'answers (id, title, created, modified, question_id)
            VALUES (6, "No", NOW(), NOW(),  3);

            INSERT INTO ' . WPT_DB_PREFIX . 'answers_parameters (answer_id, parameter_id)
            VALUES (1, 2);
            INSERT INTO ' . WPT_DB_PREFIX . 'answers_parameters (answer_id, parameter_id)
            VALUES (2, 1);
            INSERT INTO ' . WPT_DB_PREFIX . 'answers_parameters (answer_id, parameter_id)
            VALUES (3, 1);
            INSERT INTO ' . WPT_DB_PREFIX . 'answers_parameters (answer_id, parameter_id)
            VALUES (4, 2);
            INSERT INTO ' . WPT_DB_PREFIX . 'answers_parameters (answer_id, parameter_id)
            VALUES (5, 1);
            INSERT INTO ' . WPT_DB_PREFIX . 'answers_parameters (answer_id, parameter_id)
            VALUES (6, 2);
        ');
    }

    public function down()
    {
        $this
            ->clearTable('answers_parameters')
            ->clearTable('answers')
            ->clearTable('questions')
            ->clearTable('tests')
            ->clearTable('parameters')
            ->clearTable('scales_tests')
            ->clearTable('scales')
        ;
    }

    protected function clearTable($name)
    {
        if ($this->get_adapter() instanceof Ruckusing_Adapter_MySQL_Base) {
            $this->execute('SET FOREIGN_KEY_CHECKS=0');
            $this->execute('TRUNCATE TABLE ' . WPT_DB_PREFIX . $name);
            $this->execute('SET FOREIGN_KEY_CHECKS=1');
        } else {
            $this->execute('DELETE FROM ' . WPT_DB_PREFIX . $name);
        }
        return $this;
    }

}
