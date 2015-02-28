<?php

class WpTesting_Doer_TestPasser extends WpTesting_Doer_AbstractDoer implements WpTesting_Doer_IRenderer
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
     * Protection for many times calling the_content filter
     * @var string
     */
    private $filteredTestContent = null;

    /**
     * Current WordPress title separator
     * @var string
     */
    private $titleSeparator = '-';

    public function addContentFilter()
    {
        $object        = $this->wp->getQuery()->get_queried_object();
        $isPassingPage = (is_object($object) && !empty($object->post_type) && $object->post_type == 'wpt_test');
        if (!$isPassingPage) {
            return $this;
        }

        $this->test = new WpTesting_Model_Test($object);
        $action     = $this->getTestPassingAction();
        $isDie      = (self::ACTION_FILL_FORM != $action && !$this->test->isFinal());
        if ($isDie) {
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

        $this->wp->addFilter('body_class', array($this, 'addPassingActionCssClass'));
        if (self::ACTION_PROCESS_FORM == $action) {
            $passing = new WpTesting_Model_Passing();
            $passing->populate($this->test)
                ->setIp($this->getClientIp())
                ->setDeviceUuid($this->extractUuid('device_uuid', $_COOKIE))
                ->setUserAgent($this->getUserAgent())
            ;

            try {
                $passing->store(true);
                $link = $passing->getUrl($this->wp, $this->getCurrentUrl());
                $this->wp->redirect($link, 302);
                $this->wp->dieMessage(
                    $this->render('Test/Passer/redirect-message', array(
                        'url' => $link,
                    )),
                    'Redirect',
                    array(
                        'response' => 302,
                    )
                );
                return $this;
            } catch (fValidationException $e) {
                $title   = __('Test data not valid', 'wp-testing');
                $message = __('You passed not valid data to test.', 'wp-testing');
                $this->wp->dieMessage(
                    $this->render('Test/Passer/respondent-message', array(
                        'title'   => $title,
                        'content' => $message,
                        'details' => $e->getMessage(),
                    )),
                    $title,
                    array(
                        'back_link' => true,
                        'response'  => 400,
                    )
                );
            }
        } elseif (self::ACTION_GET_RESULTS == $action) {
            try {
                $this->passing = new WpTesting_Model_Passing(
                    $this->wp->getQuery()->get('wpt_passing_slug'),
                    $this->wp->getSalt()
                );
                if (!$this->passing->getId()) {
                    throw new fNotFoundException();
                }
            } catch (fNotFoundException $e) {
                $this->wp->dieMessage(
                    $this->render('Test/Passer/respondent-message', array(
                        'title'   => __('Test result not found', 'wp-testing'),
                        'content' => __('You can not get anything from nothing.', 'wp-testing'),
                    )),
                    __('Test result not found', 'wp-testing'),
                    array(
                        'back_link' => true,
                        'response' => 404,
                    )
                );
            }
            $this->wp
                ->enqueuePluginScript('wpt_render_text_with_more', 'js/render-text-with-more.js', array('detect-javascript', 'jquery'), false, true)
            ;
            if (1 == $this->wp->getCurrentPostMeta('wpt_result_page_show_scales_diagram')) {
                $isSortByScore = (1 == $this->wp->getCurrentPostMeta('wpt_result_page_sort_scales_by_score'));
                $sorryBrowser  = sprintf(__('Sorry but your browser %s is not compatible to display the chart', 'wp-testing'), $this->getUserAgent());
                $scales        = $this->toJson($this->passing->buildScalesWithRangeOnce($isSortByScore));
                $this
                    ->addJsData('warningIncompatibleBrowser', $sorryBrowser)
                    ->addJsData('scales', $scales)
                ;
                $this->wp
                    ->enqueuePluginScript('wpt_line_diagram', 'js/line-diagram.js', array('jquery', 'raphael-scale', 'raphael-line-diagram'), false, true)
                ;
            }
        } elseif (self::ACTION_FILL_FORM == $action) {
            $this->addJsData('evercookieBaseurl', $this->wp->getPluginUrl('vendor/samyk/evercookie'));
            $this->wp
                ->enqueuePluginScript('pnegri_uuid',      'vendor/pnegri/uuid-js/lib/uuid.js',         array('npm-stub'), false, true)
                ->enqueuePluginScript('samyk_swfobject',  'vendor/samyk/evercookie/js/swfobject-2.2.min.js', array(),     false, true)
                ->enqueuePluginScript('samyk_evercookie', 'vendor/samyk/evercookie/js/evercookie.js',  array(),           false, true)
                ->addFilter('wp_title', array($this, 'extractTitleSeparator'), 10, 2)
            ;
        }

        $this->wp
            ->enqueuePluginStyle('wpt_public', 'css/public.css')
            ->enqueuePluginScript('wpt_test_pass_' . $action, 'js/test-pass-' . $action . '.js', array('jquery', 'lodash'), false, true)
            ->addFilter('the_content', array($this, 'renderTestContent'))
        ;
        return $this;
    }

    public function addPassingActionCssClass($classes)
    {
        $classes[] = 'wpt_test-' . $this->getTestPassingAction();
        return $classes;
    }

    public function extractTitleSeparator($title, $separator)
    {
        if (!empty($separator)) {
            $this->titleSeparator = html_entity_decode($separator);
        }
        return $title;
    }

    public function renderTestContent($content)
    {
        // Protection for calling the_content filter not on current test content
        $isSimilar = 50 > levenshtein(
            $this->prepareToLevenshein($this->test->getContent()),
            $this->prepareToLevenshein($content)
        );
        if (!$isSimilar) {
            return $content;
        }

        // Protection for many times calling the_content filter
        if (!is_null($this->filteredTestContent)) {
            return $this->filteredTestContent;
        }
        $action   = $this->getTestPassingAction();
        $template = $this->wp->locateTemplate('entry-content-wpt-test-' . $action . '.php');
        $template = ($template) ? $template : 'Test/Passer/' . $action;

        if (self::ACTION_FILL_FORM == $action) {
            $params = array(
                'answerIdName' => fOrm::tablize('WpTesting_Model_Answer') . '::answer_id',
                'content'      => $content,
                'test'         => $this->test,
                'questions'    => $this->test->buildQuestions(),
                'isFinal'      => $this->test->isFinal(),
                'isMultipleAnswers'    => (1 == $this->wp->getCurrentPostMeta('wpt_test_page_multiple_answers')),
                'submitButtonCaption'  => current(array_filter(array(
                    $this->wp->getCurrentPostMeta('wpt_test_page_submit_button_caption'),
                    __('Get Test Results', 'wp-testing'),
                ))),
            );
            $this->addJsDataValues(array(
                'isResetAnswersOnBack' => (1 == $this->wp->getCurrentPostMeta('wpt_test_page_reset_answers_on_back')),
                'isShowProgressMeter'  => (1 == $this->wp->getCurrentPostMeta('wpt_test_page_show_progress_meter')),
                'titleSeparator'       => $this->titleSeparator,
                'percentsAnswered'     => __('{percentage}% answered', 'wp-testing'),
            ));
        } elseif (self::ACTION_GET_RESULTS == $action) {
            $isSortByScore = (1 == $this->wp->getCurrentPostMeta('wpt_result_page_sort_scales_by_score'));
            $params  = array(
                'content'    => $content,
                'renderer'   => $this,
                'test'       => $this->test,
                'passing'    => $this->passing,
                'scales'     => $this->passing->buildScalesWithRangeOnce($isSortByScore),
                'results'    => $this->passing->buildResults(),
                'isShowScalesDiagram' => (1 == $this->wp->getCurrentPostMeta('wpt_result_page_show_scales_diagram')),
                'isShowScales'      => (1 == $this->wp->getCurrentPostMeta('wpt_result_page_show_scales')),
                'isShowDescription' => (1 == $this->wp->getCurrentPostMeta('wpt_result_page_show_test_description')),
            );
        }

        $this->filteredTestContent = preg_replace_callback('|<form.+</form>|s', array($this, 'stripNewLines'), $this->render($template, $params));
        return $this->filteredTestContent;
    }

    public function renderTextAsHtml($content)
    {
        $content = preg_replace('|(<\/[^>]+>)\r?\n|', '$1', $content);
        $content = preg_replace('|[\r\n]+(<!--)|',    '$1', $content);
        $content = preg_replace('|(-->)[\r\n]+|',     '$1', $content);
        return nl2br($content);
    }

    public function renderWithMoreSplitted($content)
    {
        $extended = $this->wp->getExtended($content);
        if (empty($extended['extended'])) {
            return $content;
        }
        if (empty($extended['more_text'])) {
            $extended['more_text'] = trim($this->wp->translate('(more&hellip;)'), '()');
        }
        return $this->render('Test/Passer/text-with-more', array(
            'excerpt' => $extended['main'],
            'more'    => $extended['more_text'],
            'content' => $extended['extended'],
        ));
    }

    private function prepareToLevenshein($input)
    {
        return substr(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($input))), 0, 255);
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

    private function getTestPassingAction()
    {
        if ($this->wp->getQuery()->get('wpt_passing_slug')) {
            return self::ACTION_GET_RESULTS;
        }
        if ($this->isPost()) {
            return self::ACTION_PROCESS_FORM;
        }
        return self::ACTION_FILL_FORM;
    }

    private function extractUuid($key, $data) {
        $candidates = array();

        foreach ($data as $candidateKey => $candidateValue) {
            if (!preg_match('/' . preg_quote($key) . '$/', $candidateKey)) {
                continue;
            }
            if (!preg_match('/^[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}$/i', $candidateValue)) {
                continue;
            }
            $candidates[] = strtolower($candidateValue);
        }

        $candidatesCounts = array_count_values($candidates);
        arsort($candidatesCounts);

        return key($candidatesCounts);
    }
}
