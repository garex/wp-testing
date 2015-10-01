<?php

class WpTesting_Migration_AddPassingsTables extends WpTesting_Migration_MigrateTable
{
    public function up()
    {
        $this->createTable('passings')
            ->addPrimaryKey('passing_id')
            ->addForeignKey('test_id', array(
                'keyName'         => 'fk_passing_test',
                'referencedTable' => "{$this->globalPrefix}posts",
                'referencedKey'   => 'ID',
            ))
            ->addNullableForeignKey('respondent_id', array(
                'keyName'         => 'fk_passing_respondent',
                'referencedTable' => "{$this->globalPrefix}users",
                'referencedKey'   => 'ID',
            ))
            ->addColumnDateTime('created')
            ->addColumnDateTime('modified')
            ->finish();

        $this->createTable('passing_answers')
            ->addForeignKey('answer_id', array(
                'primary_key'     => true,
                'keyName'         => 'fk_passing_answer_answer',
                'referencedTable' => "{$this->globalPrefix}terms",
                'referencedKey'   => 'term_id',
            ))
            ->addForeignKey('question_id', array(
                'primary_key'     => true,
                'keyName'         => 'fk_passing_answer_question',
                'referencedTable' => "{$this->pluginPrefix}questions",
            ))
            ->addForeignKey('passing_id', array(
                'primary_key'     => true,
                'keyName'         => 'fk_passing_answer_passing',
                'referencedTable' => "{$this->pluginPrefix}passings",
            ))
            ->finish();
    }

    public function down()
    {
        $this->dropTable('passing_answers');
        $this->dropTable('passings');
    }
}
