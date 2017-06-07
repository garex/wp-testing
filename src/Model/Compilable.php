<?php

/**
 * Translates passed to source values into compilable value.
 *
 * Values with percents are replaced for their percentage analogs (when source contains %).
 *
 * @method string getSource() Gets the current value of source
 */
abstract class WpTesting_Model_Compilable extends WpTesting_Model_AbstractModel
{
    /**
     * mb_strlen graceful degradation
     *
     * @var string
     */
    private $stringLengthFunction = 'strlen';

    /**
     * Values, that substituted in during compile.
     *
     * @var array
     */
    private $substituteValues = array();

    /**
     * Text values, that used in during compile evaluation.
     *
     * @var array
     */
    private $textValues = array();

    private $source = null;

    public function __construct($key = null)
    {
        parent::__construct($key);

        if (function_exists('mb_strlen')) {
            $this->stringLengthFunction = 'mb_strlen';
        } elseif (function_exists('iconv_strlen')) {
            $this->stringLengthFunction = 'iconv_strlen';
        }
    }

    /**
     * Gets the current value of source once.
     *
     * @return string
     */
    public function getSourceOnce()
    {
        if (is_null($this->source)) {
            $this->source = $this->getSource();
        }

        return $this->source;
    }

    /**
     * Sets the value for source.
     *
     * @param string $source
     *
     * @return self
     */
    public function setSource($source)
    {
        $this->source = null;
        parent::setSource($source);
        return $this;
    }

    /**
     * @return self
     */
    public function resetValues()
    {
        $this->substituteValues = array();
        $this->textValues = array();
        return $this;
    }

    /**
     * Adds value to values list without rewriting. Converts value type to integer if it's not double.
     *
     * @param string $name
     * @param integer|float $value
     * @param float $percentageValue Required only when we have "%" in source
     * @throws InvalidArgumentException
     * @return self
     */
    public function addValue($name, $value, $percentageValue = null)
    {
        if (isset($this->substituteValues[$name])) {
            throw new InvalidArgumentException('Value ' . $name . ' can not be added twice');
        }

        if (strpos($this->getSourceOnce(), '%')) {
            if (!is_numeric($percentageValue)) {
                throw new InvalidArgumentException('Percentage value ' . $name . ' must be numeric. Provided: ' . var_export($percentageValue, true));
            }
            $this->substituteValues[$name] = floatval($percentageValue);
            return $this;
        }

        if (is_numeric($value)) {
            $intValue   = intval($value);
            $floatValue = floatval($value);
            $this->substituteValues[$name] = ($intValue == $floatValue) ? $intValue : $floatValue;
        } else {
            $this->substituteValues[$name] = '$t[' . count($this->textValues) . ']';
            $this->textValues[] = $value;
        }

        return $this;
    }

