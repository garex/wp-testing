<?php

abstract class WpTesting_Migration_MigrateColumn extends WpTesting_Migration_Base
{

    /**
     * Rename a column
     *
     * @param string $tableName       the name of the table
     * @param string $columnName    the column name
     * @param string $newColumnName the new column name
     *
     * @return boolean
     */
    protected function renameColumn($tableName, $columnName, $newColumnName)
    {
        return $this->adaptee->rename_column($tableName, $columnName, $newColumnName);
    }

    /**
     * Add a column
     *
     * @param string $tableName the name of the table
     * @param string $columnName the column name
     * @param string $type the column type
     * @param array|string $options
     *
     * @return boolean
     */
    protected function addColumn($tableName, $columnName, $type, $options = array())
    {
        return $this->adaptee->add_column($tableName, $columnName, $type, $options);
    }

    /**
     * Remove a column
     *
     * @param string $tableName  the name of the table
     * @param string $columnName the column name
     *
     * @return boolean
     */
    protected function removeColumn($tableName, $columnName)
    {
        return $this->adaptee->remove_column($tableName, $columnName);
    }

    /**
     * Change a column
     *
     * @param string $tableName the name of the table
     * @param string $columnName the column name
     * @param string $type the column type
     * @param array|string $options
     *
     * @return boolean
     */
    protected function changeColumn($tableName, $columnName, $type, $options = array())
    {
        return $this->adaptee->change_column($tableName, $columnName, $type, $options);
    }

    /**
     * Add an index
     *
     * @param string $tableName the name of the table
     * @param array|string $columnName the column name(-s)
     * @param array|string $options [name, unique]
     *
     * @return boolean
     */
    protected function addIndex($tableName, $columnName, $options = array())
    {
        $tableName = $this->pluginPrefix . $tableName;
        return $this->adaptee->add_index($tableName, $columnName, $options);
    }

    /**
     * Remove an index
     *
     * @param string $tableName the name of the table
     * @param array|string $columnName the column name(-s)
     * @param array|string $options [name]
     *
     * @return boolean
     */
    protected function removeIndex($tableName, $columnName, $options = array())
    {
        $tableName = $this->pluginPrefix . $tableName;
        return $this->adaptee->remove_index($tableName, $columnName, $options);
    }
}
