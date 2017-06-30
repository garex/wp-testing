<?php

class WpTesting_Model_Plugin
{
    private $baseName;
    private $wp;

    public function __construct(WpTesting_WordPressFacade $wp)
    {
        $this->baseName = $wp->getPluginBaseName();
        $this->wp = $wp;
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
}