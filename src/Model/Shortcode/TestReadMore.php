<?php

class WpTesting_Model_Shortcode_TestReadMore extends WpTesting_Model_Shortcode_Test
{

    const NAME = 'wpt_test_read_more';

    protected $startButtonCaption;

    public function getDataForTemplate(WpTesting_Facade_IORM $ormAware)
    {
        $data    = parent::getDataForTemplate($ormAware);
        /* @var $test WpTesting_Model_Test */
        $test    = $data['test'];
        $content = $this->getWp()->getExtended($test->getContent());
        return array_merge($data, array(
            'content'        => $content['main'],
            'url'            => $this->getWp()->getPermalink($test->toWpPost()),
            'buttonCaption'  => $this->startButtonCaption,
            'wp'             => $this->getWp(),
        ));
    }

    /**
     * @return WpTesting_Model_Shortcode_Attribute[]
     */
    protected function initAttributes()
    {
        return array_merge(parent::initAttributes(), array(
            new WpTesting_Model_Shortcode_Attribute('start_title',  'startButtonCaption', __('Start Test', 'wp-testing')),
        ));
    }
}
