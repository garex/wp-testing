<?php

class WpTesting_Widget_PassingTable_User extends WpTesting_Widget_PassingTable
{

    public function add_dynamic_column(WpTesting_Widget_PassingTableColumn $column)
    {
        // Does not allow to add dynamic columns yet
        return $this;
    }

    public function get_sortable_columns()
    {
        return array(
            'row_number'    => 'passing_id',
            'test_title'    => 'test_id',
            'passing_created' => 'passing_created',
        );
    }

    protected function get_static_columns()
    {
        return array(
            'row_number'  => __('#', 'wp-testing'),
            'view'        => $this->wp->translate('View'),
            'test_title'  => __('Test', 'wp-testing'),
            'scales'      => __('Scales', 'wp-testing'),
            'results'     => __('Results', 'wp-testing'),
            'passing_created' => $this->wp->translate('Date'),
        );
    }

    protected function find_items()
    {
        $params = $this->get_filter_params(array(
            'test_id',
        ));
        $params['respondent_id'] = $this->wp->getCurrentUserId();
        return WpTesting_Query_Passing::create()
            ->findAllPagedSortedByParams($params, $this->get_pagenum(), $this->records_per_page, $this->get_order_by());
    }

    protected function find_tests()
    {
        return WpTesting_Query_Test::create()
            ->findAllByPassingRespondent($this->wp->getCurrentUserId(), array('Test.post_title'));
    }

    protected function find_years_months()
    {
        return WpTesting_Query_Passing::create()
            ->queryAllMonthsByRespondent($this->wp->getCurrentUserId());
    }

    /**
     * @param WpTesting_Model_Passing $item
     * @param string $column_name
     * @return string
     */
    protected function render_static_column(WpTesting_Model_Passing $item, $column_name)
    {
        switch($column_name) {
            case 'row_number':
                return $this->get_row_number();

            case 'test_title':
                $test = $item->createTest();
                return $this->render_link(
                    $this->wp->getPostPermalink($test->getId()),
                    $test->getTitle()
                );

            case 'results':
                $links = array();

                /* @var $result WpTesting_Model_Result */
                foreach ($item->buildResults() as $result) {
                    $links[] = $result->getTitle();
                }

                return implode(', ', $links);

            case 'scales':
                $links = array();

                foreach ($item->buildScalesWithRangeOnce() as $scale) {
                    $link    = $scale->getTitle();
                    $links[] = $link . str_replace(' ', '&nbsp;', ' (' . $scale->formatValueAsOutOf() . ')');
                }

                return implode(', ', $links);

            case 'view':
                return $this->render_link($item->getUrl());
        }

        return parent::render_static_column($item, $column_name);
    }
}
