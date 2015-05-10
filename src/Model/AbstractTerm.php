<?php

/**
 * @method integer getId() getId() Gets the current value of id
 * @method string getTitle() getTitle() Gets the current value of title
 * @method string getSlug() getSlug() Gets the current value of slug
 * @method string getAbbrOnce() getAbbrOnce() Gets cached value of abbreviration
 * @method string getTitleOnce() getTitleOnce() Gets cached value of title
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
        return $this->buildWpTesting_Model_Taxonomy()->getRecord(0);
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

    public function getDescription()
    {
        /* @var $result fRecordset */
        $result = $this->buildWpTesting_Model_Taxonomy();
        if (!$result->count()) {
            return null;
        }
        return $result->getRecord(0)->getDescription();
    }

    public function getCssClass($index = null)
    {
        $name = strtolower(end(explode('_', get_class($this))));
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
            $parts[] = sprintf('∑ %d', $this->getSum());
        }
        if ($this->getSum() != $this->getMaximum()) {
            $parts[] = sprintf('max %d', $this->getMaximum());
        }
        return implode(', ', $parts);
    }

}
