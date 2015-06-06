<?php

class WpTesting_Widget_PassingTable_Admin extends WpTesting_Widget_PassingTable
{

    /**
     * Do we in Trash now?
     * @var boolean
     */
    private $is_trash;

    public function get_bulk_actions()
    {
        if ($this->is_trash) {
            return array(
                'untrash' => $this->wp->translate('Restore'),
                'delete'  => $this->wp->translate('Delete Permanently'),
            );
        }

        return array(
            'trash' => $this->wp->translate('Move to Trash'),
        );
    }

    public function get_sortable_columns()
    {
        return array(
            'id'            => 'passing_id',
            'test_title'    => 'test_id',
            'user'          => 'respondent_id',
            'passing_device_uuid'   => 'passing_device_uuid',
            'passing_ip'            => 'passing_ip',
            'passing_user_agent'    => 'passing_user_agent',
            'passing_created'       => 'passing_created',
        );
    }

    protected function get_static_columns()
    {
        return array(
            'cb'          => $this->render_tag('input', array('type' => 'checkbox')),
            'id'          => $this->wp->translate('ID'),
            'actions'     => $this->wp->translate('Actions'),
            'test_title'  => __('Test', 'wp-testing'),
            'scales'      => __('Scales', 'wp-testing'),
            'results'     => __('Results', 'wp-testing'),
            'user'        => $this->wp->translate('Username'),
            'passing_device_uuid' => __('Device', 'wp-testing'),
            'passing_ip'          => __('IP address', 'wp-testing'),
            'passing_user_agent'  => __('Browser', 'wp-testing'),
            'passing_created'     => $this->wp->translate('Date'),
        );
    }

    public function prepare_items()
    {
        $this->is_trash = ('trash' == fRequest::get('passing_status', 'string'));

        return parent::prepare_items();
    }

    protected function find_items()
    {
        $params = $this->get_filter_params(array(
            'test_id',
            'passing_created',
            'user',
            'passing_device_uuid',
            'passing_ip',
            'passing_user_agent',
        ));
        $params['passing_status'] = fRequest::get('passing_status', 'array', array('publish'));

        return WpTesting_Query_Passing::create()
            ->findAllPagedSortedByParams($params, $this->get_pagenum(), $this->records_per_page, $this->get_order_by());
    }

    protected function find_tests()
    {
        return WpTesting_Query_Test::create()->findAllPublished(array('post_title' => 'ASC'));
    }

    protected function find_years_months()
    {
        return WpTesting_Query_Passing::create()->queryAllMonths();
    }

    public function column_cb(WpTesting_Model_Passing $item) {
        $label = $this->render_tag('label', array(
            'class' => 'screen-reader-text',
            'for'   => 'cb-select-' . $item->getId(),
        ), sprintf($this->wp->translate('Select %s'), $item->getId()));

        $input = $this->render_tag('input', array(
            'type'  => 'checkbox',
            'name'  => 'passing_id[]',
            'id'    => 'cb-select-' . $item->getId(),
            'value' => $item->getId(),
        ));

        $locked = $this->render_tag('div', array('class' => 'locked-indicator'), '');

        return $label . $input . $locked;
    }

