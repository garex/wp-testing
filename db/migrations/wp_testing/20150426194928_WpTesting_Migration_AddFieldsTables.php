<?php

class WpTesting_Migration_AddFieldsTables extends WpTesting_Migration_MigrateTable
{

    public function up()
    {
        $nullable = array('null' => true);

        // Fields
        $this->createTable('fields')
            ->addPrimaryKey('field_id')
            ->addForeignKey('test_id', array(
                'keyName'         => 'fk_field_test',
                'referencedTable' => "{$this->globalPrefix}posts",
                'referencedKey'   => 'ID',
            ))
            ->addColumnText('field_title')
            ->addColumnString('field_type')
            ->addColumnBoolean('field_is_required', array('default' => 1))
            ->addColumnInteger('field_sort', array('default' => 100))
            ->addColumnText('field_clarification', $nullable)
            ->addColumnText('field_list_values',   $nullable)
            ->addColumnText('field_default_value', $nullable)
            ->finish();

        // Fields values
        $this->createTable('field_values')
            ->addPrimaryKey('field_value_id')
            ->addForeignKey('field_id', array(
                'keyName'         => 'fk_field_value_field',
                'referencedTable' => "{$this->pluginPrefix}fields",
                'onDelete'        => 'RESTRICT'
            ))
            ->addForeignKey('passing_id', array(
                'keyName'         => 'fk_field_value_passing',
                'referencedTable' => "{$this->pluginPrefix}passings"
            ))
            ->addColumnText('field_value', $nullable)
            ->finish();
    }

    public function down()
    {
        $this->execute("
            SET FOREIGN_KEY_CHECKS = 0;
            TRUNCATE TABLE {$this->pluginPrefix}field_values;
            TRUNCATE TABLE {$this->pluginPrefix}fields;
            SET FOREIGN_KEY_CHECKS = 1;
        ");
        $this->dropTable('field_values');
        $this->dropTable('fields');
    }
}
