<?php

/**
 * @method int getValue() getValue() Gets the current value for score
 * @method WpTesting_Model_Score setValue() setValue(int $value) Sets the value for score
 * @method int getScaleId() getScaleId() Gets the current value for scale id
 * @method WpTesting_Model_Score setScaleId() setScaleId(int $scaleId) Sets the scale id for score
 */
class WpTesting_Model_Score extends WpTesting_Model_AbstractModel
{

    protected $columnAliases = array(
        'value' => 'score_value',
    );

    public function getValueWithoutZeros()
    {
        $value = $this->getValue();
        return empty($value) ? '' : $value;
    }

    /**
     * @return WpTesting_Model_Scale
     */
    public function createScale()
    {
        return $this->createWpTesting_Model_Scale('scale_id');
    }
}
