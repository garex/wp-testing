<?php

class FormulaTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var WpTesting_Model_Formula
     */
    private $formula = null;

    protected function setUp()
    {
        $this->formula = new WpTesting_Model_Formula();
    }

    public function testSlugsAndNamesTranslatedIntoValues()
    {
        $this->assertEquals('12>12&&12<12', $this->formula
            ->setSource('Introversion/Extraversion scale > 12 and slug-neurothism < 12')
            ->addValue('Introversion/Extraversion scale', 12)
            ->addValue('slug-neurothism', 12)
            ->substitute()
        );
    }

    public function testOnlyNumbersAllowedAsRealValuesOfVars()
    {
        $this->setExpectedException('InvalidArgumentException', 'must be numeric');
        $this->formula->setSource('-')->addValue('key', 'value');
    }

    /**
     * @dataProvider onlyAllowedSymbolsLeftProvider
     * @param string $passedFormula
     * @param string $expectedResult
     */
    public function testOnlyAllowedSymbolsLeft($passedFormula, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->formula
            ->setSource($passedFormula)
            ->substitute()
        );
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
        $this->assertEquals($expectedResult, $this->formula
            ->setSource($passedFormula)
            ->substitute()
        );
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
        $this->assertEquals($expectedResult, $this->formula
            ->setSource($passedFormula)
            ->substitute()
        );
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
        $this->formula->setSource('something > 50%')->addValue('something', '23');
    }

    public function testValuesReplacedByPercentageInPercentageFormulas()
    {
        $this->assertEquals('0.46>0.5', $this->formula
            ->setSource('something > 50%')
            ->addValue('something', '23', 0.46)
            ->substitute()
        );
    }

    /**
     * @dataProvider formulaIsTrueOrNotProvider
     * @param string $formula
     * @param boolean $isTrue
     * @param array $values
     */
    public function testFormulaIsTrueOrNot($formula, $isTrue, $values)
    {
        $this->assertEquals($isTrue, $this->formula
            ->setSource($formula)
            ->addValues($values)
            ->isTrue()
        );
    }

    public function formulaIsTrueOrNotProvider()
    {
        return array(
            array('scale 1 > 34 scale 4 < 45', true, array(
                array('scale 1', 36, 0.3),
                array('scale 4', 30, 0.3),
            )),

            array('scale 1 < 34 scale 3 < 30 scale2 > 21', false, array(
                array('scale 1', 32, 0.3),
                array('scale 3', 25, 0.3),
                array('scale2',  21, 0.3),
            )),

            array('scale55 > 2 scale0 < 1', true, array(
                array('scale55',  3, 0.3),
                array('scale0',   0, 0.3),
            )),

            array('scale 121<=121 scale211 =>211', true, array(
                array('scale 121',  121, 0.3),
                array('scale211',   211, 0.3),
            )),

            array('scale11 = 15 or scale 18 => 11', false, array(
                array('scale11',  11, 0.3),
                array('scale 18', 10, 0.3),
            )),

            array('scale18 => 18 scale 288 => 11 or scale30 => 18', true, array(
                array('scale18',   19, 0.3),
                array('scale 288', 12, 0.3),
                array('scale 30',  3, 0.3),
            )),

            array('scale18 => 18 and (scale 288 => 11 or scale30 => 18)', true, array(
                array('scale18',   19, 0.3),
                array('scale 288', 12, 0.3),
                array('scale 30',  3, 0.3),
            )),

            array('scale18 > 15% or scale30 < 19%', false, array(
                array('scale18', 1, 0.13),
                array('scale30', 1, 0.19),
            )),

            array('scale3 > 150% or scale20 > 18% or scale40 > 40% and scale10 <= 3%', true, array(
                array('scale3',  1, 1.51),
                array('scale20', 1, 0.20),
                array('scale40', 1, 0.35),
                array('scale10', 1, 0.02),
            )),
        );
    }

    public function testSimilarScaleNamesSubstitutesCorrectly()
    {
        $this->assertEquals('11>10&&22>20', $this->formula
            ->setSource('ascale55 > 10 ascale555 > 20')
            ->addValue('ascale55',  11)
            ->addValue('ascale555', 22)
            ->addValue('cale55',    33)
            ->addValue('le',        44)
            ->substitute()
        );
    }

    public function testSingleEqualityOperatorReplacedIntoDouble()
    {
        $this->assertEquals('10==10||10==20', $this->formula
            ->setSource('somescale = 10 or somescale == 20')
            ->addValue('somescale',  10)
            ->substitute()
        );
    }

    /**
     * @dataProvider formulaIsCorrectOrNotProvider
     * @param string $formula
     * @param boolean $isCorrect
     * @param array $valueNames
     */
    public function testFormulaIsCorrect($formula, $isCorrect, $valueNames)
    {
        $this->assertEquals($isCorrect, $this->formula
            ->setSource($formula)
            ->isCorrect($valueNames)
        );
    }

    public function formulaIsCorrectOrNotProvider()
    {
        return array(
            array('Scale A > 34 Scale B < 45',     true,  array('Scale A', 'Scale B')),
            array('Scale A > 34 Scale B < 45',     false, array('Scale X')),
            array('',                              true,  array('Scale X')),
            array(' something empty?    ',         false, array('Scale X')),
            array('<><>',                          false, array('Scale X')),
            array('var_dump(file("/etc/passwd"))', false, array('Scale X')),
        );
    }
}