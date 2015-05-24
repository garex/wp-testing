<?php

class WpTesting_Widget_PassingTable_Admin extends WpTesting_Widget_PassingTable
{

    protected function get_sortable_columns() {
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

    protected function find_items()
    {
        return WpTesting_Query_Passing::create()
            ->findAllPagedSorted($this->get_pagenum(), $this->records_per_page, $this->get_order_by());
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
                $actions[] = $this->render_link(
                    $item->getUrl(),
                    $this->wp->translate('View')
                );
                return implode(' | ', $actions);
        }

        return parent::render_static_column($item, $column_name);
    }
}
