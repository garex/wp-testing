<?php

class WpTesting_Model_Shortcode_TestReadMore extends WpTesting_Model_Shortcode
{

    const NAME = 'wpt_test_read_more';

    protected $testId;
    protected $testName;
    protected $cssClass;
    protected $startButtonCaption;

    public function getDataForTemplate(WpTesting_Facade_IORM $ormAware)
    {
        $ormAware->setupORM();
        try {
            $test = WpTesting_Query_Test::create()->findByIdOrName($this->testId, $this->testName);
        } catch (fNoRemainingException $e) {
            throw new UnexpectedValueException('Can not find test by id or name');
        }
        $content = $this->getWp()->getExtended($test->getContent());
        $cssClasses = array(
            'test-' . $test->getId(),
            'test-' . $test->getName(),
            $this->cssClass,
        );
        return array(
            'cssClasses'     => implode(' ', $cssClasses),
            'title'          => $test->getTitle(),
            'content'        => $content['main'],
            'url'            => $this->getWp()->getPermalink($test->toWpPost()),
            'buttonCaption'  => $this->startButtonCaption,
            'wp'             => $this->getWp(),
        );
    }

    /**
     * @return WpTesting_Model_Shortcode_Attribute[]
     */
    protected function initAttributes()
    {
        return array(
            new WpTesting_Model_Shortcode_Attribute('id',           'testId'),
            new WpTesting_Model_Shortcode_Attribute('name',         'testName'),
            new WpTesting_Model_Shortcode_Attribute('class',        'cssClass'),
            new WpTesting_Model_Shortcode_Attribute('start_title',  'startButtonCaption', __('Start Test', 'wp-testing')),
        );
    }
}
