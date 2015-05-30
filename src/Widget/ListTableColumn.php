<?php

abstract class WpTesting_Widget_ListTableColumn
{

    abstract public function key();

    public function placeAfter()
    {
        return null;
    }

    abstract public function title();

    /**
     * @param WpTesting_Model_AbstractModel $item
     */
    abstract public function value($item);
}
