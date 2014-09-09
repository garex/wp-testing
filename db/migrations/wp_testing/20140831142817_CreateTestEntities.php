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
            ->createEntityTable('scales')
                ->finishTable()
            ->createEntityTable('tests')
                ->finishTable()
            ->createManyToManyTable('scales', 'tests')
                ->finishTable()
            ->createEntityTable('parameters')
                ->addForeignKeyTo('scales')
                ->finishTable()
            ->createEntityTable('questions')
                ->addForeignKeyTo('tests')
                ->finishTable()
            ->createEntityTable('answers')
                ->addForeignKeyTo('questions')
                ->finishTable()
            ->createManyToManyTable('answers', 'parameters')
                ->finishTable()
        ;

        $this->executeBuffered();
    }

    public function down()
    {
        $this->drop_table(WPT_DB_PREFIX . 'answers_parameters');
        $this->drop_table(WPT_DB_PREFIX . 'answers');
        $this->drop_table(WPT_DB_PREFIX . 'questions');
        $this->drop_table(WPT_DB_PREFIX . 'scales_tests');
        $this->drop_table(WPT_DB_PREFIX . 'tests');
        $this->drop_table(WPT_DB_PREFIX . 'parameters');
        $this->drop_table(WPT_DB_PREFIX . 'scales');
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
        $singularName = preg_replace('/s$/', '', $name);
        $this->currentTable->column($singularName . '_id', 'integer', array(
            'unsigned' => true,
            'null'     => false,
        ) + $options);
        $prefix = WPT_DB_PREFIX;
        $this->buffer("
            ALTER TABLE {$prefix}{$this->currentTableName}
            ADD CONSTRAINT fk_{$this->currentTableName}_{$name}
            FOREIGN KEY ({$singularName}_id)
            REFERENCES {$prefix}{$name} (id)
            ON DELETE RESTRICT
            ON UPDATE RESTRICT,
            ADD INDEX fk_{$this->currentTableName}_{$name} ({$singularName}_id)
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
