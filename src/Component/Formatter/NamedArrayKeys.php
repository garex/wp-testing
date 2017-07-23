<?php

class WpTesting_Component_Formatter_NamedArrayKeys extends WpTesting_Component_Formatter_Base
{
    private $keys = array();

    public function __construct($row, $format)
    {
        $this->keys = array_flip(array_keys($row));
        $format = preg_replace_callback('/(^|[^%])%([a-zA-Z0-9_-]+)\$/', array($this, 'replaceCallback'), $format);

        parent::__construct(vsprintf($format, $row));
    }

    private function replaceCallback($matches)
    {
        return $matches[1] . '%' . ($this->keys[$matches[2]] + 1) . '$';
    }
}