    public function addVariable(WpTesting_Model_FormulaVariable $variable)
    {
        $name = $variable->getSource();

        if (strpos($this->getSourceOnce(), $name) === false) {
            return $this;
        }

        return $this->addValue($name, $variable->getValue(), $variable->getValueAsRatio());
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
     * Evaluates source with values.
     *
     * Currently most dangerous part.
     *
     * @return mixed
     */
    protected function compile()
    {
        $t = $this->textValues;
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
        $result = $this->getSourceOnce();
        if (empty($result)) {
            return 'false';
        }

        $values = $this->substituteValues;
        uksort($values, array($this, 'compareValueNamesInverted'));

        // Replace all values
        $result = str_replace(array_keys($values), array_values($values), $result);

        // Lowercase
        $result = strtolower($result);

        // Replace and/or/not
        foreach (array('and' => '&&', 'or' => '||', 'not' => '!') as $from => $to) {
            $result = preg_replace('/([^a-z]?)' . $from . '([^a-z])/', '$1' . $to . '$2', $result);
        }

        // Leave only allowed
        // ustimenko: WARNING "-" should be 1st @see https://bugs.php.net/bug.php?id=47229
        $operators = '-+*/<>=&|!';
        $allowed   = $operators . '().% ';
        preg_match_all('/(?:['. preg_quote($allowed, '/') . '\d]+|\$t\[\d+\])/', $result, $allowedMatches);
        $result    = implode('', $allowedMatches[0]);

        // Normalize comparisions
        $result    = str_replace(array('><', '<>', '=>', '=<'), array('!=', '!=', '>=', '<='), $result);

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

        // Remove whitespaces around operators and parentheses
        $result = preg_replace('/ *([' . preg_quote($operators . '()', '/') . ']+) */', '$1', $result);

        // Add whitespace between values and NOT operator
        $result = preg_replace('/(\d)(' . preg_quote('!') . '[\d\(])/', '$1 $2', $result);

        // Replace left whitespaces with ands
        $result = preg_replace('/ +/', '&&', $result);

        // Add whitespaces between doubled operators
        $result = preg_replace('/([\-\+\*\/])\1/', '$1 $1', $result);

        // Check if there is no parse error
        $parser = new PHPParser_Parser(new PHPParser_Lexer());
        $parser->parse('<?php ' . $result . ';');

        return $result;
    }

    /**
     * Compares values' names to sort inverted by longest length then by traditional strings comparing
     *
     * @param string $name1
     * @param string $name2
     * @return integer
     */
    private function compareValueNamesInverted($name1, $name2)
    {
        $function = $this->stringLengthFunction;
        $length1  = $function($name1);
        $length2  = $function($name2);

        if ($length1 < $length2) {
            return 1;
        } elseif ($length1 > $length2) {
            return -1;
        } elseif ($name1 < $name2) {
            return 1;
        } elseif ($name1 > $name2) {
            return -1;
        } else {
            return 0;
        }
    }

    private function transformPercent($matches)
    {
        return $matches[1] / 100;
    }

    /**
     * @return boolean
     */
    protected function hasPercentsAndValues()
    {
        // Check for percents with abs values
        $source         = $this->getSourceOnce();
        $percentRegexp  = '/\d+ ?%/';
        $hasPercents    = preg_match($percentRegexp, $source);

        if (!$hasPercents) {
            return false;
        }

        $sourceWithoutPercents = preg_replace($percentRegexp, '', " $source ");
        $valueRegexp           = '/[^a-z]\d+[^a-z]/';
        $hasValues             = (bool)preg_match($valueRegexp, $sourceWithoutPercents);

        return $hasValues;
    }

    /**
     * Check for source correctness using test variables.
     *
     * @return boolean
     */
    protected function isCorrectFromTest()
    {
        /* @var $test WpTesting_Model_Test */
        $test = $this->createRelated('WpTesting_Model_Test')->setWp($this->getWp());
        $varNames = array();
        foreach ($test->buildFormulaVariables() as $var) {
            $varNames[] = $var->getSource();
        }
        return $this->isCorrect($varNames);
    }

    /**
     * Tests compilable, knowing it's possible values for correctnes.
     *
     * @param array $valueNames If not provided, tries to get current values if they are exists.
     * @throws InvalidArgumentException
     *
     * @return boolean
     */
    public function isCorrect(array $valueNames = array())
    {
        $source = $this->getSourceOnce();
        if (empty($source)) {
            return true;
        }
        $experiment = $this->createEmpty()->setSource($source);

        if (empty($valueNames)) {
            $experiment->addValues($this->substituteValues);
        } else {
            foreach (array_unique($valueNames) as $name) {
                $experiment->addVariable(new WpTesting_Model_FormulaVariable_NullValue($name));
            }
        }

        try {
            $substitute = $experiment->substitute();
            return !empty($substitute);
        } catch (PHPParser_Error $e) {
            return false;
        }
    }

    /**
     * @return self
     */
    abstract protected function createEmpty();
}
