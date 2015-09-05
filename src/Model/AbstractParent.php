<?php

/**
 * Allows to use parent class object without knowing it's data.
 * Used in addons when adding behaviours.
 */
abstract class WpTesting_Model_AbstractParent extends WpTesting_Model_AbstractModel
{

    /**
     * Used in addons when adding behaviours
     * @var WpTesting_Model_AbstractParent
     */
    private $parent = null;

    public function __construct($key = null)
    {
        if (is_object($key) && ($key instanceof self)) {
            $this->setParent($key);
            return;
        }
        parent::__construct($key);
    }

    public function setParent(WpTesting_Model_AbstractParent $parent)
    {
        $this->parent = $parent;
        if (is_null($this->wp)) {
            $this->wp = $parent->wp;
        }
        return $this;
    }

    /**
     * @return self
     */
    protected function me()
    {
        return is_null($this->parent) ? $this : $this->parent;
    }
}
