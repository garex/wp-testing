<?php
class QuestionTest extends WpTesting_Tests_TestCase
{

    /**
     * @var fDatabase
     */
    private $db;

    protected function setUp()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->db = fORMDatabase::retrieve('WpTesting_Model_Question', 'write');
        $this->db->translatedExecute('BEGIN');
    }

    protected function tearDown()
    {
        $this->db && $this->db->translatedExecute('ROLLBACK');
    }

    public function testQuestionSavedInUtfEncoding()
    {
        $question = new WpTesting_Model_Question();
        $question->setTitle('راهنمایی برای ایجاد آزمون های روانشناسی.')->setTestId(1);
        $id = $question->store()->getId();
        $question->clearIdentityMap();

        $question2 = new WpTesting_Model_Question($id);
        $this->assertEquals('راهنمایی برای ایجاد آزمون های روانشناسی.', $question2->getTitle());
    }
}
