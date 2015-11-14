<?php

abstract class WpTesting_Migration_NullifyColumn extends WpTesting_Migration_MigrateColumn
{

    protected $tableName = '';
    protected $columnName = '';
    protected $columnType = '';
    protected $columnOptions = array();

    public function up()
    {
        $this->nullifyColumn(true);
    }

    public function down()
    {
        $this->nullifyColumn(false);
    }

    private function nullifyColumn($to)
    {
        $this->changeColumn($this->pluginPrefix . $this->tableName, $this->columnName, $this->columnType, array(
            'null' => $to
        ) + $this->columnOptions);
    }
}
