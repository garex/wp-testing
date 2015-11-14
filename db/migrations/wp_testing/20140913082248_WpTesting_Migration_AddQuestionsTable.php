<?php

class WpTesting_Migration_AddQuestionsTable extends WpTesting_Migration_AddSingleTable
{

    protected $entity = 'question';

    protected function setUpTable($table)
    {
        $this->addForeignKeyToTest($table);
        $table->addColumnText('question_title');
    }
}
