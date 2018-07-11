<?php

class ShortcodesRegistratorTest extends WpTesting_Tests_TestCase
{

    /**
     * @var WpTesting_Doer_ShortcodesRegistrator
     */
    private $doer;

    public function setUp()
    {
        $this->doer = new WpTesting_Doer_ShortcodesRegistrator($this->getWpFacade(), $this->getFacade(), $this->getFacade());
    }

    public function testTestsRendered()
    {
        $result = $this->doer->renderFactory('', null, 'wpt_tests');
        $this->assertContains('EPI', $result);
        $this->assertContains('decimal', $result);
    }

    public function testTestsAndItsBackwardAliasIdentical()
    {
        $old     = $this->doer->renderFactory('', null, 'wptlist');
        $current = $this->doer->renderFactory('', null, 'wpt_tests');
        $this->assertEquals($old, $current);
    }

    public function testEmptyAttributesAsStringOrArrayAreEqual()
    {
        $old     = $this->doer->renderFactory('',       null, 'wpt_tests');
        $current = $this->doer->renderFactory(array(),  null, 'wpt_tests');
        $this->assertEquals($old, $current);
    }

    public function testBadTestsAttributeGivesAnErrorWithGuide()
    {
        $result = $this->doer->renderFactory(array('list' => 'unknown'), null, 'wpt_tests');
        $this->assertContains('error-message', $result);
        $this->assertContains('UnexpectedValueException', $result);
        $this->assertContains('See <a href="http://www.w3.org/wiki/CSS/Properties/list-style-type">', $result);
    }

    public function testNoAttributesToTestReadMoreGivesError()
    {
        $result = $this->doer->renderFactory('', null, 'wpt_test_read_more');
        $this->assertTestReadMoreNotFound($result);
    }

    public function testUnknownIdToTestReadMoreGivesError()
    {
        $result = $this->doer->renderFactory(array('id' => -1), null, 'wpt_test_read_more');
        $this->assertTestReadMoreNotFound($result);
    }

    public function testTestReadMoreRendered()
    {
        $attributes = array(
            'name'        => 'eysencks-personality-inventory-epi-extroversionintroversion',
            'start_title' => 'Qwerty',
        );
        $result = $this->doer->renderFactory($attributes, null, 'wpt_test_read_more');

        $this->assertContains('EPI', $result);
        $this->assertContains('Qwerty', $result);
    }

    private function assertTestReadMoreNotFound($result)
    {
        $this->assertNotContains('EPI',                   $result);
        $this->assertContains('UnexpectedValueException', $result);
        $this->assertContains('wpt_test_read_more',       $result);
        $this->assertContains('Can not find',             $result);
    }
}
