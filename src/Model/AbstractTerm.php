<?php

/**
 * @method integer getId() Gets the current value of id
 * @method integer getIdOnce() Gets cached value of id
 * @method string getTitle() Gets the current value of title
 * @method string getSlug() Gets the current value of slug
 * @method string getAbbrOnce() Gets cached value of abbreviration
 * @method string getTitleOnce() Gets cached value of title
 * @method string getDescriptionOnce() Gets cached value of desription
 */
abstract class WpTesting_Model_AbstractTerm extends WpTesting_Model_AbstractModel implements JsonSerializable
{

    protected $columnAliases = array(
        'id'    => 'term_id',
        'title' => 'name',
    );

    /**
     * @return WpTesting_Model_Taxonomy
     */
    public function createTaxonomy()
    {
        return $this->buildRelated('WpTesting_Model_Taxonomy')->getRecord(0);
    }

    /**
     * Abbreviration of title
     *
     * @return string
     */
    public function getAbbr()
    {
        return mb_substr($this->getTitleOnce(), 0, 1, 'UTF-8');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $result = $this->buildRelated('WpTesting_Model_Taxonomy');
        if (!$result->count()) {
            return null;
        }
        return $result->getRecord(0)->getDescription();
    }

    public function getDescriptionAsTooltip()
    {
        return mb_substr(strip_tags($this->getDescriptionOnce()), 0, 1024, 'UTF-8');
    }

    /**
     * @param integer $index
     * @return string
     */
    public function getCssClass($index = null)
    {
        $classParts = explode('_', get_class($this));
        $name = strtolower(end($classParts));
        $id   = $this->getId();
        $slug = $this->getSlug();
        $css  = "$name $name-id-$id $name-slug-$slug";
        if (!is_null($index)) {
            $css .= " $name-index-$index";
        }
        return $css;
    }

    public function jsonSerialize()
    {
        return array(
            'id'    => $this->getId(),
            'title' => $this->getTitle(),
            'slug'  => $this->getSlug(),
        );
    }
}
