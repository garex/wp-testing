<?php

class WpTesting_ShortcodeProcessor
{

    public function getList()
    {
        $tests = WpTesting_Query_Test::create()->findAllPublished();
        return $this->render('Shortcode/list', array(
            'tests' => $tests,
        ));
    }

    protected function render($__template, $__params = array())
    {
        $__template .= '.php';
        extract($__params, EXTR_SKIP);
        ob_start();
        include dirname(__FILE__) . '/Template/' . $__template;
        return ob_get_clean();
    }
}
