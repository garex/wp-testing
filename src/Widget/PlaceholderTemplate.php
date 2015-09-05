<?php

class WpTesting_Widget_PlaceholderTemplate
{
    private $template;

    public function __construct($template)
    {
        if (empty($template)) {
            throw new InvalidArgumentException('Plcaholder template can not be empty!');
        }
        if (!is_string($template)) {
            throw new InvalidArgumentException('Plcaholder template must be a string');
        }

        $this->template = $template;
    }

    public function apply(array $values)
    {
        return str_replace(array_keys($values), $values, $this->template);
    }
}
