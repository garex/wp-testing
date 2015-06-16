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
     * @return WpTesting_Addon_Updater
     */
    public function add(WpTesting_Addon_IAddon $addon)
    {
        $root           = dirname($addon->getRoot());
        $slug           = basename($root);
        $metadataUrl    = sprintf($this->metadataUrlFormat, $slug);
        $pluginFile     = $root . DIRECTORY_SEPARATOR . $slug . '.php';
        $checkEachHours = 1;

        $checker = PucFactory::buildUpdateChecker(
            $metadataUrl,
            $pluginFile,
            '',
            $checkEachHours
        );

        return $this;
    }

}
