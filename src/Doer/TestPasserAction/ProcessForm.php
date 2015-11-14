<?php

class WpTesting_Doer_TestPasserAction_ProcessForm extends WpTesting_Doer_TestPasserAction
{

    public function beforeRender(WpTesting_Model_Test $test, WpTesting_Model_Passing $passing = null)
    {
        if (is_null($passing)) {
            throw new InvalidArgumentException('Passing must exists on fill form step!');
        }

        $this->test    = $test;
        $this->passing = $passing;
        $passing
            ->setIp($this->getClientIp())
            ->setDeviceUuid($this->extractUuid('device_uuid', $_COOKIE))
            ->setUserAgent($this->getUserAgent())
            ->setRespondentId($this->wp->getCurrentUserId())
        ;

        try {
            $passing->storeAll();
            $link = $passing->getUrl($this->getCurrentUrl());
            return $this->redirectAndDie($link);
        } catch (fValidationException $e) {
            return $this->dieNotValid($e->getMessage());
        }
    }

    public function renderContent($content, $template)
    {
        // nothing here
        return '';
    }

    private function extractUuid($key, $data)
    {
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

    private function redirectAndDie($link)
    {
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
    }

    private function dieNotValid($details)
    {
        return $this->dieMessage('Test/Passer/respondent-message', 400, array(
            'title'   => __('Test data not valid', 'wp-testing'),
            'content' => __('You passed not valid data to test.', 'wp-testing'),
            'details' => $details,
        ));
    }
}