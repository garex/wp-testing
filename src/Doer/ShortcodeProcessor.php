<?php

class WpTesting_Doer_ShortcodeProcessor extends WpTesting_Doer_AbstractDoer
{

    public function getList()
    {
        $tests = WpTesting_Query_Test::create()->findAllPublished();
        return $this->render('Shortcode/list', array(
            'tests' => $tests,
            'wp'    => $this->wp,
        ));
    }
}
