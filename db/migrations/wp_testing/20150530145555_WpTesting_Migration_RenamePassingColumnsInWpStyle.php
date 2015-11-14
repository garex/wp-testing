<?php

class WpTesting_Migration_RenamePassingColumnsInWpStyle extends WpTesting_Migration_MigrateColumn
{
    public function up()
    {
        $this->renameColumns('', 'passing_');
    }

    public function down()
    {
        $this->renameColumns('passing_', '');
    }

    private function renameColumns($oldPrefix, $newPrefix)
    {
        $columns = array(
            'created',
            'modified',
            'ip',
            'device_uuid',
            'user_agent',
        );

        foreach ($columns as $column) {
            $this->renameColumn($this->pluginPrefix . 'passings', $oldPrefix . $column, $newPrefix . $column);
        }
    }
}
