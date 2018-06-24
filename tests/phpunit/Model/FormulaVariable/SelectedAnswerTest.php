<?php

class SelectedAnswerTest extends WpTesting_Tests_TestCase
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

    public function testNoVariablesFromEmptyTest()
    {
        $test = $this->mockTest();
        $variables = WpTesting_Model_FormulaVariable_SelectedAnswer::buildAllFrom($test);
        $this->assertEmpty($variables);
    }

    public function testNoVariablesFromTestWithOnlyQuestions()
    {
        $test = $this->mockTestWithQuestions();
        $variables = WpTesting_Model_FormulaVariable_SelectedAnswer::buildAllFrom($test);
        $this->assertEmpty($variables);
    }

    public function testNoVariablesFromEmptyTestAndPassing()
    {
        $test    = $this->mockTest()->setId(-1);
        $passing = $this->mockPassing(array(), $test);

        $variables = WpTesting_Model_FormulaVariable_SelectedAnswer::buildAllFrom($test, $passing);
        $this->assertEmpty($variables);
    }

    public function testVariablesFromTestSameAsAnswersCountInsideQuestions()
    {
        $test = $this->mockTestWithQuestionsAndAnswers();

        $variables = WpTesting_Model_FormulaVariable_SelectedAnswer::buildAllFrom($test);
        $this->assertCount(5, $variables);

        return $variables;
    }

    /**
     * @depends testVariablesFromTestSameAsAnswersCountInsideQuestions
     * @param WpTesting_Model_FormulaVariable[] $variables
     */
    public function testVariablesFromTestHasSourcesByTheirPositions(array $variables)
    {
        $this->assertEquals(array(
            'question_1_answer_1',
            'question_1_answer_2',
            'question_1_answer_3',
            'question_2_answer_1',
            'question_2_answer_2',
        ), array_keys($variables));
    }

    /**
     * @depends testVariablesFromTestSameAsAnswersCountInsideQuestions
     * @param WpTesting_Model_FormulaVariable[] $variables
     */
    public function testVariablesFromTestAllHasFalseValues(array $variables)
    {
        $this->assertVariablesValuesEquals(array(
            0,
            0,
            0,
            0,
            0,
        ), $variables);
    }

    private function assertVariablesValuesEquals(array $expected, array $variables)
    {
        $values = array();
        foreach ($variables as $variable) {
            $values[] = $variable->getValue();
        }
        $this->assertEquals($expected, $values);
    }

    public function testVariablesFromTestAndPassingSameAsAnswersCountInsideQuestions()
    {
        $test    = $this->mockTestWithQuestionsAndAnswers();
        $passing = $this->mockPassingWithAnswers(array(), $test);

        $variables = WpTesting_Model_FormulaVariable_SelectedAnswer::buildAllFrom($test, $passing);
        $this->assertCount(5, $variables);

        return $variables;
    }

    /**
     * @depends testVariablesFromTestAndPassingSameAsAnswersCountInsideQuestions
     * @param WpTesting_Model_FormulaVariable[] $variables
     */
    public function testVariablesFromTestAndPassingHasSourcesByTheirPositions(array $variables)
    {
        $this->testVariablesFromTestHasSourcesByTheirPositions($variables);
    }

    /**
     * @depends testVariablesFromTestAndPassingSameAsAnswersCountInsideQuestions
     * @param WpTesting_Model_FormulaVariable[] $variables
     */
    public function testVariablesFromTestAndPassingAllHasTrueValuesFromSelectedAnswers(array $variables)
    {
        $this->assertVariablesValuesEquals(array(
            1,
            0,
            0,
            1,
            0,
        ), $variables);
    }

    /**
     * @param array $methods
     * @return WpTesting_Model_Test
     */
    private function mockTest(array $methods = array())
    {
        $class = 'WpTesting_Model_Test_' . substr(md5(microtime()), 0, 6);
        fORM::mapClassToTable($class, fORM::tablize('WpTesting_Model_Test'));
        return $this->getMockBuilder('WpTesting_Model_Test')
            ->setMethods(array_merge(array('__wakeup'), $methods))
            ->setMockClassName($class)
            ->getMock();
    }

    /**
     * @param array $methods
     * @return WpTesting_Model_Test
     */
    private function mockTestWithQuestions(array $methods = array(), array $questions = array())
    {
        $methods[] = 'buildQuestionsWithAnswers';
        $mock = $this->mockTest($methods);

        if (empty($questions)) {
            $questions = array(
                new WpTesting_Model_Question(),
                new WpTesting_Model_Question(),
            );
        }

        $mock->expects($this->any())
            ->method('buildQuestionsWithAnswers')
            ->will($this->returnValue($questions));

        return $mock;
    }

    private function mockTestWithQuestionsAndAnswers(array $methods = array())
    {
        $q1 = new WpTesting_Model_Question();
        $q2 = new WpTesting_Model_Question();
        $answers = array(
            new WpTesting_Model_Answer(),
            new WpTesting_Model_Answer(),
            new WpTesting_Model_Answer(),
            new WpTesting_Model_Answer(),
            new WpTesting_Model_Answer(),
        );
        $q1->associateAnswers(array(
            $answers[0]->setId(1),
            $answers[1]->setId(2),
            $answers[2]->setId(3),
        ));
        $q2->associateAnswers(array(
            $answers[3]->setId(4),
            $answers[4]->setId(5),
        ));
        $questions = array($q1, $q2);

        return $this->mockTestWithQuestions($methods, $questions);
    }

    /**
     * @param array $methods
     * @return WpTesting_Model_Passing
     */
    private function mockPassing(array $methods = array(), WpTesting_Model_Test $test = null)
    {
        $class = 'WpTesting_Model_Passing_' . substr(md5(microtime()), 0, 6);
        fORM::mapClassToTable($class, fORM::tablize('WpTesting_Model_Passing'));
        $mock  = $this->getMockBuilder('WpTesting_Model_Passing')
            ->setMethods(array_merge(array('__wakeup'), $methods))
            ->setMockClassName($class)
            ->getMock();

        if (!is_null($test)) {
            $mock->expects($this->any())
                ->method('createTest')
                ->will($this->returnValue($test));
            $mock->setWp($this->getWpFacade())->setTestId($test->getId());
        }

        return $mock;
    }

    /**
     * @param array $methods
     * @return WpTesting_Model_Passing
     */
    private function mockPassingWithAnswers(array $methods = array(), WpTesting_Model_Test $test = null)
    {
        $methods[] = 'buildAnswers';
        $mock = $this->mockPassing($methods, $test);

        $answers = array();
        foreach ($test->buildQuestionsWithAnswers() as $question) {
            foreach ($question->buildAnswers() as $answer) {
                $answers[] = $answer;
                break;
            }
        }

        $mock->expects($this->any())
            ->method('buildAnswers')
            ->will($this->returnValue(fRecordSet::buildFromArray('WpTesting_Model_Answer', $answers)));

        return $mock;
    }
}
