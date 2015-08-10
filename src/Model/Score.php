<?php

/**
 * @method int getScaleId() getScaleId() Gets the current value for scale id
 * @method WpTesting_Model_Score setScaleId() setScaleId(int $scaleId) Sets the scale id for score
 */
class WpTesting_Model_Score extends WpTesting_Model_AbstractModel
{

    protected $columnAliases = array(
        'value' => 'score_value',
    );

    public function getId()
    {
        return $this->get('scale_id') . '|' . $this->get('answer_id');
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return floatval($this->get('value'));
    }

    public function getValueWithoutZeros()
    {
        $value = $this->getValue();
        return empty($value) ? '' : $value;
    }

    public function setScoreValue($value)
    {
        if (empty($value)) {
            $value = 0;
        }
        $this->set('score_value', $value);
        return $this;
    }

    /**
     * @return WpTesting_Model_Scale
     */
    public function createScale()
    {
        return $this->createWpTesting_Model_Scale('scale_id');
    }
}
