<?php

/**
 * Formula that translates passed values into true or false
 *
 * Formula text and values are immutable — can be set only once.
 *
 * Values with percents are replaced for their percentage analogs (when source contains %).
 *
 */
class WpTesting_Model_Formula
{

    /**
     * Formula's source — some kind of "source code"
     * @var string
     */
    private $source = null;

    /**
     * Values, that are substitutes in formula during comparing
     * @var array
     */
    private $values = array();

    /**
     * @param string $source Formula's source — some kind of "source code"
     */
    public function __construct($source)
    {
        $this->setSource($source);
    }

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
        if (isset($this->values[$name])) {
            throw new InvalidArgumentException('Value ' . $name . ' can not be added twice');
        }
        if (is_null($value)) {
            throw new InvalidArgumentException('Value ' . $name . ' can not be null');
        }

        if (strpos($this->source, '%')) {
            if (is_null($percentageValue)) {
                throw new InvalidArgumentException('Percentage value ' . $name . ' can not be null when source contains percentage');
            }
            if (!is_numeric($percentageValue)) {
                throw new InvalidArgumentException('Percentage value ' . $name . ' must be numeric. Provided: ' . var_export($percentageValue, true));
            }
            $this->values[$name] = floatval($percentageValue);
            return $this;
        }

        if (!is_numeric($value)) {
            throw new InvalidArgumentException('Value ' . $name . ' must be numeric. Provided: ' . var_export($value, true));
        }

        $intValue   = intval($value);
        $floatValue = floatval($value);
        $this->values[$name] = ($intValue == $floatValue) ? $intValue : $floatValue;

        return $this;
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
        $result = $this->source;

        $values = $this->values;
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
            call_user_method_array('addValue', $this, $params);
        }
        return $this;
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

    /**
     * Formula's source — some kind of "source code"
     *
     * @param string $source
     * @throws InvalidArgumentException
     * @return WpTesting_Model_Formula
     */
    protected function setSource($source)
    {
        if (is_null($source)) {
            throw new InvalidArgumentException('Formula source can not be null');
        }
        if (!is_null($this->source)) {
            throw new InvalidArgumentException('Formula source can not be rewritten');
        }
        $this->source = $source;
        return $this;
    }
}
