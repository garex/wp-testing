<?php

class WpTesting_Migration_AddComputedVariablesTable extends WpTesting_Migration_AddSingleTable
{

    protected $entity = 'computed_variable';

    protected function setUpTable($table)
    {
        $this->addForeignKeyToTest($table);
        $table
            ->addColumnString('computed_variable_name', array(
                'limit' => 190,
            ))
            ->addColumnText('computed_variable_source')
            ->addColumnInteger('computed_variable_sort', array(
                'default' => 100,
            ))
            ->addIndexUnique('uq_computed_variable_name_test', array(
                'test_id',
                'computed_variable_name'
            ));
    }
}
