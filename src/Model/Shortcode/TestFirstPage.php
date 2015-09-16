<?php

class WpTesting_Model_Shortcode_TestFirstPage extends WpTesting_Model_Shortcode
{

    const NAME = 'wpt_test_first_page';

    protected $testId;
    protected $testName;
    protected $cssClass;

    public function getDataForTemplate(WpTesting_Facade_IORM $ormAware)
    {
        $ormAware->setupORM();
        try {
            $test = WpTesting_Query_Test::create()->findByIdOrName($this->testId, $this->testName);
        } catch (fNoRemainingException $e) {
            throw new UnexpectedValueException('Can not find test by id or name');
        }
        $cssClasses = array(
            'test-' . $test->getId(),
            'test-' . $test->getName(),
            $this->cssClass,
        );
        return array(
            'cssClasses' => implode(' ', $cssClasses),
            'title'      => $test->getTitle(),
            'content'    => null,
            'test'       => $test,
        );
    }

    /**
     * @return WpTesting_Model_Shortcode_Attribute[]
     */
    protected function initAttributes()
    {
        return array(
            new WpTesting_Model_Shortcode_Attribute('id',       'testId'),
            new WpTesting_Model_Shortcode_Attribute('name',     'testName'),
            new WpTesting_Model_Shortcode_Attribute('class',    'cssClass'),
        );
    }
}
