<?php

class WpTesting_Migration_AddTestScoresTable extends WpTesting_Migration_MigrateTable
{
    public function up()
    {
        $this->createTable('scores')
            ->addForeignKey('answer_id', array(
                'primary_key'     => true,
                'keyName'         => 'fk_score_answer',
                'referencedTable' => "{$this->globalPrefix}terms",
                'referencedKey'   => 'term_id',
            ))
            ->addForeignKey('question_id', array(
                'primary_key'     => true,
                'keyName'         => 'fk_score_question',
                'referencedTable' => "{$this->pluginPrefix}questions",
            ))
            ->addForeignKey('scale_id', array(
                'primary_key'     => true,
                'keyName'         => 'fk_score_scale',
                'referencedTable' => "{$this->globalPrefix}terms",
                'referencedKey'   => 'term_id',
            ))
            ->addColumnIntegerTiny('score_value', array('default' => 0))
            ->finish();
    }

    public function down()
    {
        $this->dropTable('scores');
    }
}
