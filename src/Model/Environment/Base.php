<?php

abstract class WpTesting_Model_Environment_Base implements WpTesting_Model_IEnvironment
{
    protected $label = null;
    private $text = null;

    /**
     * @param string|Exception $text
     */
    public function __construct($text)
    {
        if ($text instanceof Exception) {
            $text = $text->getMessage();
        }
        $this->text = $text;
    }

    public function label()
    {
        return $this->label;
    }

    public function text()
    {
        return $this->text;
    }
}
