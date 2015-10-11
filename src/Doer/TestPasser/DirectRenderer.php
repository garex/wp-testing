<?php

/**
 * Test passer directly by provided Test model.
 */
class WpTesting_Doer_TestPasser_DirectRenderer extends WpTesting_Doer_TestPasser
{

    /**
     * @param WpTesting_Model_Test $test
     * @param WpTesting_Doer_IExclusiveContentFilterer $filterer
     * @return string
     */
    public function renderOutside(WpTesting_Model_Test $test, WpTesting_Doer_IExclusiveContentFilterer $filterer)
    {
        try {
            $this->beforeRender($test);
        } catch (UnexpectedValueException $e) {
            return __('Test is under construction', 'wp-testing');
        }

        $filterer->close();
        $content = $this->wp->applyFilters('the_content', $test->getContent());
        $content = $this->renderTestContent($content);
        $filterer->open();

        return $content;
    }
}
