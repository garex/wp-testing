<?php

abstract class WpTesting_Model_Shortcode
{
    const NAME = '';

    /**
     * @var WpTesting_WordPressFacade
     */
    private $wp;

    /**
     * @param WpTesting_WordPressFacade $wp
     * @param array $attributes
     * @throws UnexpectedValueException
     */
    public function __construct(WpTesting_WordPressFacade $wp, array $attributes)
    {
        $this->wp = $wp;
        $this->setProperties($attributes);
    }

    /**
     * Unique name for shortcode
     *
     * When shortcode has id/names, they could be added to it's unique name.
     * In other cases it will be same as name.
     *
     * @return string
     */
    public function getUniqueName()
    {
        $result = $this->getName();
        $id     = $this->getUniqueIdentifier();
        return ($id) ? $result . '#' . $id : $result;
    }

    /**
     * Should be overrided by shortcodes, which could be identified by ids/names/other params
     * @return mixed
     */
    protected function getUniqueIdentifier()
    {
        return null;
    }

    public function getDataForTemplate(WpTesting_Facade_IORM $ormAware)
    {
        return array();
    }

    /**
     * @param array $externalAttributes
     * @return WpTesting_Model_Shortcode
     * @throws UnexpectedValueException
     */
    protected function setProperties(array $externalAttributes)
    {
        /* @var $rawAttributes WpTesting_Model_Shortcode_Attribute[] */
        $rawAttributes = $this->wp->applyFilters(
            'wp_testing_shortcode_attributes_' . $this->getName(),
            $this->initAttributes()
        );

        $defaults = $attributes = array();
        foreach ($rawAttributes as $attribute) {
            $defaults   += $attribute->toDefaultsArray();
            $attributes += $attribute->toExternalNamesArray();
        }

        $cleanAttributes = $this->wp->sanitazeShortcodeAttributes($defaults, $externalAttributes, $this->getName());
        foreach ($cleanAttributes as $externalName => $dirtyValue) {
            $attribute = $attributes[$externalName];
            $property  = $attribute->getPropertyName();
            $this->$property = $attribute->cleanValue($dirtyValue);
        }

        return $this;
    }

    protected function getName()
    {
        // Same as static::NAME in PHP > 5.2
        return constant(get_class($this) . '::NAME');
    }

    /**
     * @return WpTesting_Model_Shortcode_Attribute[]
     */
    protected function initAttributes()
    {
        return array();
    }

    protected function getWp()
    {
        return $this->wp;
    }
}
