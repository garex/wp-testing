<?php

/**
 * @method integer getId() getId() Gets the current value of id
 * @method string getTitle() getTitle() Gets the current value of title
 * @method string getSlug() getSlug() Gets the current value of slug
 */
abstract class WpTesting_Model_AbstractTerm extends WpTesting_Model_AbstractModel
{

    protected $columnAliases = array(
        'id'    => 'term_id',
        'title' => 'name',
    );

    /**
     * Abbreviration of title
     *
     * @return string
     */
    public function getAbbr()
    {
        return mb_substr($this->getTitle(), 0, 1, 'UTF-8');
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

}
