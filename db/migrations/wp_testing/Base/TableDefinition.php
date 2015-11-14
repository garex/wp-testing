<?php

class WpTesting_Migration_TableDefinition extends Ruckusing_Adapter_MySQL_TableDefinition
{

    /**
     * @var Ruckusing_Adapter_MySQL_Base
     */
    private $dbAdapter;
    private $tableName = '';
    private $alterTableDefinitions = array();
    private $pluginPrefix = '';

    public function __construct($adapter, $name, $options = array())
    {
        parent::__construct($adapter, $name, $options);
        $this->dbAdapter    = $adapter;
        $this->tableName    = $name;
        $this->pluginPrefix = $options['pluginPrefix'];
    }

    public function addPrimaryKey($columnName, $options = array())
    {
        return $this->addKey($columnName, $options + array(
            'primary_key'    => true,
            'auto_increment' => true,
        ));
    }

    public function addForeignKey($columnName, $options = array())
    {
        $options += array(
            'referencedKey' => $columnName,
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE',
        );
        return $this
            ->addKey($columnName, $options)
            ->addConstraintForeignKey(
                $options['keyName'], $columnName,
                $options['referencedTable'], $options['referencedKey'],
                $options['onDelete'], $options['onUpdate'])
            ->addIndex($options['keyName'], array($columnName))
        ;
    }

    private function addKey($columnName, $options = array())
    {
        return $this->addColumnIntegerBig($columnName, $options + array(
            'unsigned' => true,
        ));
    }

    public function addNullableForeignKey($columnName, $options = array())
    {
        return $this->addForeignKey($columnName, $options + array(
            'null' => true,
        ));
    }

    public function addColumnIntegerTiny($columnName, $options = array())
    {
        return $this->column($columnName, 'tinyinteger', $options);
    }

    public function addColumnInteger($columnName, $options = array())
    {
        return $this->column($columnName, 'integer', $options);
    }

    public function addColumnIntegerBig($columnName, $options = array())
    {
        return $this->column($columnName, 'biginteger', $options);
    }

    public function addColumnBoolean($columnName, $options = array())
    {
        return $this->column($columnName, 'boolean', $options);
    }

    public function addColumnDateTime($columnName, $options = array())
    {
        return $this->column($columnName, 'datetime', $options);
    }

    public function addColumnString($columnName, $options = array())
    {
        return $this->column($columnName, 'string', $options);
    }

    public function addColumnText($columnName, $options = array())
    {
        return $this->column($columnName, 'text', $options);
    }

    private function addConstraintForeignKey($name, $key, $referencedTable, $referencedKey, $onDelete = 'CASCADE', $onUpdate = 'CASCADE')
    {
        $name = $this->pluginPrefix . $name;
        $this->alterTableDefinitions[] = "
            ADD CONSTRAINT $name
            FOREIGN KEY ($key)
            REFERENCES $referencedTable($referencedKey)
            ON DELETE $onDelete
            ON UPDATE $onUpdate
        ";

        return $this;
    }

    /**
     * @param string $name
     * @param array $columns
     * @param string $options
     * @return self
     */
    public function addIndex($name, $columns, $options = '')
    {
        $columns = implode(', ', $columns);
        $this->alterTableDefinitions[] = "
            ADD $options INDEX $name($columns)
        ";

        return $this;
    }

    /**
     * @param string $name
     * @param array $columns
     * @return self
     */
    public function addIndexUnique($name, $columns)
    {
        return $this->addIndex($name, $columns, 'UNIQUE');
    }

    /**
     * Create a column
     *
     * @param string $columnName the column name
     * @param string $type       the column type
     * @param array  $options
     *
     * @return self
     */
    public function column($columnName, $type, $options = array())
    {
        parent::column($columnName, $type, $options + array(
            'null' => false,
        ));
        return $this;
    }

    /**
     * Finish table definition and apply it
     *
     * @param boolean $wantsSql
     * @return self
     *
     * @throws Ruckusing_Exception
     */
    public function finish($wantsSql = false)
    {
        parent::finish($wantsSql);

        // Apply alter table
        if (!empty($this->alterTableDefinitions)) {
            $definitions = implode(",\n", $this->alterTableDefinitions);
            $this->dbAdapter->execute("ALTER TABLE {$this->tableName} $definitions");
        }

        return $this;
    }
}
