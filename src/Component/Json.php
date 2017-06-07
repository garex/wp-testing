<?php

class WpTesting_Component_Json
{
    private $options;

    public function __construct()
    {
        if (defined('JSON_UNESCAPED_UNICODE')) {
            $this->options |= JSON_UNESCAPED_UNICODE;
        }

    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function encode($value)
    {
        if ($this->options) {
            return json_encode($value, $this->options);
        }

        return json_encode($value);
    }
}
