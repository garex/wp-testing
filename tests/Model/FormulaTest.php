<?php

class FormulaTest extends PHPUnit_Framework_TestCase
{

    public function testSlugsAndNamesTranslatedIntoValues()
    {
        $formula = new WpTesting_Model_Formula('Introversion/Extraversion scale > 12 and slug-neurothism < 12');
        $formula->addValue('Introversion/Extraversion scale', 12)->addValue('slug-neurothism', 12);

        $this->assertEquals('12>12&&12<12', $formula->substitute());
    }

    public function testOnlyNumbersAllowedAsRealValuesOfVars()
    {
        $this->setExpectedException('InvalidArgumentException', 'must be numeric');
        $formula = new WpTesting_Model_Formula('-');
        $formula->addValue('key', 'value');
    }

    /**
     * @dataProvider onlyAllowedSymbolsLeftProvider
     * @param string $passedFormula
     * @param string $expectedResult
     */
    public function testOnlyAllowedSymbolsLeft($passedFormula, $expectedResult)
    {
        $formula = new WpTesting_Model_Formula($passedFormula);
        $this->assertEquals($expectedResult, $formula->substitute());
    }

    public function onlyAllowedSymbolsLeftProvider()
    {
        return array(
            array('45 > 123 +*/\\  ;"!@#$%^*() hey! and 34 > 23 %', '45>123&&34>0.23'),
            array('45 > 123 and 34 > 23',  '45>123&&34>23'),
            array('somefunction(45) < 23', '(45)<23'),
        );
    }

    /**
     * @dataProvider operatorsNormalizedProvider
     * @param string $passedFormula
     * @param string $expectedResult
     */
    public function testOperatorsNormalized($passedFormula, $expectedResult)
    {
        $formula = new WpTesting_Model_Formula($passedFormula);
        $this->assertEquals($expectedResult, $formula->substitute());
    }

    public function operatorsNormalizedProvider()
    {
        return array(
            array('12 <> 12', '12!=12'),
            array('12 >< 12', '12!=12'),

            array('12 >= 12', '12>=12'),
            array('12 <= 12', '12<=12'),
            array('12 => 12', '12>=12'),
            array('12 =< 12', '12<=12'),
        );
    }

    /**
     * @dataProvider logicalAndsPlacedAutomaticallyProvider
     * @param string $passedFormula
     * @param string $expectedResult
     */
    public function testLogicalAndsPlacedAutomatically($passedFormula, $expectedResult)
    {
        $formula = new WpTesting_Model_Formula($passedFormula);
        $this->assertEquals($expectedResult, $formula->substitute());
    }

    public function logicalAndsPlacedAutomaticallyProvider()
    {
        return array(
            array('12 > 12 and 23 < 45', '12>12&&23<45'),
            array('12 > 12  23 < 45', '12>12&&23<45'),
        );
    }

    public function testRequirePercentage()
    {
        $this->setExpectedException('InvalidArgumentException', 'can not be null when source contains percentage');
        $formula = new WpTesting_Model_Formula('something > 50%');
        $formula->addValue('something', '23');
    }

    public function testValuesReplacedByPercentageInPercentageFormulas()
    {
        $formula = new WpTesting_Model_Formula('something > 50%');
        $formula->addValue('something', '23', 0.46);

        $this->assertEquals('0.46>0.5', $formula->substitute());
    }
}