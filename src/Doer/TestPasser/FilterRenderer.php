<?php

/**
 * Test passer through content filter.
 */
class WpTesting_Doer_TestPasser_FilterRenderer extends WpTesting_Doer_TestPasser implements WpTesting_Doer_IExclusiveContentFilterer
{

    /**
     * Protection for many times calling the_content filter
     * @var string
     */
    private $filteredTestContent = null;

    /**
     * @var boolean
     */
    private $canRenderOnFilter = true;

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

        $this->wp->addFilter('the_content', array($this, 'renderOnFilter'), WpTesting_WordPress_IPriority::PRIORITY_HIGH);
        return $this;
    }

    /**
     * @param string $content
     * @return string
     */
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

        $this->close();
        $this->filteredTestContent = $renderedContent = $this->renderTestContent($content);
        $this->open();

        // Not cache for content, that is cleared of shortcodes
        $isShortcodesCleared = ($this->hasShortcodes($testContent) && !$this->hasShortcodes($content));
        if ($isShortcodesCleared) {
            $this->filteredTestContent = null;
        }

        return $renderedContent;
    }

    public function open()
    {
        $this->canRenderOnFilter = true;
        return $this;
    }

    public function close()
    {
        $this->canRenderOnFilter = false;
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

    private function dieUnderConctruction()
    {
        return $this->dieMessage('Test/Passer/respondent-message', 403, array(
            'title'   => __('Test is under construction', 'wp-testing'),
            'content' => __('You can not get any results from it yet.', 'wp-testing'),
        ));
    }
}
