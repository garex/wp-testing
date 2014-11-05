<?php

/**
 * Formula that translates passed values into true or false
 *
 * Values with percents are replaced for their percentage analogs (when source contains %).
 *
 * @method integer getId() getId() Gets the current value of id
 * @method integer getTestId() getTestId() Gets the current value of test id
 * @method integer getResultId() getResultId() Gets the current value of result id
 * @method WpTesting_Model_Formula setResultId() setResultId(integer $id) Sets the value for result id
 * @method string getSource() getSource() Gets the current value of source
 * @method WpTesting_Model_Formula setSource() setSource(string $source) Sets the value for source
 */
class WpTesting_Model_Formula extends WpTesting_Model_AbstractModel
{

    protected $columnAliases = array(
        'id'        => 'formula_id',
        'source'    => 'formula_source',
    );

    /**
     * Values, that are substitutes in formula during comparing
     * @var array
     */
    private $substituteValues = array();

    /**
     * Adds value to values list without rewriting. Converts value type to integer if it's not double.
     *
     * @param string $name
     * @param numeric $value
     * @param float $percentageValue Required only when we have "%" in source
     * @throws InvalidArgumentException
     * @return WpTesting_Model_Formula
     */
    public function addValue($name, $value, $percentageValue = null)
    {
        if (isset($this->substituteValues[$name])) {
            throw new InvalidArgumentException('Value ' . $name . ' can not be added twice');
        }
        if (is_null($value)) {
            throw new InvalidArgumentException('Value ' . $name . ' can not be null');
        }

        if (strpos($this->getSource(), '%')) {
            if (is_null($percentageValue)) {
                throw new InvalidArgumentException('Percentage value ' . $name . ' can not be null when source contains percentage');
            }
            if (!is_numeric($percentageValue)) {
                throw new InvalidArgumentException('Percentage value ' . $name . ' must be numeric. Provided: ' . var_export($percentageValue, true));
            }
            $this->substituteValues[$name] = floatval($percentageValue);
            return $this;
        }

        if (!is_numeric($value)) {
            throw new InvalidArgumentException('Value ' . $name . ' must be numeric. Provided: ' . var_export($value, true));
        }

        $intValue   = intval($value);
        $floatValue = floatval($value);
        $this->substituteValues[$name] = ($intValue == $floatValue) ? $intValue : $floatValue;

        return $this;
    }

    /**
     * Tests formula, knowing it's possible values for correctnes.
     *
     * @param array $valueNames If not provided, tries to get current values if they are exists.
     * @throws InvalidArgumentException
     * @return boolean
     */
    public function isCorrect(array $valueNames = array())
    {
        $source = $this->getSource();
        if (empty($source)) {
            return true;
        }
        $experiment = new WpTesting_Model_Formula();
        $experiment->setSource($source);

        if (empty($valueNames)) {
            $experiment->addValues($this->substituteValues);
        } else {
            foreach (array_unique($valueNames) as $name) {
                $experiment->addValue($name, 12, 0.34);
            }
        }

        if (empty($experiment->substituteValues)) {
            throw new InvalidArgumentException('Value names are required when own values are empty');
        }

        try {
            $substitute = $experiment->substitute();
            return !empty($substitute);
        } catch (PHPParser_Error $e) {
            return false;
        }
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
        $result = @eval('return ' . $this->substitute() . ';');
        return $result;
    }

    /**
     * Substitutes values inside formula. Cleans up source from forbidden content.
     *
     * @throws PHPParser_Error
     * @return string
     */
    public function substitute()
    {
        $result = $this->getSource();
        if (empty($result)) {
            return 'false';
        }

        $values = $this->substituteValues;
        uksort($values, array($this, 'compareValueNamesInverted'));

        // Replace all values
        $result = str_replace(array_keys($values), array_values($values), $result);

        // Lowercase
        $result = strtolower($result);

        // Replace and/or
        $result = str_replace(array('and', 'or'), array('&&', '||'), $result);

        // Leave only allowed
        $result = preg_replace('/[^\d\-%<>=\(\)&\| \.]+/', '', $result);

        // Normalize comparisions
        $result = str_replace(array('><', '<>', '=>', '=<'), array('!=', '!=', '>=', '<='), $result);

        // Normalize equalities
        $result = preg_replace('/=+/', '=', $result);
        $result = preg_replace('/([^!<>])=([^!<>])/', '$1==$2', $result);

        // Normalize percents
        $result = preg_replace('/%+/', '%', $result);

        // Convert percents into floats
        $result = preg_replace_callback('/(\d+) ?%/', array($this, 'transformPercent'), $result);

        // Remove empty brackets
        $result = preg_replace('/\( *\)/', '', $result);

        // Remove percents without numbers
        $result = preg_replace('/([^\d])%+/', '$1', $result);

        // Normalize whitespaces
        $result = preg_replace('/ +/', ' ', trim($result));

        // Remove whitespaces around operators
        $result = preg_replace('/ *([<>!=&\|]+) */', '$1', $result);

        // Replace left whitespaces with ands
        $result = preg_replace('/ +/', '&&', $result);

        // Check if there is no parse error
        $parser = new PHPParser_Parser(new PHPParser_Lexer());
        $parser->parse('<?php ' . $result . ';');

        return $result;
    }

    /**
     * Shortcut method for addValue
     *
     * @param array $values
     * @throws InvalidArgumentException
     * @return WpTesting_Model_Formula
     */
    public function addValues(array $values)
    {
        foreach ($values as $params) {
            call_user_func_array(array($this, 'addValue'), $params);
        }
        return $this;
    }

    /**
     * @return WpTesting_Model_Result
     */
    public function createResult()
    {
        return $this->createWpTesting_Model_Result();
    }

    public function validateSource(WpTesting_Model_Formula $me, &$values, &$oldValues, &$relatedRecords, &$cache, &$validationMessages)
    {
        /* @var $test WpTesting_Model_Test */
        $test = $me->createWpTesting_Model_Test();
        $varNames = array();
        foreach ($test->buildFormulaVariables() as $var) {
            $varNames[] = $var->getSource();
        }
        if ($me->isCorrect($varNames)) {
            return;
        }
        /* @var $result WpTesting_Model_Result */
        $result = $me->createWpTesting_Model_Result();
        $validationMessages['formula_source'] = 'Formula for ' . $result->getTitle() . ' has error';
    }

    protected function configure()
    {
        parent::configure();
        fORM::registerHookCallback($this, 'post::validate()', array($this, 'validateSource'));
    }

    /**
     * Compares values' names to sort by longest length then by traditional strings comparing
     *
     * @param string $name1
     * @param string $name2
     * @return number
     */
    protected function compareValueNames($name1, $name2)
    {
        if (mb_strlen($name1) < mb_strlen($name2)) {
            return -1;
        } elseif (mb_strlen($name1) > mb_strlen($name2)) {
            return 1;
        } elseif ($name1 < $name2) {
            return -1;
        } elseif ($name1 > $name2) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Compares values' names inverted
     *
     * @param string $name1
     * @param string $name2
     * @return number
     */
    protected function compareValueNamesInverted($name1, $name2)
    {
        return $this->compareValueNames($name1, $name2) * -1;
    }

    protected function transformPercent($matches)
    {
        return $matches[1] / 100;
    }
}
