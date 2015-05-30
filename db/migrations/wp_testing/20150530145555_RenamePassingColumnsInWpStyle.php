<?php

class RenamePassingColumnsInWpStyle extends Ruckusing_Migration_Base
{
    public function up()
    {
        $this->rename_columns('', 'passing_');
    }

    public function down()
    {
        $this->rename_columns('passing_', '');
    }

    private function rename_columns($old_prefix, $new_prefix)
    {
        $columns = array(
            'created',
            'modified',
            'ip',
            'device_uuid',
            'user_agent',
        );

        foreach ($columns as $column) {
            $this->rename_column(WPT_DB_PREFIX . 'passings', $old_prefix . $column, $new_prefix . $column);
        }
    }
}
