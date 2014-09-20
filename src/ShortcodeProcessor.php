<?php

class WpTesting_ShortcodeProcessor extends WpTesting_Doer
{
    /**
     * @var WpTesting_WordPressFacade
     */
    private $wp = null;

    public function __construct(WpTesting_WordPressFacade $wp)
    {
        $this->wp = $wp;
    }

    public function getList()
    {
        $tests = WpTesting_Query_Test::create()->findAllPublished();
        return $this->render('Shortcode/list', array(
            'tests' => $tests,
            'wp'    => $this->wp,
        ));
    }
}
