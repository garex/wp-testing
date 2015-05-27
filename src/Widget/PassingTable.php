<?php

abstract class WpTesting_Widget_PassingTable extends WP_List_Table
{
    protected $records_per_page = 10;

    /**
     * @var WpTesting_WordPressFacade
     */
    protected $wp = null;

    protected $empty_value = '-';

    private $row_number = 0;

    private $order_by = 'passing_id';

    private $order_direction = 'desc';

    /**
     * Dynamic columns, that allows to extend this table
     * @var array key => WpTesting_Widget_PassingTableColumn
     */
    private $dynamic_columns = array();

    public function __construct($args = array())
    {
        if (empty($args['wp']) || !($args['wp'] instanceof WpTesting_WordPressFacade)) {
            throw new InvalidArgumentException('WordPress facade is required');
        }
        $this->wp = $args['wp'];
        parent::__construct(array(
            'singular'  => 'passing',
            'plural'    => 'passings',
            'ajax'      => false,
        ) + $args);
    }

    public function set_records_per_page($value)
    {
        if ($value > 0 && $value < 1000) {
            $this->records_per_page = intval($value);
        }
        return $this;
    }

    public function set_order_by($field, $direction)
    {
        if (!in_array($field, $this->get_sortable_columns())) {
            return $this;
        }
        if (!in_array($direction, array('asc', 'desc'))) {
            return $this;
        }
        $this->order_by = $field;
        $this->order_direction = $direction;
        return $this;
    }

    public function get_table_classes()
    {
        $classes = parent::get_table_classes();
        $classes = array_combine($classes, $classes);
        unset($classes['fixed']);
        return array_values($classes);
    }

    public function add_dynamic_column(WpTesting_Widget_PassingTableColumn $column)
    {
        $this->dynamic_columns[$column->key()] = $column;
        return $this;
    }

    public function get_columns()
    {
        $columns = $this->get_static_columns();
        foreach ($this->dynamic_columns as $key => $column) { /* @var $column WpTesting_Widget_PassingTableColumn */
            $index   = array_search($column->placeAfter(), array_keys($columns)) + 1;
            $columns =
                array_slice($columns, 0, $index, true) +
                array($key => $column->title()) +
                array_slice($columns, $index, count($columns) - 1, true)
            ;
        }
        return $columns;
    }

    /**
     * @see WpTesting_Widget_PassingTable::get_columns
     * @return array
     */
    abstract protected function get_static_columns();

    public function prepare_items()
    {
        $this->items = $this->find_items();

        $total = $this->items->count(true);
        $this->set_pagination_args(array(
            'total_items' => $total,
            'per_page'    => $this->records_per_page,
            'total_pages' => ceil($total / $this->records_per_page)
        ));

        $this->row_number = ($this->get_pagenum()-1) * $this->records_per_page;
        if ($this->is_order_desc()) {
            $this->row_number = ($total + 1) - $this->row_number;
        }

        return $this;
    }

    public function has_items() {
        if ($this->items instanceof fRecordSet) {
            return (count($this->items) > 0);
        }

        return parent::has_items();
    }

    protected function get_row_number()
    {
        return $this->row_number;
    }

    protected function get_order_by()
    {
        return array(
            $this->order_by => $this->order_direction,
        );
    }

    protected function is_order_desc()
    {
        return ($this->order_direction == 'desc');
    }

    /**
     * @return fRecordSet
     */
    abstract protected function find_items();

    protected function get_filter_params($allowedKeys)
    {
        $params = array();
        foreach (fRequest::get('filter_condition', 'array') as $key => $value) {
            if (!in_array($key, $allowedKeys) || empty($value)) {
                continue;
            }
            $params[$key] = $value;
        }
        return $params;
    }

    public function single_row($item) {
        $this->row_number += $this->is_order_desc() ? -1 : +1;
        parent::single_row($item);
    }

    /**
     * @param WpTesting_Model_Passing $item
     * @param string $column_name
     * @return string
     */
    public function column_default($item, $column_name)
    {
        $item->setWp($this->wp);

        if (isset($this->dynamic_columns[$column_name])) {
            $value = $this->dynamic_columns[$column_name]->value($item);
        } else {
            $value = $this->render_static_column($item, $column_name);
        }

        return ($value === '' || is_null($value)) ? $this->empty_value : $value;
    }

    protected function display_tablenav($which) {
        if ('top' == $which) {
            $_SERVER['REQUEST_URI'] = $this->wp->removeQueryArgument('_wp_http_referer', $_SERVER['REQUEST_URI']);
        }
        parent::display_tablenav($which);
    }

    /**
     * @param string $which Where this nav outputs? top|bottom
     */
    public function extra_tablenav($which) {
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

    protected function render_link($url, $text = null)
    {
        $text = (is_null($text)) ? $url : $text;
        return $this->render_tag('a', array('href' => $url), $text);
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

    protected function render_search_input($name, $label = '')
    {
        $value = fRequest::get($name, 'string');
        $id    = trim(preg_replace('/[^a-z\d]+/', '-', $name), '-');
        return $this->render_tag('label', array(
            'for'   => $id,
            'class' => 'search-input ' . ($value === '' ? 'no-value' : 'has-value'),
        ), $this->render_tag('input', array(
            'type'        => 'search',
            'value'       => $value,
            'name'        => $name,
            'id'          => $id,
            'placeholder' => ($value === '' ? $label : ''),
            'title'       => $label,
        )) . $this->render_tag('span', array(), $label));
    }

    protected function render_submit($label, $name = false, $id = false)
    {
        return $this->render_tag('input', array(
            'type'  => 'submit',
            'class' => 'button',
            'value' => $label,
            'name'  => $name,
            'id'    => $id,
        ));
    }

    private function render_select($name, $options)
    {
        $selectedValue = fRequest::get($name);
        $optionsHtml = '';
        foreach ($options as $value => $text) {
            $optionAttributes = array('value' => $value);
            if ($value == $selectedValue) {
                $optionAttributes['selected'] = 'selected';
            }
            $optionsHtml .= $this->render_tag('option', $optionAttributes, $text);
        }
        return $this->render_tag('select', array(
            'name' => $name,
        ), $optionsHtml);
    }

    private function render_hiddens($values)
    {
        $result = '';
        foreach ($values as $name => $value) {
            $result .= $this->render_hidden($name, $value) . PHP_EOL;
        }
        return $result;
    }

    private function render_hidden($name, $value)
    {
        return $this->render_tag('input', array(
            'type'  => 'hidden',
            'name'  => $name,
            'value' => $value,
        ));
    }

    /**
     * Renders HTML tag with attributes escaped
     *
     * @param string $tag
     * @param array $attributes [Attribute name => Unescaped value]
     * @param string $innerHtml false|string When false, tag will be without any inner HTML. When empty it will be with start and end tags
     * @return string
     */
    private function render_tag($tag, $attributes = array(), $innerHtml = false)
    {
        $attributesHtml = '';
        foreach ($attributes as $name => $value) {
            if (false === $value) {
                continue;
            }
            $attributesHtml .= sprintf(' %s="%s"', $name, htmlspecialchars($value));
        }

        if (false === $innerHtml) {
            return sprintf('<%s%s/>', $tag, $attributesHtml);
        }

        return sprintf('<%s%s>%s</%s>', $tag, $attributesHtml, $innerHtml, $tag);
    }
}
