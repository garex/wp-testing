<?php

class WpTesting_Component_Formatter_ItemList extends WpTesting_Component_Formatter_Base
{
    public function __construct($rows, $itemFormat)
    {
        foreach ($rows as $i => $row) {
            $rows[$i] = new WpTesting_Component_Formatter_NamedArrayKeys($row, $itemFormat);
        }

        parent::__construct(new WpTesting_Component_Formatter_ListFromArray($rows));
    }
}
