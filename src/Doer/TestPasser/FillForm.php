<?php

class WpTesting_Doer_TestPasser_FillForm extends WpTesting_Doer_TestPasser_Action
{

    /**
     * Current WordPress title separator
     * @var string
     */
    private $titleSeparator = '-';

    public function beforeRender(WpTesting_Model_Test $test, WpTesting_Model_Passing $passing = null)
    {
        $this->test    = $test;
        $this->passing = $passing;

        if ($this->test->isOneQuestionPerStep()) {
            $stepStrategy  = new WpTesting_Component_StepStrategy_OneToOne($test, $passing->buildAnswers());
        } else {
            $stepStrategy  = new WpTesting_Component_StepStrategy_AllInOne($test, $passing->buildAnswers());
        }

        $stepStrategy  = $this->wp->applyFilters(
            'wp_testing_passer_step_strategy',
            $stepStrategy
        );
        $this->passing->setStepStrategy($stepStrategy);

        $this
            ->addJsData('evercookieBaseurl', $this->wp->getPluginUrl('vendor/samyk/evercookie'))
            ->enqueueScript('test-pass-fill-form', array('jquery', 'pnegri_uuid', 'samyk_evercookie'))
        ;
        $this->wp
            ->addFilter('wp_title', array($this, 'extractTitleSeparator'), 10, 2)
            ->doAction('wp_testing_passer_fill_form_before_render', $this->passing, $this->test)
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
            'questionsAnswered'    => $this->passing->getAnsweredQuestionsCount(),
            'questionsTotal'       => $this->passing->getQuestionsCount(),
        ));

        $step = $this->passing->getCurrentStep();
        $submitButtonCaption = current(array_filter(array(
            $this->wp->getCurrentPostMeta('wpt_test_page_submit_button_caption'),
            __('Get Test Results', 'wp-testing'),
        )));
        if (!$step->isLast()) {
            $submitButtonCaption = __('Next', 'wp-testing');
        }
        $answerIdName = fOrm::tablize('WpTesting_Model_Answer') . '::answer_id';
        $answerIndex  = 0;
        if (isset($_POST[$answerIdName]) && is_array($_POST[$answerIdName])) {
            $answerIndex = max(array_keys($_POST[$answerIdName])) + 1;
        }
        $params = array(
            'wp'           => $this->wp,
            'hiddens'      => $this->generateHiddens($step),
            'answerIdName' => $answerIdName,
            'answerIndex'  => $answerIndex,
            'content'      => $content,
            'test'         => $this->test,
            'questions'    => $step->getQuestions(),
            'isShowContent'=> $step->isFirst(),
            'subTitle'     => $step->getTitle(),
            'isFinal'      => $this->test->isFinal(),
            'isMultipleAnswers'    => $this->test->isMultipleAnswers(),
            'submitButtonCaption'  => $submitButtonCaption,
            'stepsCounter' => $this->passing->getStepsCounter(),
        );

        $this->wp->doAction('wp_testing_passer_fill_form_render_content', $this->passing, $this->test);
        return preg_replace_callback(
            '|<form.+</form>|s',
            array($this, 'stripNewLines'),
            $this->render($template, $params)
        );
    }

    private function generateHiddens(WpTesting_Model_Step $step)
    {
        $hiddens = array();
        $hiddens['passer_action'] = ($step->isLast())
            ? WpTesting_Doer_TestPasser::ACTION_PROCESS_FORM
            : WpTesting_Doer_TestPasser::ACTION_FILL_FORM;
        if (!fRequest::isPost()) {
            return $hiddens;
        }
        unset($_POST['passer_action']);
        foreach ($_POST as $key => $value) {
            if (!is_array($value)) {
                $hiddens[$key] = $value;
                continue;
            }
            foreach($value as $index => $subValue) {
                $hiddens["{$key}[$index]"] = $subValue;
            }
        }
        return $hiddens;
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