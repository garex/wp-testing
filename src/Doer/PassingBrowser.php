<?php

abstract class WpTesting_Doer_PassingBrowser extends WpTesting_Doer_AbstractDoer
{

    protected $screenHook = '';

    protected $passingTableClass = '';

    protected $passingsPageTitle = '';

    /**
     * @var WpTesting_Widget_PassingTable
     */
    private $passingTable = null;

    public function registerPages()
    {
        $this->addMenuPages();
        $this->wp
            ->addAction('load-' . $this->screenHook, array($this, 'loadPassingsPage'))
            ->addFilter('manage_' . $this->screenHook . '_columns', array($this, 'managePassingsPageColumns'))
        ;
        $this->enqueueStyle('admin');
        return $this;
    }

    /**
     * @return WpTesting_Doer_PassingBrowser
     */
    abstract protected function addMenuPages();

    /**
     * Process action on page load
     */
    public function loadPassingsPage()
    {
        $table = $this->createPassingTableOnce();
        if (!fRequest::check('passing_id')) {
            return;
        }
        $this->processAction($table->current_action(), fRequest::get('passing_id', 'array'));
    }

    public function managePassingsPageColumns($columns)
    {
        $this->wp
            ->addScreenOption('per_page', array(
                'label'     => $this->wp->translate('Number of items per page:'),
                'default'   => 10,
                'option'    => 'passing_browser_per_page',
            ))
            ->addFilter('set-screen-option', array($this, 'validatePerPageOption'), WpTesting_Addon_IWordPressFacade::PRIORITY_DEFAULT, 3)
            ->setScreenOptions()
        ;

        return $columns;
    }

    protected function processAction($action, $ids)
    {
        return $this;
    }

    public function validatePerPageOption($defaultFalse, $option, $value)
    {
        if ('passing_browser_per_page' == $option) {
            $value = intval($value);
            if ($value >= 1 && $value < 1000) {
                return $value;
            }
        }
    }

    /**
     * @return WpTesting_Widget_PassingTable
     */
    protected function createPassingTableOnce()
    {
        if (!is_null($this->passingTable)) {
            return $this->passingTable;
        }
        $this->passingTable = new $this->passingTableClass(array(
            'wp'    => $this->wp,
            'screen'=> $this->screenHook,
        ));
        $this->passingTable
            ->set_records_per_page($this->getCurrentUserMeta('passing_browser_per_page'))
            ->set_order_by($this->getRequestValue('orderby'), $this->getRequestValue('order'))
        ;
        $this->wp->doAction('wp_testing_passing_browser_create_table', $this->passingTable);
        return $this->passingTable;
    }

    public function renderPassingsPage()
    {
        $table = $this->createPassingTableOnce();
        $table->prepare_items();

        $this->output('Passing/Browser/view-all', array(
            'page'  => $this->getRequestValue('page'),
            'title' => $this->passingsPageTitle,
            'table' => $table,
            'isSemanticHeaders' => $this->isWordPressAlready('4.3'),
        ));
    }
}
