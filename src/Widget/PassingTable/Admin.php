<?php

class WpTesting_Widget_PassingTable_Admin extends WpTesting_Widget_PassingTable
{

    protected $find_items_filter_params = array(
        'test_id',
        'passing_created',
        'user',
        'passing_device_uuid',
        'passing_ip',
        'passing_user_agent',
    );

    /**
     * Do we in Trash now?
     * @var boolean
     */
    private $is_trash;

    private $bulk_actions = array();

    /**
     * @var WpTesting_Widget_PlaceholderTemplate_Collection
     */
    private $templates;

    public function __construct($args = array())
    {
        parent::__construct($args);

        $this->templates = new WpTesting_Widget_PlaceholderTemplate_Collection();
    }

    public function get_bulk_actions()
    {
        if ($this->is_trash) {
            $this
                ->add_bulk_action('untrash', $this->wp->translate('Restore'))
                ->add_bulk_action('delete',  $this->wp->translate('Delete Permanently'))
            ;
        } else {
            $this
                ->add_bulk_action('trash', $this->wp->translate('Move to Trash'))
            ;
        }
        return $this->bulk_actions;
    }

    public function add_bulk_action($key, $title)
    {
        $this->bulk_actions[$key] = $title;
        return $this;
    }

    public function add_filter_param($key)
    {
        $this->find_items_filter_params[] = $key;
        return $this;
    }

    public function append_actions_templates($template)
    {
        $this->templates->append('actions', $template);
        return $this;
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
        if ($this->items instanceof fRecordSet) {
            return $this;
        }

        $this->is_trash = ('trash' == fRequest::get('passing_status', 'string'));
        $this->init_templates();
        return parent::prepare_items();
    }

    protected function get_find_items_params()
    {
        $params = $this->get_filter_params($this->find_items_filter_params);
        $params['passing_status'] = fRequest::get('passing_status', 'array', array('publish'));
        return $params;
    }

    protected function find_tests()
    {
        return WpTesting_Query_Test::create()->findAllPublished(array('post_title' => 'ASC'));
    }

    protected function find_years_months()
    {
        return WpTesting_Query_Passing::create()->queryAllMonths();
    }

    public function column_cb(WpTesting_Model_Passing $item)
    {
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
     * @return string|integer
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
                    $links[] = $link . str_replace(' ', '&nbsp;', ' (' . $scale->formatValueAsOutOf() . ')');
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
                $values = array(
                    '{{ actionUrl }}' => '?post_type=wpt_test&page=wpt_test_respondents_results&passing_id=' . $item->getId() . '&action=',
                    '{{ itemUrl }}'   => $item->getUrl(),
                    '{{ itemSlug }}'  => $item->getSlug($this->wp->getSalt()),
                );
                $head    = $this->templates->apply('head',     $values);
                $actions = $this->templates->apply('actions',  $values);

                return
                    $this->render_tag('strong', array(), $head) .
                    $this->render_tag('div', array('class' => 'row-actions'), implode(' | ', $actions))
                ;
        }

        return parent::render_static_column($item, $column_name);
    }

    protected function get_test_title_link(WpTesting_Model_Test $test)
    {
        return $this->wp->getEditPostLink($test->getId());
    }

    public function get_views()
    {
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

    private function init_templates()
    {
        if ($this->is_trash) {
            $this->templates
                ->set('head', $this->render_link(
                    '{{ actionUrl }}untrash',
                    $this->wp->translate('Untrash'),
                    'row-title',
                    array('title' => $this->wp->translate('Restore this item from the Trash'))
                ))
                ->append('actions', $this->render_tag('span', array('class' => 'delete'), $this->render_link(
                    '{{ actionUrl }}delete',
                    $this->wp->translate('Delete Permanently'),
                    'submitdelete',
                    array('title' => $this->wp->translate('Delete this item permanently'))
                )))
            ;
        } else {
            $this->templates
                ->set('head', $this->render_link(
                    '{{ itemUrl }}',
                    $this->wp->translate('View'),
                    'row-title',
                    array('title' => sprintf(
                        html_entity_decode($this->wp->translate('View &#8220;%s&#8221;')),
                        '{{ itemSlug }}'
                    ))
                ))
                ->append('actions', $this->render_tag('span', array('class' => 'trash'), $this->render_link(
                    '{{ actionUrl }}trash',
                    $this->wp->translate('Trash'),
                    'submitdelete',
                    array('title' => $this->wp->translate('Move this item to the Trash'))
                )))
            ;
        }

        return $this;
    }
}
