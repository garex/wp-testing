<?php

abstract class WpTesting_Migration_UpdateData extends WpTesting_Migration_Base
{

    /**
     * Select first field value
     *
     * @param string $sql the query to run
     *
     * @return string
     */
    protected function field($sql)
    {
        $result = $this->adaptee->select_one($sql);
        if (empty($result)) {
            return null;
        }
        return reset($result);
    }

    /**
     * Select all query
     *
     * @param string $sql the query to run
     *
     * @return array
     */
    protected function selectAll($sql)
    {
        return $this->adaptee->select_all($sql);
    }

    /**
     * Quote a string
     *
     * @param string $str the string to quote
     *
     * @return string
     */
    protected function quoteString($str)
    {
        return $this->adaptee->quote_string($str);
    }
}
