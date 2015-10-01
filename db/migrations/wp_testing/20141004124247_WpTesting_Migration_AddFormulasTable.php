<?php

class WpTesting_Migration_AddFormulasTable extends WpTesting_Migration_AddSingleTable
{

    protected $entity = 'formula';

    protected function setUpTable($table)
    {
        $this->addForeignKeyToTest($table);
        $table
            ->addForeignKey('result_id', array(
                'keyName'         => 'fk_formula_result',
                'referencedTable' => "{$this->globalPrefix}terms",
                'referencedKey'   => 'term_id',
            ))
            ->addColumnText('formula_source')
            ->addIndexUnique('uq_formula_test_result', array(
                'test_id',
                'result_id'
            ));
    }
}
