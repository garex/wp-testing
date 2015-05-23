<?php

abstract class WpTesting_Widget_PassingTable extends WP_List_Table
{
    protected $records_per_page = 10;

    /**
     * @var WpTesting_WordPressFacade
     */
    protected $wp = null;

    private $row_number = 0;

    private $order_by = 'passing_id';

    private $order_direction = 'desc';

    /**
     * Dynamic columns, that allows to extend this table
     * @var array key => WpTesting_Widget_PassingTableColumn
     */
    private $dynamic_columns = array();

    public function __construct(WpTesting_WordPressFacade $wp)
    {
        $this->wp = $wp;
        parent::__construct(array(
            'singular'  => 'passing',
            'plural'    => 'passings',
            'ajax'      => false,
        ));
    }

    public function get_table_classes()
    {
        return array('widefat', 'striped', $this->_args['plural']);
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
        $this->_column_headers = array($this->get_columns(), array(), array());

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
            return $this->dynamic_columns[$column_name]->value($item);
        }

        return $this->render_static_column($item, $column_name);
    }

    /**
     * @param WpTesting_Model_Passing $item
     * @param string $column_name
     * @return string
     */
    protected function render_static_column(WpTesting_Model_Passing $item, $column_name)
    {
        switch($column_name) {
            case 'created':
                return $item->getCreated();
        }

        return '-';
    }

    protected function render_link($url, $text = null)
    {
        $text = (is_null($text)) ? $url : $text;
        return sprintf('<a href="%s">%s</a>',
            $url,
            $text
        );
    }

}
