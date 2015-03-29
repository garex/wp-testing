<?php

class WpTesting_Mock_Facade extends WpTesting_Facade
{

    public function __construct(WpTesting_WordPressFacade $wp)
    {
        parent::__construct($wp);
        $this->setupORM();
    }

}
