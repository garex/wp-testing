<?php

class WpTesting_Component_Formatter_ListFromArray extends WpTesting_Component_Formatter_Base
{
    public function __construct($rows)
    {
        if (!is_array($rows)) {
            return parent::__construct($rows);
        }

        foreach ($rows as $i => $row) {
            $label = '';
            if (is_string($i)) {
                $label = $i;
            }
            $rows[$i] = '<li>' . $label . new WpTesting_Component_Formatter_ListFromArray($row). '</li>';
        }

        parent::__construct(sprintf('<ul>%s</ul>', implode(PHP_EOL, $rows)));
    }
}