    /**
     * @param WpTesting_Model_Passing $item
     * @param string $column_name
     * @return string
     */
    protected function render_static_column(WpTesting_Model_Passing $item, $column_name)
    {
        switch($column_name) {
            case 'id':
                return $item->getId();

            case 'passing_device_uuid':
                return $item->getDeviceUuid();

            case 'passing_ip':
                return $item->getIp();

            case 'passing_user_agent':
                return $item->getUserAgent();

            case 'test_title':
                $test = $item->createTest();
                return $this->render_link(
                    $this->wp->getEditPostLink($test->getId()),
                    $test->getTitle()
                );

            case 'results':
                $links = array();

                /* @var $result WpTesting_Model_Result */
                foreach ($item->buildResults() as $result) {
                    $links[] = $this->render_link(
                        $this->wp->getEditTermLink($result->getId(), 'wpt_result', 'wpt_test'),
                        $result->getTitle()
                    );
                }

                return implode(', ', $links);


            case 'scales':
                $links = array();

                foreach ($item->buildScalesWithRangeOnce() as $scale) {
                    $link = $this->render_link(
                        $this->wp->getEditTermLink($scale->getId(), 'wpt_scale', 'wpt_test'),
                        $scale->getTitle()
                    );
                    $outOf = ' (' . sprintf(
                            __('%1$d out of %2$d', 'wp-testing'),
                            $scale->getValue(),
                            $scale->getMaximum()) . ')';
                    $links[] = $link . str_replace(' ', '&nbsp;', $outOf);
                }

                return implode(', ', $links);

            case 'user':
                $user = $this->wp->getUserdata($item->getRespondentId());
                if (!$user) {
                    return '';
                }
                $avatar   = $this->wp->getAvatar($user->ID, 32);
                $editLink = $this->wp->getEditUserLink($user->ID);
                return "$avatar <strong><a href=\"$editLink\">$user->user_login</a></strong>";

            case 'actions':
                $actions = array();
                $url     = '?post_type=wpt_test&page=wpt_test_respondents_results&passing_id=' . $item->getId() . '&action=';

                if ($this->is_trash) {
                    $head = $this->render_link(
                        $url . 'untrash',
                        $this->wp->translate('Untrash'),
                        'row-title',
                        array('title' => $this->wp->translate('Restore this item from the Trash'))
                    );
                    $actions[] = $this->render_tag('span', array('class' => 'delete'), $this->render_link(
                        $url . 'delete',
                        $this->wp->translate('Delete Permanently'),
                        'submitdelete',
                        array('title' => $this->wp->translate('Delete this item permanently'))
                    ));
                } else {
                    $head = $this->render_link(
                        $item->getUrl(),
                        $this->wp->translate('View'),
                        'row-title',
                        array('title' => sprintf(
                            html_entity_decode($this->wp->translate('View &#8220;%s&#8221;')),
                            $item->getSlug($this->wp->getSalt())
                        ))
                    );
                    $actions[] = $this->render_tag('span', array('class' => 'trash'), $this->render_link(
                        $url . 'trash',
                        $this->wp->translate('Trash'),
                        'submitdelete',
                        array('title' => $this->wp->translate('Move this item to the Trash'))
                    ));
                }

                return
                    $this->render_tag('strong', array(), $head) .
                    $this->render_tag('div', array('class' => 'row-actions'), implode(' | ', $actions))
                ;
        }

        return parent::render_static_column($item, $column_name);
    }

    public function get_views() {
        $results = WpTesting_Query_Passing::create()->countAllStatuses();
        $views   = array(
            'all' => '',
        );
        $currentStatus  = fRequest::get('passing_status');
        $defaultUrl     = '?post_type=wpt_test&page=wpt_test_respondents_results';
        $total          = 0;
        foreach ($results as $row) {
            $status = $this->wp->getPostStatusObject($row['passing_status']);
            $text   = sprintf($this->wp->translatePlural(
                $status->label_count['singular'],
                $status->label_count['plural'],
                $row['passing_count']
            ), $row['passing_count']);
            $url    = $defaultUrl . '&passing_status=' . $row['passing_status'];
            $class  = ($currentStatus == $row['passing_status']) ? 'current' : '';
            $views[$row['passing_status']] = $this->render_link($url, $text, $class);
            if ($status->show_in_admin_all_list) {
                $total += $row['passing_count'];
            }
        }

        $class = (empty($currentStatus)) ? 'current' : '';
        $views['all'] = $this->render_link($defaultUrl, sprintf($this->wp->translatePluralWithContext(
            'All <span class="count">(%s)</span>',
            'All <span class="count">(%s)</span>',
            $total,
            'posts'
        ), $total), $class);

        return $views;
    }

    protected function render_filter_controls()
    {
        $labels = $this->get_static_columns();
        $currentStatus = fRequest::get('passing_status');
        if ($currentStatus) {
            $currentStatus = $this->render_hidden('passing_status', $currentStatus);
        } else {
            $currentStatus = '';
        }
        return implode(PHP_EOL, array(
            $currentStatus,
        )) . parent::render_filter_controls() . implode(PHP_EOL, array(
            $this->render_search_input('filter_condition[user]',        $labels['user']),
            $this->render_search_input('filter_condition[passing_device_uuid]', $labels['passing_device_uuid']),
            $this->render_search_input('filter_condition[passing_ip]',          $labels['passing_ip']),
            $this->render_search_input('filter_condition[passing_user_agent]',  $labels['passing_user_agent']),
        ));
    }
}
