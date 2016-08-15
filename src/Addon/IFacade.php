<?php

interface WpTesting_Addon_IFacade extends WpTesting_Facade_IORM
{

    /**
     * @param WpTesting_Addon_IAddon $addon
     * @return WpTesting_Addon_IFacade
     */
    public function registerAddon($addon);

}
