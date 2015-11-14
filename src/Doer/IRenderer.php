<?php

interface WpTesting_Doer_IRenderer
{
    /**
     * Add HTML tags to text. For example new lines as br tags.
     * @param string $content
     * @return string
     */
    public function renderTextAsHtml($content);

    /**
     * Break content by more tag into excerpt and "more" part that hides under "more" link
     * @param string $content
     * @return string
     */
    public function renderWithMoreSplitted($content);
}
