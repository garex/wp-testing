<?php

class WpTesting_ShortcodeProcessor extends WpTesting_Doer
{

    public function getList()
    {
        $tests = WpTesting_Query_Test::create()->findAllPublished();
        return $this->render('Shortcode/list', array(
            'tests' => $tests,
        ));
    }
}
