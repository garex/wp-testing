<?php

class ScaleValueTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var fDatabase
     */
    private $db;

    protected function setUp()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->db = fORMDatabase::retrieve('WpTesting_Model_Test', 'write');
        $this->db->translatedExecute('BEGIN');
    }

    protected function tearDown()
    {
        $this->db->translatedExecute('ROLLBACK');
    }

    public function testNoVariablesFromEmptyTest()
    {
        $test = $this->createTest();
        $variables = WpTesting_Model_FormulaVariable_ScaleValue::buildAllFrom($test);
        $this->assertEmpty($variables);
    }

    public function testNoVariablesFromEmptyTestAndPassing()
    {
        $test    = $this->createTest()->store();
        $passing = new WpTesting_Model_Passing();
        $passing->setWp($this->getWpFacade())->setTestId($test->getId());

        $variables = WpTesting_Model_FormulaVariable_ScaleValue::buildAllFrom($test, $passing);
        $this->assertEmpty($variables);
    }

    public function testVariablesCreatedFromTestWithScales()
    {
        $scale1 = new WpTesting_Model_Scale();
        $scale1->setRange(1, 100)->setValue(50)->setSlug('slug1')->setTitle('title1');

        $scale2 = new WpTesting_Model_Scale();
        $scale2->setRange(1, 100)->setValue(10)->setSlug('slug2')->setTitle('title2');

        $testStub = $this->getMockBuilder('WpTesting_Model_Test')
            ->setMethods(array('buildScalesWithRange', '__wakeup'))
            ->disableOriginalConstructor()
            ->getMock();

        $testStub->expects($this->any())
            ->method('buildScalesWithRange')
            ->will($this->returnValue(array($scale1, $scale2)));

        /* @var $testStub WpTesting_Model_Test */
        $variables = WpTesting_Model_FormulaVariable_ScaleValue::buildAllFrom($testStub);
        $this->assertNotEmpty($variables);

        return $variables;
    }

    public function testVariablesCreatedFromTestAndPassingWithScales()
    {
        $scale1 = new WpTesting_Model_Scale();
        $scale1->setRange(0, 100)->setId(1)->setValue(10)->setSlug('slug1')->setTitle('title1');

        $scale2 = new WpTesting_Model_Scale();
        $scale2->setRange(0, 100)->setId(2)->setValue(20)->setSlug('slug2')->setTitle('title2');

        fORM::mapClassToTable('WpTesting_Model_Test_' . md5(__METHOD__), fORM::tablize('WpTesting_Model_Test'));
        $testStub = $this->getMockBuilder('WpTesting_Model_Test')
            ->setMethods(array('buildScalesWithRange', '__wakeup'))
            ->setMockClassName('WpTesting_Model_Test_' . md5(__METHOD__))
            ->getMock();

        fORM::mapClassToTable('WpTesting_Model_Passing_' . md5(__METHOD__), fORM::tablize('WpTesting_Model_Passing'));
        $passingStub = $this->getMockBuilder('WpTesting_Model_Passing')
            ->setMethods(array('createTest', 'buildAnswersScores', '__wakeup'))
            ->setMockClassName('WpTesting_Model_Passing_' . md5(__METHOD__))
            ->getMock();

        $testStub->expects($this->any())
            ->method('buildScalesWithRange')
            ->will($this->returnValue(array($scale1, $scale2)));

        $score = new WpTesting_Model_Score();
        $score->setValue(5)->setScaleId($scale1->getId());
        $passingStub->expects($this->any())
            ->method('createTest')
            ->will($this->returnValue($testStub));
        $passingStub->expects($this->any())
            ->method('buildAnswersScores')
            ->will($this->returnValue(array($score, $score)));

        /* @var $testStub WpTesting_Model_Test */
        /* @var $passingStub WpTesting_Model_Passing */

        $variables = WpTesting_Model_FormulaVariable_ScaleValue::buildAllFrom($testStub, $passingStub);
        $this->assertNotEmpty($variables);

        return $variables;
    }

    /**
     * @depends testVariablesCreatedFromTestAndPassingWithScales
     * @param array $variables
     */
    public function testVariablesFromPassingSameCountAndHasKey($variables)
    {
        $this->testVariablesFromTestCountSameAsScalesCount($variables);
        $this->testVariablesFromTestKeysAreSameAsSourceFromSlug($variables);
        return $variables['slug1'];
    }

    /**
     * @depends testVariablesFromPassingSameCountAndHasKey
     * @param WpTesting_Model_FormulaVariable $variable
     */
    public function testVariableFromPassingValueIsSameAsInScale(WpTesting_Model_FormulaVariable $variable)
    {
        $this->assertEquals(10, $variable->getValue());
    }

    /**
     * @depends testVariablesCreatedFromTestWithScales
     * @param array $variables
     */
    public function testVariablesFromTestCountSameAsScalesCount($variables)
    {
        $this->assertCount(2, $variables);
    }

    /**
     * @depends testVariablesCreatedFromTestWithScales
     * @param WpTesting_Model_FormulaVariable $variable
     */
    public function testVariablesFromTestKeysAreSameAsSourceFromSlug($variables)
    {
        $this->assertArrayHasKey('slug1', $variables);
        return $variables['slug1'];
    }

    /**
     * @depends testVariablesFromTestKeysAreSameAsSourceFromSlug
     * @param WpTesting_Model_FormulaVariable $variable
     */
    public function testVariableFromTestValueIsSameAsInScale(WpTesting_Model_FormulaVariable $variable)
    {
        $this->assertEquals(50, $variable->getValue());
    }

    /**
     * @depends testVariablesFromTestKeysAreSameAsSourceFromSlug
     * @param WpTesting_Model_FormulaVariable $variable
     */
    public function testVariableFromTestRatioValueCalculatedFromScaleRange(WpTesting_Model_FormulaVariable $variable)
    {
        $this->assertEquals(0.5, $variable->getValueAsRatio());
    }

    private function createTest()
    {
        $test = new WpTesting_Model_Test();
        return $test
            ->setWp($this->getWpFacade())
            ->setTitle('Test ' . date(DateTime::ATOM))
            ->setContent('Content')
            ->setExcerpt('Excerpt')
            ->setContentFiltered('Content')
            ->setToPing('http://localhost/')
            ->setPinged('http://localhost/')
            ->setType('wpt_test')
            ->setName('test-' . time());
    }

    /**
     * @return WpTesting_WordPressFacade
     */
    private function getWpFacade()
    {
        return $GLOBALS['wp_facade_mock'];
    }
}
