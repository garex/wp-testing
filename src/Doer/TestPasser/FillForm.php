<?php

class WpTesting_Doer_TestPasser_FillForm extends WpTesting_Doer_TestPasser_Action
{

    /**
     * Current WordPress title separator
     * @var string
     */
    private $titleSeparator = '-';

    public function beforeRender(WpTesting_Model_Test $test)
    {
        $this->test = $test;
        $this
            ->addJsData('evercookieBaseurl', $this->wp->getPluginUrl('vendor/samyk/evercookie'))
            ->enqueueScript('test-pass-fill-form', array('jquery', 'pnegri_uuid', 'samyk_evercookie'))
        ;
        $this->wp
            ->addFilter('wp_title', array($this, 'extractTitleSeparator'), 10, 2)
        ;
    }

    public function extractTitleSeparator($title, $separator)
    {
        if (!empty($separator)) {
            $this->titleSeparator = html_entity_decode($separator);
        }
        return $title;
    }

    public function renderContent($content, $template)
    {
        $this->addJsDataValues(array(
            'isResetAnswersOnBack' => $this->test->isResetAnswersOnBack(),
            'isShowProgressMeter'  => $this->test->isShowProgressMeter(),
            'titleSeparator'       => $this->titleSeparator,
            'percentsAnswered'     => __('{percentage}% answered', 'wp-testing'),
        ));

        $params = array(
            'wp'           => $this->wp,
            'answerIdName' => fOrm::tablize('WpTesting_Model_Answer') . '::answer_id',
            'content'      => $content,
            'test'         => $this->test,
            'questions'    => $this->test->buildQuestions(),
            'isFinal'      => $this->test->isFinal(),
            'isMultipleAnswers'    => $this->test->isMultipleAnswers(),
            'submitButtonCaption'  => current(array_filter(array(
                $this->wp->getCurrentPostMeta('wpt_test_page_submit_button_caption'),
                __('Get Test Results', 'wp-testing'),
            ))),
        );

        return preg_replace_callback(
            '|<form.+</form>|s',
            array($this, 'stripNewLines'),
            $this->render($template, $params)
        );
    }

    private function stripNewLines($matches)
    {
        $result = $matches[0];
        $result = preg_replace('/[\n\r\s]+/s', ' ', $result);
        $result = str_replace('> <', '><', $result);
        $result = preg_replace('/(>) ([^<])/s', '$1$2', $result);
        $result = preg_replace('|([^>]) (</)|s', '$1$2', $result);
        return $result;
    }
}