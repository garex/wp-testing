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
        $tableName = $this->pluginPrefix . $tableName;
        $options  += array(
            'id'           => false,
            'options'      => $this->getTableEngineOption(),
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
     * Get default wordpress tables engine
     * @return string
     */
    private function getTableEngineOption()
    {
        try {
            return 'ENGINE=' . $this->getWpTableEngine();
        } catch (Exception $e) {
            $this->adaptee->get_adapter()->logger->log('Engine option is unknown: ' . $e->getMessage());
        }
        return '';
    }

    /**
     * Get default wordpress tables engine
     *
     * @throws Ruckusing_Exception
     * @return string
     */
    private function getWpTableEngine()
    {
        $status = $this->adaptee->select_one("SHOW TABLE STATUS LIKE '{$this->globalPrefix}posts'");

        if (empty($status['Engine'])) {
            throw new Ruckusing_Exception(
                'Default WP table is missing or it has unknown engine',
                Ruckusing_Exception::INVALID_TABLE_DEFINITION
            );
        }

        return $status['Engine'];
    }
}
