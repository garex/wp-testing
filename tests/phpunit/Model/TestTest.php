<?php

class TestTest extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        /* @var $db fDatabase */
        $db = fORMDatabase::retrieve('WpTesting_Model_Test', 'write');
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $db->translatedExecute('BEGIN');
    }

    protected function tearDown()
    {
        /* @var $db fDatabase */
        $db = fORMDatabase::retrieve('WpTesting_Model_Test', 'write');
        $db->translatedExecute('ROLLBACK');
    }

    public function testMinimalScaleScore()
    {
        $test = new WpTesting_Model_Test();
        $test->setWp($GLOBALS['wp_facade_mock'])
            ->setTitle('test ' . date(DateTime::ATOM))->setContent('.')->setExcerpt('.')->setContentFiltered('.')
            ->setToPing('http://localhost/')->setPinged('http://localhost/')->setType('wpt_test')
            ->setName('test-' . time());
        $test->store();
        $this->greaterThan($test->getId());

        $scales = fRecordSet::build('WpTesting_Model_Scale', array(
            'name=' => 'Lie',
        ));
        $this->assertNotEmpty($scales);
        /* @var $scale WpTesting_Model_Scale */
        $scale = $scales[0];
        $scaleTaxonomies = $scale->buildWpTesting_Model_Taxonomy();
        $this->assertNotEmpty($scaleTaxonomies);
        /* @var $scaleTaxonomy WpTesting_Model_Taxonomy */
        $scaleTaxonomy = $scaleTaxonomies[0];

        $answers = fRecordSet::build('WpTesting_Model_GlobalAnswer', array(
            'name=' => 'Yes',
        ));
        $this->assertNotEmpty($answers);
        /* @var $scale WpTesting_Model_GlobalAnswer */
        $answer = $answers[0];
        $answerTaxonomies = $answer->buildWpTesting_Model_Taxonomy();
        $this->assertNotEmpty($answerTaxonomies);
        /* @var $answerTaxonomy WpTesting_Model_Taxonomy */
        $answerTaxonomy = $answerTaxonomies[0];

        $test->associateWpTesting_Model_Taxonomies(array($scaleTaxonomy, $answerTaxonomy));

        $question1 = new WpTesting_Model_Question();
        $question1->setTitle('Question 1');
        $question2 = new WpTesting_Model_Question();
        $question2->setTitle('Question 2');
        $test->associateWpTesting_Model_Questions(array($question1, $question2));

        $test->store(true);
        $test->syncQuestionsAnswers();

        foreach ($test->buildQuestions() as $question) { /* @var $question WpTesting_Model_Question */
            foreach ($question->buildAnswers() as $answer) { /* @var $answer WpTesting_Model_Answer */
                $score = new WpTesting_Model_Score();
                $score->setValue(-1)->setScaleId($scale->getId());
                $answer->associateWpTesting_Model_Scores(array($score));
            }
        }
        $test->store(true);

        $scalesWithRange = $test->buildScalesWithRange();
        $this->assertNotEmpty($scalesWithRange);
        /* @var $scaleWithRange WpTesting_Model_Scale */
        $scaleWithRange = $scalesWithRange[0];

        $this->assertEquals(0, $scaleWithRange->getMaximum());
        $this->assertEquals(2, $scaleWithRange->getLength());
        $scaleWithRange->setValue(-2);
    }

}