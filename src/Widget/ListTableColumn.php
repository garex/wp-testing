<?php

abstract class WpTesting_Widget_ListTableColumn
{

    /**
     * @return string
     */
    abstract public function key();

    /**
     * @return string
     */
    public function placeAfter()
    {
        return null;
    }

    /**
     * @return string
     */
    abstract public function title();

    /**
     * @param WpTesting_Model_AbstractModel $item
     * @return string
     */
    abstract public function value($item);
}
