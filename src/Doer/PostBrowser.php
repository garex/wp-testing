<?php

class WpTesting_Doer_PostBrowser extends WpTesting_Doer_AbstractDoer
{

    /**
     * @var array
     */
    private $hiddenIds = null;

    /**
     * @param WP_Query $query
     */
    public function addTestsToQuery($query)
    {
        if (!$this->wp->isQueryMain($query) || $query->is_preview() || $query->is_attachment()) {
            return;
        }

        if ($query->is_archive() && !$this->isTaxonomyOf($query, 'wpt_test')) {
            return;
        }

        $query->set('post_type', $this->addTestToPostTypes($query));
        if (!$query->is_home()) {
            return;
        }

        $postIn = $this->getQueryVariableAsArray($query, 'post__in');
        if (empty($postIn)) {
            $postNotIn = array_merge(
                $this->getQueryVariableAsArray($query, 'post__not_in'),
                $this->queryHomepageHiddenTestsOnce()
            );
            $query->set('post__not_in', array_unique($postNotIn));
        } else {
            $postIn = array_diff(
                $postIn,
                $this->queryHomepageHiddenTestsOnce()
            );
            $query->set('post__in', array_unique($postIn));
        }
    }

    /**
     * @param array $classes
     * @return array
     */
    public function inheritPostClassesToTest($classes)
    {
        if (in_array('wpt_test', $classes)) {
            $classes[] = 'post';
            $classes[] = 'type-post';
        } elseif (in_array('single-wpt_test', $classes)) {
            $classes[] = 'single-post';
        }
        return $classes;
    }

    /**
     * @param WP_Query $query
     * @return array
     */
    private function addTestToPostTypes($query)
    {
        $postTypes = $this->getQueryVariableAsArray($query, 'post_type');

        if (in_array('wpt_test', $postTypes)) {
            return $postTypes;
        }
        if (empty($postTypes)) {
            $postTypes[] = $query->is_page ? 'page' : 'post';
        }
        $postTypes[] = 'wpt_test';

        return $postTypes;
    }

    /**
     * @return array
     */
    private function queryHomepageHiddenTestsOnce()
    {
        if (!is_null($this->hiddenIds)) {
            return $this->hiddenIds;
        }

        $hiddenTestsQuery = new WP_Query(array(
            'no_found_rows'         => true,
            'fields'                => 'ids',
            'ignore_sticky_posts'   => true,
            'post_type'             => 'wpt_test',
            'meta_key'              => 'wpt_publish_on_home',
            'meta_value'            => '0',
            'orderby'               => 'none',
            'nopaging'              => true,
        ));
        $this->hiddenIds = $hiddenTestsQuery->posts;

        return $this->hiddenIds;
    }

    /**
     * @param WP_Query $query
     * @param string $variable
     * @return array
     */
    private function getQueryVariableAsArray($query, $variable)
    {
        return array_filter((array)$query->get($variable));
    }

    /**
     * Does current query taxonomy has required object type?
     *
     * @param WP_Query $query
     * @param string $objectType
     * @return boolean
     */
    private function isTaxonomyOf($query, $objectType)
    {
        if (!isset($query->tax_query->queries[0]['taxonomy'])) {
            return false;
        }

        $taxonomy = $this->wp->getTaxonomy($query->tax_query->queries[0]['taxonomy']);
        if (!$taxonomy || !isset($taxonomy->object_type)) {
            return false;
        }

        return in_array($objectType, $taxonomy->object_type);
    }
}
