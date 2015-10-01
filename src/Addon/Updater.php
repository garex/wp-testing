<?php

class WpTesting_Addon_Updater
{

    private $metadataUrlFormat = null;

    public function __construct($updateRoot)
    {
        $serverName = implode('.', array_slice(explode('.', $_SERVER['SERVER_NAME']), -2));
        $this->metadataUrlFormat = rtrim($updateRoot, '/') . '/' . $serverName .  '/%s.json';
    }

    /**
     * @uses PucFactory
     * @return WpTesting_Addon_Updater
     */
    public function add(WpTesting_Addon_IAddon $addon)
    {
        $root           = dirname($addon->getRoot());
        $slug           = basename($root);
        $metadataUrl    = sprintf($this->metadataUrlFormat, $slug);
        $pluginFile     = $root . '/' . $slug . '.php';
        $checkEachHours = 1;

        /**
         * Uses external library named "Plugin Update Checker" for the purposes of updating paid addons that hosted at
         * http://apsiholog.ru/addons/
         *
         * Updates happens only in admin area and only when addon registered.
         * So it's not touches in any ways usual plugin users.
         *
         * @since 2015-07-31
         * @author Ustimenko Alexander
         */
        PucFactory::buildUpdateChecker(
            $metadataUrl,
            $pluginFile,
            '',
            $checkEachHours
        );

        return $this;
    }

}
