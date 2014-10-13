<?php

class ScaleTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var WpTesting_Model_Scale
     */
    private $scale = null;

    protected function setUp()
    {
        $this->scale = new WpTesting_Model_Scale();
    }

    public function testScaleCanSetRangeToNotNullValues()
    {
        $this->scale->setRange(1, 2);
        $this->scale->setRange(0, 1);
        $this->setExpectedException('InvalidArgumentException', 'null');
        $this->scale->setRange(0, null);
    }

    public function testScaleValuesMustBeJustIntegers()
    {
        $this->scale->setRange('1', '2');
        $this->scale->setRange('1', 2);
        $this->scale->setRange('1', 2.0);
        $this->setExpectedException('InvalidArgumentException', 'integer');
        $this->scale->setRange('1.8', 2.2);
    }

    public function testScaleRangeMinimumShouldBeLessThanMaximum()
    {
        $this->scale->setRange(1, '2');
        $this->setExpectedException('InvalidArgumentException', 'minimum 2 must be less than maximum 1');
        $this->scale->setRange('2', 1.0);
    }

    /**
     * @dataProvider scaleValueMustBeWithinRangeProvider
     * @param boolean $isSuccess
     * @param integer $min
     * @param integer $max
     * @param integer $value
     */
    public function testScaleValueMustBeWithinRange($isSuccess, $min, $max, $value)
    {
        if (!is_null($min)) {
            $this->scale->setRange($min, $max);
        }
        if (!$isSuccess) {
            $this->setExpectedException('InvalidArgumentException', 'within range');
        }
        $this->scale->setValue($value);
    }

    public function scaleValueMustBeWithinRangeProvider()
    {
        return array(
            array(false, null, null, 5),
            array(false, 1,    4,    5),
            array(true,  1,    5,    5),
        );
    }

    public function testScaleRangeMustBeAroundValue()
    {
        $this->scale->setRange(1, 10)->setValue(5);
        $this->setExpectedException('InvalidArgumentException', 'must include value');
        $this->scale->setRange(100, 200);
    }

    public function testScaleValueAlwaysAvailableAsPercentage()
    {
        $this->assertEquals( '0%', $this->scale->getValueAsPercentage());
        $this->assertEquals('23%', $this->scale->setRange(0, 100)->setValue(23)->getValueAsPercentage());
        $this->assertEquals('23%', $this->scale->setRange(0,  98)->setValue(23)->getValueAsPercentage());
        $this->assertEquals('23%', $this->scale->setRange(0,  52)->setValue(12)->getValueAsPercentage());
    }

    public function testScaleValueAlwaysAvailableAsRatio()
    {
        $this->assertEquals(   0, $this->scale->getValueAsRatio());
        $this->assertEquals(0.23, $this->scale->setRange(0, 100)->setValue(23)->getValueAsRatio());
        $this->assertEquals(0.23, $this->scale->setRange(0,  98)->setValue(23)->getValueAsRatio());
        $this->assertEquals(0.23, $this->scale->setRange(0,  52)->setValue(12)->getValueAsRatio());
    }

    public function testScaleValueCanNotBeSetWithoutMinMaxRange()
    {
        $this->setExpectedException('InvalidArgumentException', 'within range');
        $this->scale->setValue(123);
    }

    public function testScaleRangeCanBeSetFromAnotherScale()
    {
        $another = new WpTesting_Model_Scale();

        $this->assertEquals('23%', $this->scale
            ->extractRangeFrom($another->setRange(0, 100))
            ->setValue(23)
            ->getValueAsPercentage()
        );
    }

    public function testScaleValueAndMaximumAlwaysAvailable()
    {
        $this->assertEquals(  0, $this->scale->getValue());
        $this->assertEquals(  0, $this->scale->getMaximum());
        $this->assertEquals( '', $this->scale->getValue());
        $this->assertEquals( '', $this->scale->getMaximum());

        $this->scale->setRange(0, 100)->setValue(23);
        $this->assertEquals( 23, $this->scale->getValue());
        $this->assertEquals(100, $this->scale->getMaximum());
    }
}