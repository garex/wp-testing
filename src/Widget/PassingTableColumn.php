<?php

abstract class WpTesting_Widget_PassingTableColumn
{

    abstract public function key();

    public function placeAfter()
    {
        return null;
    }

    abstract public function title();

    /**
     * @param WpTesting_Model_Passing $item
     */
    abstract public function value($item);
}
