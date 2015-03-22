<?php

interface WpTesting_Addon_IAddon extends WpTesting_Component_IRootable
{

    /**
     * @return WpTesting_Addon_IAddon
     */
    public function setWp(WpTesting_Addon_IWordPressFacade $wp);

}
