<?php

class WpTesting_Doer_TestPasser extends WpTesting_Doer_AbstractDoer
{

    /**
     * Initially we show to respondent form with test description, questions and answers
     */
    const ACTION_FILL_FORM = 'fill-form';

    /**
     * After form filled and button clicked, we show results page with scales
     */
    const ACTION_GET_RESULTS = 'get-results';

    /**
     * @var WpTesting_Model_Test
     */
    private $test = null;

    public function addContentFilter()
    {
        $object        = $this->wp->getQuery()->get_queried_object();
        $isPassingPage = (is_object($object) && !empty($object->post_type) && $object->post_type == 'wpt_test');
        if (!$isPassingPage) {
            return $this;
        }
        $this->test = new WpTesting_Model_Test($object);
        $action     = $this->getTestPassingAction();
        $this->wp
            ->enqueuePluginStyle('wpt_public', 'css/public.css')
            ->enqueuePluginScript('wpt_test_pass_' . $action, 'js/test-pass-' . $action . '.js', array('jquery', 'lodash'), false, true)
            ->addFilter('the_content', array($this, 'renderTestContent'))
        ;
        return $this;
    }

    public function renderTestContent($content)
    {
        $action   = $this->getTestPassingAction();
        $template = $this->wp->locateTemplate('entry-content-wpt-test-' . $action . '.php');
        $template = ($template) ? $template : 'Test/Passer/' . $action;

        if (self::ACTION_FILL_FORM == $action) {
            $params = array(
                'content'    => $content,
                'test'       => $this->test,
                'questions'  => $this->test->buildQuestions(),
                'isFinal'    => $this->test->isFinal(),
            );
        } elseif (self::ACTION_GET_RESULTS == $action) {
            $passing = new WpTesting_Model_Passing();
            $passing->populate($this->test);
            $params = array(
                'content'    => $content,
                'test'       => $this->test,
                'passing'    => $passing,
                'scales'     => $passing->buildScalesWithRangeOnce(),
                'results'    => $passing->buildResults(),
            );
        }

        return preg_replace_callback('|<form.+</form>|s', array($this, 'stripNewLines'), $this->render($template, $params));
    }

    private function stripNewLines($matches)
    {
        return str_replace('> <', '><', preg_replace('/[\n\r\s]+/s', ' ', $matches[0]));
    }

    private function getTestPassingAction()
    {
        return $this->isPost() ? self::ACTION_GET_RESULTS : self::ACTION_FILL_FORM;
    }
}
