<?php

class CreateTestEntities extends Ruckusing_Migration_Base
{
    /**
     * @var Ruckusing_Adapter_MySQL_TableDefinition
     */
    protected $currentTable = null;

    protected $currentTableName = null;

    protected $queriesBuffer = array();

    public function up()
    {
        $this->drop_table(WPT_DB_PREFIX . 'tests');

        $this
            ->createEntityTable('scale')
                ->finishTable()
            ->createEntityTable('test')
                ->finishTable()
            ->createManyToManyTable('scale', 'test')
                ->finishTable()
            ->createEntityTable('parameter')
                ->addForeignKeyTo('scale')
                ->finishTable()
            ->createEntityTable('question')
                ->addForeignKeyTo('test')
                ->finishTable()
            ->createEntityTable('answer')
                ->addForeignKeyTo('question')
                ->finishTable()
            ->createManyToManyTable('answer', 'parameter')
                ->finishTable()
        ;

        $this->executeBuffered();
    }

    public function down()
    {
        $this->drop_table(WPT_DB_PREFIX . 'answer_parameter');
        $this->drop_table(WPT_DB_PREFIX . 'answer');
        $this->drop_table(WPT_DB_PREFIX . 'question');
        $this->drop_table(WPT_DB_PREFIX . 'scale_test');
        $this->drop_table(WPT_DB_PREFIX . 'test');
        $this->drop_table(WPT_DB_PREFIX . 'parameter');
        $this->drop_table(WPT_DB_PREFIX . 'scale');
    }

    protected function createTable($name, array $options = array())
    {
        $this->currentTableName = $name;
        $this->currentTable     = $this->create_table(WPT_DB_PREFIX . $this->currentTableName, $options);
        return $this;
    }

    protected function createEntityTable($name)
    {
        $this->createTable($name);
        $notNull = array('null' => false);
        $this->currentTable->column('title',    'string',    $notNull);
        $this->currentTable->column('created',  'datetime',  $notNull);
        $this->currentTable->column('modified', 'datetime',  $notNull);
        return $this;
    }

    protected function createManyToManyTable($from, $to)
    {
        $pk = array('primary_key' => true);
        return $this
            ->createTable($from . '_' . $to, array('id' => false))
            ->addForeignKeyTo($from, $pk)->addForeignKeyTo($to, $pk);
    }

    protected function addForeignKeyTo($name, $options = array())
    {
        $this->currentTable->column($name . '_id', 'integer', array(
            'unsigned' => true,
            'null'     => false,
        ) + $options);
        $prefix = WPT_DB_PREFIX;
        $this->buffer("
            ALTER TABLE {$prefix}{$this->currentTableName}
            ADD CONSTRAINT fk_{$this->currentTableName}_{$name}
            FOREIGN KEY ({$name}_id)
            REFERENCES {$prefix}{$name} (id)
            ON DELETE RESTRICT
            ON UPDATE RESTRICT,
            ADD INDEX fk_{$this->currentTableName}_{$name} ({$name}_id)
        ");
        return $this;
    }

    protected function finishTable()
    {
        $this->currentTable->finish();
        return $this;
    }

    protected function buffer($query)
    {
        $this->queriesBuffer[] = $query;
    }

    protected function executeBuffered()
    {
        foreach ($this->queriesBuffer as $query) {
            $this->execute($query);
        }

        $this->queriesBuffer = array();
    }
}
