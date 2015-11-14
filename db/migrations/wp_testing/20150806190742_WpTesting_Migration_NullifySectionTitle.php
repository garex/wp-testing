<?php

class WpTesting_Migration_NullifySectionTitle extends WpTesting_Migration_NullifyColumn
{

    protected $tableName  = 'sections';
    protected $columnName = 'section_title';
    protected $columnType = 'text';
}
