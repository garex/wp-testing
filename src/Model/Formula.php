<?php

/**
 * Formula that translates passed values into true or false
 *
 * Values with percents are replaced for their percentage analogs (when source contains %).
 *
 * @method integer getId() Gets the current value of id
 * @method integer getTestId() Gets the current value of test id
 * @method WpTesting_Model_Formula setTestId(integer $id) Sets the value for test id
 * @method integer getResultId() Gets the current value of result id
 * @method integer getResultIdOnce() Gets cached value of result id
 * @method WpTesting_Model_Formula setResultId(integer $id) Sets the value for result id
 * @method string getSource() Gets the current value of source
 */
class WpTesting_Model_Formula extends WpTesting_Model_Compilable implements JsonSerializable
{

    protected $columnAliases = array(
        'id'        => 'formula_id',
        'source'    => 'formula_source',
    );

    protected function createEmpty()
    {
        return new WpTesting_Model_Formula();
    }

    public function jsonSerialize()
    {
        return array(
            'id'     => $this->getId(),
            'source' => $this->getSourceOnce(),
        );
    }

    public function addValue($name, $value, $percentageValue = null)
    {
        if (is_null($value)) {
            throw new InvalidArgumentException('Value ' . $name . ' can not be null');
        }

        if (strpos($this->getSourceOnce(), '%') && is_null($percentageValue)) {
            throw new InvalidArgumentException('Percentage value ' . $name . ' can not be null when source contains percentage');
        }

        return parent::addValue($name, $value, $percentageValue);
    }

    /**
     * Does this formula has some source?
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return (trim($this->getSourceOnce()) == '');
    }

    /**
     * Evaluates formula with values and checks if it's true or not.
     *
     * Currently most dangerous part.
     *
     * @return boolean
     */
    public function isTrue()
    {
        return $this->compile();
    }

    /**
     * @return WpTesting_Model_Result
     */
    public function createResult()
    {
        return $this->createRelated('WpTesting_Model_Result');
    }

    public function validateSource(WpTesting_Model_Formula $me, &$values, &$oldValues, &$relatedRecords, &$cache, &$validationMessages)
    {
        if (isset($oldValues['formula_source'][0]) && $oldValues['formula_source'][0] == $values['formula_source']) {
            // No need to validate not changed values.
            return;
        }

        if ($me->hasPercentsAndValues()) {
            $validationMessages['formula_source'] = sprintf(__('Formula for %s is incompatible as it contains both numbers and percentages', 'wp-testing'), $me->createResult()->getTitle());
        } elseif (!$me->isCorrectFromTest()) {
            $validationMessages['formula_source'] = sprintf(__('Formula for %s has error', 'wp-testing'), $me->createResult()->getTitle());
        }
    }

    protected function configure()
    {
        parent::configure();
        fORM::registerHookCallback($this, 'post::validate()', array($this, 'validateSource'));
    }
}
