<?php

class WpTesting_Doer_TestPasserAction_GetResults extends WpTesting_Doer_TestPasserAction implements WpTesting_Doer_IRenderer
{

    public function beforeRender(WpTesting_Model_Test $test, WpTesting_Model_Passing $passing = null)
    {
        $this->test = $test;
        try {
            $this->passing = new WpTesting_Model_Passing(
                $this->wp->getQuery()->get('wpt_passing_slug'),
                $this->wp->getSalt()
            );
            $this->passing->setWp($this->wp);
            if (!$this->passing->isViewable()) {
                throw new fNotFoundException();
            }
        } catch (fNotFoundException $e) {
            return $this->dieNotFound();
        }
        $this
            ->enqueueScript('render-text-with-more', array('detect-javascript', 'jquery'))
            ->enqueueScript('test-pass-get-results', array('jquery'))
        ;
        $this->setupScalesDiagram($this->test, $this->passing);
    }

    public function renderContent($content, $template)
    {
        $params  = array(
            'content'               => $content,
            'renderer'              => $this,
            'test'                  => $this->test,
            'passing'               => $this->passing,
            'scales'                => $this->passing->buildScalesWithRangeOnce(),
            'results'               => $this->passing->buildResults(),
            'isShowScalesDiagram'   => $this->test->isShowScalesDiagram(),
            'isShowScales'          => $this->test->isShowScales(),
            'isShowDescription'     => $this->test->isShowTestDescription(),
        );

        return $this->render($template, $params);
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

    private function dieNotFound()
    {
        return $this->dieMessage('Test/Passer/respondent-message', 404, array(
            'title'   => __('Test result not found', 'wp-testing'),
            'content' => __('You can not get anything from nothing.', 'wp-testing'),
        ));
    }

    private function setupScalesDiagram(WpTesting_Model_Test $test, WpTesting_Model_Passing $passing)
    {
        if ($test->isShowScalesDiagram() !== true) {
            return $this;
        }
        $sorryBrowser  = sprintf(__('Sorry but your browser %s is not compatible to display the chart', 'wp-testing'), $this->getUserAgent());
        $scales        = $this->toJson($passing->buildScalesWithRangeOnce());
        return $this
            ->addJsData('warningIncompatibleBrowser', $sorryBrowser)
            ->addJsData('scales', $scales)
            ->enqueueScript('line-diagram', array('jquery', 'raphael-scale', 'raphael-line-diagram'))
        ;
    }
}