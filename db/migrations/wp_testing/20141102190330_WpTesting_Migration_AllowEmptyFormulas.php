<?php

class WpTesting_Migration_AllowEmptyFormulas extends WpTesting_Migration_NullifyColumn
{

    protected $tableName  = 'formulas';
    protected $columnName = 'formula_source';
    protected $columnType = 'text';
}
