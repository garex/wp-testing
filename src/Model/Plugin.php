<?php

class WpTesting_Model_Plugin
{
    private $baseName;
    private $wp;
    private $ormAware;

    public function __construct(WpTesting_WordPressFacade $wp, WpTesting_Facade_IORM $ormAware)
    {
        $this->baseName = $wp->getPluginBaseName();
        $this->wp = $wp;
        $this->ormAware = $ormAware;
    }

    public function getBaseName()
    {
        return $this->baseName;
    }

    public function getPaidSupportUrl()
    {
        return 'https://docs.google.com/document/d/1eHQB69neQJ68xl3vT-x4cHERZTBskq2L0x47AjUPyKM/edit?usp=sharing';
    }

    public function getPaidAddonsUrl()
    {
        return 'https://docs.google.com/spreadsheets/d/1BrZv6gpIo0QV21p42oJ9KIO5jZzqugOUB1GqQOeQqEY/edit?usp=sharing';
    }

    public function getRateUsUrl()
    {
        return 'https://wordpress.org/support/plugin/wp-testing/reviews?rate=5#new-post';
    }

    public function isRateUsClicked()
    {
        return $this->wp->getOption('wpt_rateus_clicked');
    }

    public function getRateUsNonce()
    {
        if ($this->isRateUsClicked()) {
            return false;
        }

        return $this->wp->createNonce('feeback-rate-us');
    }

    public function checkRateUsNonce()
    {
        return $this->wp->checkAjaxReferer('feeback-rate-us');
    }

    public function markRateUsAsClicked()
    {
        return $this->wp->updateOption('wpt_rateus_clicked', true);
    }

    /**
     * @return WpTesting_Model_IEnvironment[]
     */
    public function getEnvironment()
    {
        $wp = $this->wp;
        $database = $this->ormAware->setupORM();

        return array(
            new WpTesting_Model_Environment_Browser(),
            new WpTesting_Model_Environment_PhpVersion(),
            new WpTesting_Model_Environment_PhpExtensions(),
            new WpTesting_Model_Environment_PhpIniValues(),
            new WpTesting_Model_Environment_MysqlClientVersion(),
            new WpTesting_Model_Environment_MysqlServerVersion($database),
            new WpTesting_Model_Environment_MysqlEngines($database),
            new WpTesting_Model_Environment_MysqlTableStatus($database),
            new WpTesting_Model_Environment_WordPressVersion($wp),
            new WpTesting_Model_Environment_Plugins($wp),
            new WpTesting_Model_Environment_ActiveTheme($wp),
        );
    }
}