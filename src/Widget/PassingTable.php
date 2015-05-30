<?php

abstract class WpTesting_Widget_PassingTable extends WpTesting_Widget_ListTable
{

    public function __construct($args = array())
    {
        parent::__construct(array(
            'singular'  => 'passing',
            'plural'    => 'passings',
        ) + $args);

        $this->set_order_by('passing_id', 'desc');
    }

    /**
     * @param string $which Where this nav outputs? top|bottom
     */
    public function extra_tablenav($which)
    {
        if ('top' != $which) {
            return;
        }

        echo $this->render_tag('div', array('class' => 'alignleft actions filteractions'), implode(PHP_EOL, array(
            $this->render_hidden('post_type', 'wpt_test'),
            $this->render_filter_controls(),
            $this->render_submit($this->wp->translate('Filter'), 'filter_action'),
        )));
    }

    protected function render_filter_controls()
    {
        return implode(PHP_EOL, array(
            $this->render_date_select(),
            $this->render_test_select(),
        ));
    }

    /**
     * @param WpTesting_Model_Passing $item
     * @param string $column_name
     * @return string
     */
    protected function render_static_column(WpTesting_Model_Passing $item, $column_name)
    {
        switch($column_name) {
            case 'passing_created':
                return $item->getCreated();
        }

        return '';
    }

    /**
     * @return WpTesting_Model_Test[]
     */
    abstract protected function find_tests();

    protected function render_test_select()
    {
        $options = array('' => __('All Tests', 'wp-testing'));
        foreach ($this->find_tests() as $test) {
            $options[$test->getId()] = $test->getTitle();
        }
        return $this->render_select('filter_condition[test_id]', $options);
    }

    /**
     * @return fResult [ [ created_year => .., created_month => .. ] ]
     */
    abstract protected function find_years_months();

    protected function render_date_select()
    {
        $options = array('' => $this->wp->translate('All dates'));
        foreach ($this->find_years_months() as $row) {
            $value = $row['created_year'] . $this->wp->zeroise($row['created_month'], 2);
            $title = sprintf(
                $this->wp->translate('%1$s %2$d'),
                $this->wp->getLocale()->get_month($row['created_month']),
                $row['created_year']
            );
            $options[$value] = $title;
        }
        return $this->render_select('filter_condition[passing_created]', $options);
    }
}
