<?php

class WpTesting_Doer_TestPasser extends WpTesting_Doer_AbstractDoer
{

    /**
     * Initially we show to respondent form with test description, questions and answers
     */
    const ACTION_FILL_FORM = 'fill-form';

    /**
     * After form filled and button clicked, we process it and redirect to passing result
     */
    const ACTION_PROCESS_FORM = 'process-form';

    /**
     * After form processed and redirected, we show results page with scales of concrete passing
     */
    const ACTION_GET_RESULTS = 'get-results';

    /**
     * @var WpTesting_Model_Test
     */
    private $test = null;

    /**
     * @var WpTesting_Model_Passing
     */
    private $passing = null;

    /**
     * @var WpTesting_Doer_TestPasser_Action
     */
    private $actionProcessor = null;

    /**
     * Protection for many times calling the_content filter
     * @var string
     */
    private $filteredTestContent = null;

    /**
     * @var boolean
     */
    private $canRenderOnFilter = null;

    public function addContentFilter()
    {
        if (!$this->isPostType('wpt_test')) {
            return $this;
        }

        try {
            $this->beforeRender($this->createTest($this->wp->getQuery()->get_queried_object()));
        } catch (UnexpectedValueException $e) {
            return $this->dieUnderConctruction();
        }

        $this->wp->addFilter('the_content', array($this, 'renderOnFilter'), 5);
        $this->canRenderOnFilter = true;
        return $this;
    }

    /**
     * @param WpTesting_Model_Test $test
     * @throws UnexpectedValueException
     * @return self
     */
    private function beforeRender(WpTesting_Model_Test $test)
    {
        $this->test = $test;
        $action     = $this->getTestPassingAction();
        $isLive     = (self::ACTION_FILL_FORM == $action || $this->test->isFinal());
        if (!$isLive) {
            throw new UnexpectedValueException(sprintf('Test %d is under construction', $test->getId()));
            return __('Test is under construction', 'wp-testing');
        }

        $this->registerScripts()->wp->addFilter('body_class', array($this, 'addPassingActionCssClass'));
        $this->createActionProcessor($action)->beforeRender($this->test, $this->passing);
        $this->enqueueStyle('public');
        return $this;
    }

    public function renderOutside(WpTesting_Model_Test $test)
    {
        try {
            $this->beforeRender($test);
        } catch (UnexpectedValueException $e) {
            return __('Test is under construction', 'wp-testing');
        }

        $hasFilter = ($this->canRenderOnFilter === true);
        if ($hasFilter) {
            $this->canRenderOnFilter = false;
        }

        $content = $this->wp->applyFilters('the_content', $test->getContent());
        if ($hasFilter) {
            $this->canRenderOnFilter = true;
        }
        $content = $this->renderTestContent($content);

        return $content;
    }

    public function addPassingActionCssClass($classes)
    {
        $classes[] = 'wpt_test-' . $this->getTestPassingAction();
        return $classes;
    }

    public function renderOnFilter($content)
    {
        if ($this->canRenderOnFilter !== true) {
            return $content;
        }

        // Protection for calling the_content filter not on current test content
        $testContent = $this->test->getContent();
        $isSimilar = empty($testContent) || 50 > levenshtein(
            $this->prepareToLevenshein($testContent),
            $this->prepareToLevenshein($content)
        );
        if (!$isSimilar) {
            return $content;
        }

        // Protection for many times calling the_content filter
        if (!is_null($this->filteredTestContent)) {
            return $this->filteredTestContent;
        }

        $this->filteredTestContent = $renderedContent = $this->renderTestContent($content);

        // Not cache for content, that is cleared of shortcodes
        $isShortcodesCleared = ($this->hasShortcodes($testContent) && !$this->hasShortcodes($content));
        if ($isShortcodesCleared) {
            $this->filteredTestContent = null;
        }

        return $renderedContent;
    }

    public function renderTestContent($content) {
        $hasFilter = ($this->canRenderOnFilter === true);
        if ($hasFilter) {
            $this->canRenderOnFilter = false;
        }

        $action   = $this->getTestPassingAction();
        $template = $this->wp->locateTemplate('entry-content-wpt-test-' . $action . '.php');
        $template = ($template) ? $template : 'Test/Passer/' . $action;

        $this->wp->doAction('wp_testing_passer_render_content',             $this->test);
        $this->wp->doAction('wp_testing_passer_render_content_' . $action,  $this->test);

        $content = $this->createActionProcessor($action)->renderContent($content, $template);

        if ($hasFilter) {
            $this->canRenderOnFilter = true;
        }
        return $content;
    }

    private function dieUnderConctruction()
    {
        $this->wp->dieMessage(
            $this->render('Test/Passer/respondent-message', array(
                'title'   => __('Test is under construction', 'wp-testing'),
                'content' => __('You can not get any results from it yet.', 'wp-testing'),
            )),
            __('Test is under construction', 'wp-testing'),
            array(
                'back_link' => true,
                'response' => 403,
            )
        );
        return $this;
    }

    private function prepareToLevenshein($input)
    {
        $levensteinMax = 255;
        $input = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $input);
        return substr(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($input))), 0, $levensteinMax);
    }

    /**
     * @param string $text
     * @return boolean
     */
    private function hasShortcodes($text)
    {
        return (strstr($text, '[') !== false);
    }

    private function getTestPassingAction()
    {
        if ($this->wp->getQuery()->get('wpt_passing_slug')) {
            return self::ACTION_GET_RESULTS;
        }
        $this->passing = new WpTesting_Model_Passing();
        $this->passing->setWp($this->wp);
        if ($this->isPost()) {
            $this->passing->populateFromTest($this->test);
            if (self::ACTION_PROCESS_FORM == $this->getRequestValue('passer_action')) {
                return self::ACTION_PROCESS_FORM;
            }
        }
        return self::ACTION_FILL_FORM;
    }

    /**
     * @param string $action
     * @return WpTesting_Doer_TestPasser_Action
     */
    private function createActionProcessor($action)
    {
        if (is_null($this->actionProcessor)) {
            switch ($action) {
                case self::ACTION_FILL_FORM:
                    $this->actionProcessor = new WpTesting_Doer_TestPasser_FillForm($this->wp);
                    break;
                case self::ACTION_PROCESS_FORM:
                    $this->actionProcessor = new WpTesting_Doer_TestPasser_ProcessForm($this->wp);
                    break;
                case self::ACTION_GET_RESULTS:
                    $this->actionProcessor = new WpTesting_Doer_TestPasser_GetResults($this->wp);
                    break;
            }
        }
        return $this->actionProcessor;
    }
}
