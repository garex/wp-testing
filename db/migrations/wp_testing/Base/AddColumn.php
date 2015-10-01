<?php

abstract class WpTesting_Migration_AddColumn extends WpTesting_Migration_MigrateColumn
{

    /**
     * [[table => .., column => .., type => .., options => ..]]
     * @var array
     */
    protected $columns = array();

    public function up()
    {
        foreach ($this->columns as $column) {
            $column += array('options' => array());
            $this->addColumn($this->pluginPrefix . $column['table'], $column['column'], $column['type'], $column['options']);
        }
    }

    public function down()
    {
        foreach ($this->columns as $column) {
            $this->removeColumn($this->pluginPrefix . $column['table'], $column['column']);
        }
    }
}
