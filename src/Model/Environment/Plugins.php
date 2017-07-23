<?php

class WpTesting_Model_Environment_Plugins extends WpTesting_Model_Environment_WordPress
{
    protected $label = 'Plugins';

    protected function calculate(WpTesting_WordPressFacade $wp)
    {
        $rows = $wp->getPlugins();

        return new WpTesting_Component_Formatter_ItemList(array_values($rows), '%Name$s: %Version$s');
    }
}
