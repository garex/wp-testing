<?php

class WpTesting_Doer_ShortcodesRegistrator extends WpTesting_Doer_AbstractDoer
{

    /**
     * @var WpTesting_Facade_IORM
     */
    private $ormAware;

    /**
     * @var WpTesting_Facade_ITestPasser
     */
    private $testPasserAware;

    /**
     * Map of shortcode tag to it's rendering class
     * @var array
     */
    private $shortcodes;

    public function __construct(WpTesting_WordPressFacade $wp, WpTesting_Facade_IORM $ormAware, WpTesting_Facade_ITestPasser $testPasserAware)
    {
        parent::__construct($wp);
        $this->ormAware = $ormAware;
        $this->testPasserAware = $testPasserAware;

        $this->shortcodes = (array)$this->wp->applyFilters('wp_testing_shortcoder_shortcodes', array(
            'wpt_tests'             => 'WpTesting_Doer_Shortcoder_Tests',
            'wpt_test_read_more'    => 'WpTesting_Doer_Shortcoder_TestReadMore',
            'wpt_test_first_page'   => 'WpTesting_Doer_Shortcoder_TestFirstPage',

            // Backward compatibility
            'wptlist'               => 'WpTesting_Doer_Shortcoder_Tests',
        ));

        foreach (array_keys($this->shortcodes) as $tag) {
            $this->wp->addShortcode($tag, array($this, 'renderFactory'));
        }
    }

    public function renderFactory($attributes, $content, $tag)
    {
        if (!isset($this->shortcodes[$tag])) {
            return null;
        }
        if (empty($attributes)) {
            $attributes = array();
        }

        /* @var $shortcode WpTesting_Doer_Shortcoder */
        $shortcode = new $this->shortcodes[$tag]($this->wp, $this->ormAware, $this->testPasserAware);
        return $shortcode->renderShortcode($attributes, $content, $tag);
    }
}
