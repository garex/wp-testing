<?php

abstract class WpTesting_Migration_MigrateTable extends WpTesting_Migration_MigrateColumn
{

    /**
     * Create a table
     * @param string $tableName the name of the table
     * @param array|string $options
     * @return WpTesting_Migration_TableDefinition
     * @throws Ruckusing_Exception
     */
    public function createTable($tableName, $options = array())
    {
        $adapter   = $this->adaptee->get_adapter();
        if (!($adapter instanceof Ruckusing_Adapter_MySQL_Base)) {
            throw new Ruckusing_Exception('Only MySQL adapter allowed');
        }

        $tableStatus  = array();
        $tableOptions = '';
        try {
            $tableStatus  = $this->showTableStatus();
            $tableOptions = 'ENGINE=' . $tableStatus['default']['engine'];
        } catch (Exception $e) {
            $this->adaptee->get_adapter()->logger->log(__METHOD__ . ': ' . $e->getMessage());
        }

        $tableName   = $this->pluginPrefix . $tableName;
        $options    += array(
            'id'           => false,
            'tableStatus'  => $tableStatus,
            'options'      => $tableOptions,
            'pluginPrefix' => $this->pluginPrefix,
        );

        return new WpTesting_Migration_TableDefinition($adapter, $tableName, $options);
    }

    /**
     * Drop a table
     *
     * @param string $tableName the name of the table
     *
     * @return boolean
     */
    public function dropTable($tableName)
    {
        return $this->adaptee->drop_table($this->pluginPrefix . $tableName);
    }

    /**
     * Show all tables status as array by table name and columns as subarray
     *
     * @throws Ruckusing_Exception
     * @return array
     */
    private function showTableStatus()
    {
        $result = array();
        foreach ($this->adaptee->select_all("SHOW TABLE STATUS") as $upperCasedRow) {
            $row = array();
            foreach ($upperCasedRow as $upperCasedParam => $value) {
                $row[strtolower($upperCasedParam)] = $value;
            }
            $result[$row['name']] = $row;
        }

        $defaultTable = $this->blogPrefix . 'posts';
        if (empty($result[$defaultTable]['engine'])) {
            throw new Ruckusing_Exception(
                'Default WordPress table is missing or has unknown engine',
                Ruckusing_Exception::MIGRATION_FAILED
            );
        }
        $result['default'] = $result[$defaultTable];

        return $result;
    }
}
