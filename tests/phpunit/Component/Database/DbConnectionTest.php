<?php

abstract class DbConnectionTest extends WpTesting_Tests_TestCase
{
    /**
     * @dataProvider dataForConnected
     */
    public function testConnected($dbHost, $skipIfExceptionText = null)
    {
        $wp = self::createWordPressFacade(array('host' => $dbHost));

        try {
            $result = $this->connectToDbAndQueryOne($wp);
        } catch (Exception $e) {
            $this->assertContains($skipIfExceptionText, $e->getMessage());
            $this->markTestSkipped($e->getMessage());
        }

        $this->assertEquals(1, $result);
    }

    /**
     * @return string
     */
    abstract protected function connectToDbAndQueryOne(WpTesting_WordPressFacade $wp);

    public function dataForConnected()
    {
        return array(
            array('localhost'),
            array('127.0.0.1'),
            array(''),
            array('localhost:3306'),
            array('127.0.0.1:3306'),
            array(':3306'),

            array('0000:0000:0000:0000:0000:0000:0000:0001', 'connect'),
            array('::1', 'connect'),
            array('[::1]', 'connect'),

            array(':/var/run/mysqld/mysqld.sock'),
            array('localhost:/var/run/mysqld/mysqld.sock'),
            array('127.0.0.1:3306:/var/run/mysqld/mysqld.sock'),
            array('[::1]:3306:/var/run/mysqld/mysqld.sock'),
        );
    }
}
