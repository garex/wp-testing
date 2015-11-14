<?php

class WpTesting_Migration_NullifySectionQuestion extends WpTesting_Migration_NullifyColumn
{

    protected $tableName  = 'sections';
    protected $columnName = 'question_id';
    protected $columnType = 'biginteger';
    protected $columnOptions = array('unsigned' => true);
}
