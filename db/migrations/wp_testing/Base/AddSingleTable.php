<?php

abstract class WpTesting_Migration_AddSingleTable extends WpTesting_Migration_MigrateTable
{

    protected $entity = '';

    public function up()
    {
        $table = $this->createTable($this->entity . 's')->addPrimaryKey($this->entity . '_id');
        $this->setUpTable($table);
        $table->finish();
    }

    public function down()
    {
        $this->dropTable($this->entity . 's');
    }

    /**
     * @param WpTesting_Migration_TableDefinition $table
     * @return void
     */
    protected function addForeignKeyToTest($table)
    {
        $table->addForeignKey('test_id', array(
            'keyName'         => "fk_{$this->entity}_test",
            'referencedTable' => "{$this->globalPrefix}posts",
            'referencedKey'   => 'ID',
        ));
    }

    /**
     * @param WpTesting_Migration_TableDefinition $table
     * @return void
     */
    abstract protected function setUpTable($table);
}
