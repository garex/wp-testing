<?php

class WpTesting_Doer_TestPasser extends WpTesting_Doer_AbstractDoer
{

    /**
     * @var WpTesting_Model_Test
     */
    private $test = null;

    public function addContentFilter()
    {
        $object = $this->wp->getQuery()->get_queried_object();
        if ($object instanceof WP_Post && $object->post_type == 'wpt_test') {
            $this->test = new WpTesting_Model_Test($object);
            $this->wp->addFilter('the_content', array($this, 'renderTestContent'));
        }
        return $this;
    }

    public function renderTestContent($content)
    {
        $action   = $this->isPost() ? 'get-results' : 'fill-form';
        $template = $this->wp->locateTemplate('entry-content-wpt-test-' . $action . '.php');
        $template = ($template) ? $template : 'Test/Passer/' . $action;

        if ($this->isPost()) {
            $passing = new WpTesting_Model_Passing();
            $passing->populate($this->test);
            $params = array(
                'content'    => $content,
                'test'       => $this->test,
                'passing'    => $passing,
                'scales'     => $passing->buildScalesWithRangeOnce(),
                'results'    => $passing->buildResults(),
            );
        } else {
            $params = array(
                'content'    => $content,
                'test'       => $this->test,
                'questions'  => $this->test->buildQuestions(),
            );
        }

        $this->wp
            ->enqueuePluginStyle('wpt_public', 'css/public.css')
            ->enqueuePluginScript('wpt_test_pass_' . $action, 'js/test-pass-' . $action . '.js', array('jquery', 'lodash'), false, true)
        ;

        return $this->render($template, $params);
    }

}
