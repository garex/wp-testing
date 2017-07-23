<?php

class WpTesting_Component_Formatter_LineList extends WpTesting_Component_Formatter_Base
{
    public function __construct($value)
    {
        parent::__construct(implode(', ', $value));
    }
}
