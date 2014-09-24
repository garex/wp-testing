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
        $action   = 'fill-form';
        $template = $this->wp->locateTemplate('entry-content-wpt-test-' . $action . '.php');
        $template = ($template) ? $template : 'Test/Passer/' . $action;

        $this->wp
            ->enqueuePluginStyle('wpt_public', 'css/public.css')
            ->enqueuePluginScript('wpt_test_pass_' . $action, 'js/test-pass-' . $action . '.js', array('jquery', 'lodash'), false, true)
        ;

        return $this->render($template, array(
            'content'    => $content,
            'test'       => $this->test,
            'questions'  => $this->test->buildQuestions(),
        ));
    }

}
