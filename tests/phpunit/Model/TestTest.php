<?php

class TestTest extends WpTesting_Tests_TestCase
{

    /**
     * @var fDatabase
     */
    private $db;

    protected function setUp()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->db = fORMDatabase::retrieve('WpTesting_Model_Test', 'write');
        $this->db->translatedExecute('BEGIN');
    }

    protected function tearDown()
    {
        $this->db && $this->db->translatedExecute('ROLLBACK');
    }

    public function testTestCanBeCreatedAndStored()
    {
        $test = $this->createTest()->store();
        $this->greaterThan($test->getId());
    }

    public function testAddQuestion()
    {
        $test = $this->createTest()->store();
        $test->addQuestion('Question 1');
        $test->addQuestion('Question 2');
        $test->store(true);

        $test2 = new WpTesting_Model_Test($test->getId());
        $this->assertCount(2, $test2->buildQuestions());
    }

    public function testMinimalScaleScore()
    {
        $test   = $this->createTest()->store();
        $scale  = WpTesting_Query_Scale::create()->findByName('Lie');
        $answer = WpTesting_Query_GlobalAnswer::create()->findByName('Yes');
        $test
            ->associateScale($scale)->associateGlobalAnswer($answer)
            ->addQuestion('Question 1')->addQuestion('Question 2')
            ->store(true)->syncQuestionsAnswers()
        ;

        foreach ($test->buildQuestions() as $question) { /* @var $question WpTesting_Model_Question */
            foreach ($question->buildAnswers() as $answer) { /* @var $answer WpTesting_Model_Answer */
                $answer->getScoreByScale($scale)->setValue(-1);
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


    public function testQuestionsWithAnswersNotOverwritesExistingValues()
    {
        /* @var $test WpTesting_Model_Test */
        $test = $this->createTest()->store();
        $test
            ->addQuestion('Question 1')
            ->addQuestion('Question 2')
        ;
        $test->store(true);
        foreach ($test->buildQuestions() as $question) { /* @var $question WpTesting_Model_Question */
            for ($i = 2; $i; --$i) {
                $answer = new WpTesting_Model_Answer();
                $answer->setQuestionId($question->getId());
                $question->associateAnswers($question->buildAnswers()->merge($answer));
            }
            $question->store();
        }

        $test2 = new WpTesting_Model_Test($test->getId());
        $questions2 = $test2->buildQuestionsWithAnswers();
        $this->assertCount(2, $questions2);
        $this->assertCount(2, $questions2[0]->buildAnswers());

        $answer3 = new WpTesting_Model_Answer();
        $answer3->setQuestionId($questions2[0]->getId());
        $questions2[0]->associateAnswers($questions2[0]->buildAnswers()->merge($answer3));
        $this->assertCount(3, $questions2[0]->buildAnswers());

        $questions3 = $test2->buildQuestionsWithAnswers();
        $this->assertCount(3, $questions3[0]->buildAnswers());
    }

    private function createTest()
    {
        $test = new WpTesting_Model_Test();
        return $test
            ->setWp($this->getWpFacade())
            ->setTitle('Test ' . date(DateTime::ATOM))
            ->setContent('Content')
            ->setExcerpt('Excerpt')
            ->setContentFiltered('Content')
            ->setToPing('http://localhost/')
            ->setPinged('http://localhost/')
            ->setType('wpt_test')
            ->setName('test-' . time());
    }
}