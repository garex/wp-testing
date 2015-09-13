<?php

class WpTesting_Model_Shortcode_Tests extends WpTesting_Model_Shortcode
{

    const NAME = 'wpt_tests';

    protected $cssClass;
    protected $orderBy;
    protected $reverseBy;
    protected $limit;
    protected $listStyle;
    protected $testIds;

    public function getDataForTemplate(WpTesting_Facade_IORM $ormAware)
    {
        $ormAware->setupORM();

        $order = array();
        if ($this->orderBy) {
            $order = array($this->orderBy => 'ASC');
        } elseif ($this->reverseBy) {
            $order = array($this->reverseBy => 'DESC');
        }

        if (empty($this->testIds)) {
            $tests = WpTesting_Query_Test::create()->findAllPublished($order, $this->limit);
        } else {
            $ids   = array_filter(explode(',', $this->testIds));
            $tests = WpTesting_Query_Test::create()->findAllByIds($ids);
        }

        return array(
            'cssClasses' => $this->cssClass,
            'tests'      => $tests,
            'listStyle'  => $this->listStyle,
            'wp'         => $this->getWp(),
        );
    }

    /**
     * @return WpTesting_Model_Shortcode_Attribute[]
     */
    protected function initAttributes()
    {
        $attributes = array(
            $class   = new WpTesting_Model_Shortcode_Attribute('class',    'cssClass'),
            $sort    = new WpTesting_Model_Shortcode_Attribute('sort',     'orderBy'),
            $reverse = new WpTesting_Model_Shortcode_Attribute('reverse',  'reverseBy'),
            $max     = new WpTesting_Model_Shortcode_Attribute('max',      'limit'),
            $id      = new WpTesting_Model_Shortcode_Attribute('id',       'testIds'),
            $list    = new WpTesting_Model_Shortcode_Attribute('list',     'listStyle', 'decimal'),
        );

        $orderColumns = array(
            'id'        => 'ID',
            'title'     => 'post_title',
            'created'   => 'post_date',
            'modified'  => 'post_modified',
            'status'    => 'post_status',
            'name'      => 'post_name',
            'comments'  => 'comment_count',
        );
        $sort->allowOnlyList($orderColumns);
        $reverse->allowOnlyList($orderColumns);

        $max->allowOnlyMask('/^[0-9]+$/')->guideOnError('Use only numbers');
        $id->allowOnlyMask('/^[0-9,]+$/')->guideOnError('Use only numbers and commas');

        $listStyle = array(
            'disc', 'circle', 'square', 'decimal', 'decimal-leading-zero',
            'lower-roman', 'upper-roman', 'lower-greek', 'lower-latin', 'upper-latin',
            'armenian', 'georgian', 'lower-alpha', 'upper-alpha', 'none',
        );
        $list->allowOnlyList(array_combine($listStyle, $listStyle));
        $list->guideOnError('See http://www.w3.org/wiki/CSS/Properties/list-style-type for examples');

        return $attributes;
    }
}
