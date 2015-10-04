<?php

class WpTesting_Migration_AddSectionsTable extends WpTesting_Migration_AddSingleTable
{

    protected $entity = 'section';

    protected function setUpTable($table)
    {
        $this->addForeignKeyToTest($table);
        $table
            ->addForeignKey('question_id', array(
                'keyName'         => 'fk_section_question',
                'referencedTable' => "{$this->pluginPrefix}questions",
                'referencedKey'   => 'question_id',
            ))
            ->addColumnText('section_title')
            ->addIndexUnique('uq_section_test_question', array(
                'test_id',
                'question_id'
            ));
    }
}
