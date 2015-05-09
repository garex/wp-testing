<?php

class WpTesting_Widget_PassingTable extends WP_List_Table
{
    protected $records_per_page = 10;

    /**
     * @var WpTesting_WordPressFacade
     */
    protected $wp = null;

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
        $columns = array(
            'actions'     => $this->wp->translate('Actions'),
            'test_title'  => __('Test', 'wp-testing'),
            'scales'      => __('Scales', 'wp-testing'),
            'results'     => __('Results', 'wp-testing'),
            'user'        => $this->wp->translate('Username'),
            'device_uuid' => __('Device', 'wp-testing'),
            'ip'          => __('IP address', 'wp-testing'),
            'user_agent'  => __('Browser', 'wp-testing'),
            'created'     => $this->wp->translate('Date'),
        );
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

    public function prepare_items()
    {
        $this->_column_headers = array($this->get_columns(), array(), array());

        $sort = array('passing_id' => 'desc');

        $this->items = WpTesting_Query_Passing::create()
            ->findAllPagedSorted($this->get_pagenum(), $this->records_per_page, $sort);

        $total = $this->items->count(true);
        $this->set_pagination_args(array(
            'total_items' => $total,
            'per_page'    => $this->records_per_page,
            'total_pages' => ceil($total / $this->records_per_page)
        ));

        return $this;
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

        switch($column_name) {
            case 'id':
                return $item->getId();
            case 'created':
                return $item->getCreated();
            case 'device_uuid':
                return $item->getDeviceUuid();
            case 'ip':
                return $item->getIp();
            case 'user_agent':
                return $item->getUserAgent();

            case 'test_title':
                $test = $item->createTest();
                return $this->renderLink(
                    $this->wp->getEditPostLink($test->getId()),
                    $test->getTitle()
                );

            case 'results':
                $links = array();

                /* @var $result WpTesting_Model_Result */
                foreach ($item->buildResults() as $result) {
                    $links[] = $this->renderLink(
                        $this->wp->getEditTermLink($result->getId(), 'wpt_result', 'wpt_test'),
                        $result->getTitle()
                    );
                }

                return (count($links)) ? implode(', ', $links) : '-';


            case 'scales':
                $links = array();

                foreach ($item->buildScalesWithRangeOnce() as $scale) {
                    $link = $this->renderLink(
                        $this->wp->getEditTermLink($scale->getId(), 'wpt_scale', 'wpt_test'),
                        $scale->getTitle()
                    );
                    $outOf = ' (' . sprintf(
                        __('%1$d out of %2$d', 'wp-testing'),
                        $scale->getValue(),
                        $scale->getMaximum()) . ')';
                    $links[] = $link . str_replace(' ', '&nbsp;', $outOf);
                }

                return (count($links)) ? implode(', ', $links) : '-';

            case 'user':
                $user = $this->wp->getUserdata($item->getRespondentId());
                if (!$user) {
                    return '-';
                }
                $avatar   = $this->wp->getAvatar($user->ID, 32);
                $editLink = $this->wp->getEditUserLink($user->ID);
                return "$avatar <strong><a href=\"$editLink\">$user->user_login</a></strong>";

            case 'actions':
                $actions = array();
                $actions[] = $item->getId() . '.&nbsp;' . $this->renderLink(
                    $item->getUrl(),
                    $this->wp->translate('View')
                );
                return implode(' | ', $actions);
        }

        return '-';
    }

    protected function renderLink($url, $text = null)
    {
        $text = (is_null($text)) ? $url : $text;
        return sprintf('<a href="%s">%s</a>',
            $url,
            $text
        );
    }

}
