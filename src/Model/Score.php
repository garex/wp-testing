<?php

/**
 * @method int getValue() getValue() Gets the current value for score
 * @method int getScaleId() getScaleId() Gets the current value for scale id
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
