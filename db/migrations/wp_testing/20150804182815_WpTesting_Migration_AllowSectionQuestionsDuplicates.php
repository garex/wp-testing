<?php

class WpTesting_Migration_AllowSectionQuestionsDuplicates extends WpTesting_Migration_MigrateColumn
{

    private $indexColumns = array('test_id', 'question_id');
    private $indexOptions = array('name' => 'uq_section_test_question', 'unique' => true);

    public function up()
    {
        $this->removeIndex('sections', $this->indexColumns, $this->indexOptions);
    }

    public function down()
    {
        $this->addIndex('sections', $this->indexColumns, $this->indexOptions);
    }
}
