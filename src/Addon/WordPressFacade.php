<?php
class WpTesting_Addon_WordPressFacade extends WpTesting_WordPressFacade
{

    public function addAction($tag, $function, $priority = self::PRIORITY_DEFAULT, $functionArgsCount = 1)
    {
        return $this;
    }

    public function addFilter($tag, $function, $priority = self::PRIORITY_DEFAULT, $functionArgsCount = 1)
    {
        return $this;
    }
}
