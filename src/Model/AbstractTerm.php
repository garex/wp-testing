<?php

/**
 * @method integer getId() Gets the current value of id
 * @method string getTitle() Gets the current value of title
 * @method string getSlug() Gets the current value of slug
 * @method string getAbbrOnce() Gets cached value of abbreviration
 * @method string getTitleOnce() Gets cached value of title
 */
abstract class WpTesting_Model_AbstractTerm extends WpTesting_Model_AbstractModel
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

    /**
     * @return number
     */
    public function getSum()
    {
        return null;
    }

    /**
     * @return number
     */
    public function getMaximum()
    {
        return null;
    }

    public function getAggregatesTitle()
    {
        $parts   = array();
        if (!is_null($this->getSum())) {
            $parts[] = sprintf('∑ %g', $this->getSum());
        }
        if ($this->getSum() != $this->getMaximum()) {
            $parts[] = sprintf('max %g', $this->getMaximum());
        }
        return implode(', ', $parts);
    }

}
