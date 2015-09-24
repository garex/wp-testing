<?php

abstract class WpTesting_Model_Shortcode_Test extends WpTesting_Model_Shortcode
{

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
        if (!$test->isPublished()) {
            throw new LogicException(sprintf(__('Test "%s" is not published. You can not include it anywhere.', 'wp-testing'), $test->getTitle()));
        }
        $cssClasses = array(
            'test-' . $test->getId(),
            'test-' . $test->getName(),
            $this->cssClass,
        );
        return array(
            'cssClasses' => implode(' ', array_filter($cssClasses)),
            'title'      => $test->getTitle(),
            'test'       => $test,
        );
    }

    protected function getUniqueIdentifier()
    {
        $ids = array_filter(array($this->testId, $this->testName));
        return reset($ids);
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
