<?php

class WpTesting_Widget_PassingTable extends WP_List_Table
{
    protected $records_per_page = 10;

    /**
     * @var WpTesting_WordPressFacade
     */
    protected $wp = null;

    public function __construct(WpTesting_WordPressFacade $wp)
    {
        $this->wp = $wp;
        parent::__construct(array(
            'singular'  => 'passing',
            'plural'    => 'passings',
            'ajax'      => false,
        ));
    }

    public function get_columns()
    {
        return array(
            'test_title'  => __('Test', 'wp-testing'),
            'results'     => __('Results', 'wp-testing'),
            'scales'      => __('Scales', 'wp-testing'),
            'created'     => $this->wp->translate('Date'),
            'actions'     => $this->wp->translate('Actions'),
            'device_uuid' => __('Device', 'wp-testing'),
            'ip'          => __('IP address', 'wp-testing'),
            'user_agent'  => __('Browser', 'wp-testing'),
        );
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
     * @return strnig
     */
    public function column_default($item, $column_name)
    {
        $item->setWp($this->wp);
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


            case 'actions':
                $actions = array();
                $actions[] = $this->renderLink(
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
