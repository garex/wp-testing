<?php

class WpTesting_Widget_PlaceholderTemplate_Collection
{
    /**
     * @var array|WpTesting_Widget_PlaceholderTemplate[]
     */
    private $templates = array();

    /**
     * Set template by key
     *
     * @param string $key
     * @param string $template
     * @return self
     */
    public function set($key, $template)
    {
        $this->templates[$key] = new WpTesting_Widget_PlaceholderTemplate($template);
        return $this;
    }

    /**
     * Prepend new template to array of templates
     *
     * @param string $key
     * @param string $template
     * @return self
     */
    public function prepend($key, $template)
    {
        return $this->addToArray('array_unshift', $key, $template);
    }

    /**
     * Append new template to array of templates
     *
     * @param string $key
     * @param string $template
     * @return self
     */
    public function append($key, $template)
    {
        return $this->addToArray('array_push', $key, $template);
    }

    /**
     * Apply values to template by key
     *
     * @param string $key
     * @param array  $values
     * @return string|array
     */
    public function apply($key, array $values)
    {
        if (empty($this->templates[$key])) {
            throw new UnexpectedValueException('Key not found in templates collection: ' . $key);
        }
        if (!is_array($this->templates[$key])) {
            return $this->templates[$key]->apply($values);
        }
        $result = array();
        foreach ($this->templates[$key] as $template) {
            $result[] = $template->apply($values);
        }
        return $result;
    }

    private function addToArray($operation, $key, $template)
    {
        if (empty($this->templates[$key])) {
            $this->templates[$key] = array();
        }
        $operation($this->templates[$key], new WpTesting_Widget_PlaceholderTemplate($template));
        return $this;
    }
}
