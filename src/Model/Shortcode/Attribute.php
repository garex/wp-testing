<?php

class WpTesting_Model_Shortcode_Attribute
{

    /**
     * @var string
     */
    private $externalName;

    /**
     * @var string
     */
    private $propertyName;

    /**
     * @var mixed
     */
    private $defaultValue;

    private $allowMask;
    private $allowList;
    private $errorGuide;

    /**
     * @param string $externalName
     * @param string $propertyName
     * @param mixed $defaultValue
     */
    public function __construct($externalName, $propertyName = null, $defaultValue = null)
    {
        $this->externalName = $externalName;
        $this->propertyName = (!empty($propertyName)) ? $propertyName : $externalName;
        $this->defaultValue = $defaultValue;
    }

    public function allowOnlyMask($mask)
    {
        $this->allowMask = $mask;
        return $this;
    }

    public function allowOnlyList(array $list)
    {
        $this->allowList = $list;
        return $this;
    }

    public function guideOnError($text)
    {
        $this->errorGuide = $text;
    }

    public function toDefaultsArray()
    {
        return array(
            $this->externalName => $this->defaultValue
        );
    }

    public function toExternalNamesArray()
    {
        return array(
            $this->externalName => $this
        );
    }

    /**
     * @param mixed $dirtyValue
     * @return mixed
     * @throws UnexpectedValueException
     */
    public function cleanValue($dirtyValue)
    {
        if (empty($dirtyValue)) {
            return $dirtyValue;
        }

        if ($this->allowMask && !preg_match($this->allowMask, $dirtyValue)) {
            throw new UnexpectedValueException($this->formExceptionMessage(
                'Value "%s" for attribute "%s" is not allowed by mask',
                $dirtyValue, $this->externalName
            ));
        }

        if (is_array($this->allowList)) {
            if (isset($this->allowList[$dirtyValue])) {
                return $this->allowList[$dirtyValue];
            }
            $allowed = implode(', ', array_keys($this->allowList));
            throw new UnexpectedValueException($this->formExceptionMessage(
                'Value "%s" for attribute "%s" is not in allowed list: %s',
                $dirtyValue, $this->externalName, $allowed
            ));
        }

        return $dirtyValue;
    }

    /**
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    private function formExceptionMessage()
    {
        $argsPhp52Workaround = func_get_args();
        $message = call_user_func_array('sprintf', $argsPhp52Workaround);
        if ($this->errorGuide) {
            $message .= PHP_EOL . PHP_EOL . $this->errorGuide;
        }
        return $message;
    }
}
