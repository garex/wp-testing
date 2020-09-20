<?php

abstract class DbConnectionTest extends WpTesting_Tests_TestCase
{
    /**
     * @dataProvider dataForConnected
     */
    public function testConnected($dbHost, $skipIfExceptionText = null)
    {
        $wp = self::createWordPressFacade(array('host:port' => $dbHost));

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
        $dbIp = explode(' ', trim(shell_exec('getent hosts db')));
        $dbIp = reset($dbIp);
        $dbIp6 = self::ipv4ToIpv6($dbIp);

        return array(
            array('db'),
            array($dbIp),
            array('db:3306'),
            array($dbIp.':3306'),

            array($dbIp6, 'connect'),
            array('['.$dbIp6.']', 'connect'),

            array(':/var/run/mysqld/mysqld.sock'),
            array('db:/var/run/mysqld/mysqld.sock'),
            array($dbIp.':3306:/var/run/mysqld/mysqld.sock'),
            array('['.$dbIp6.']:3306:/var/run/mysqld/mysqld.sock'),
        );
    }

    private static function ipv4ToIpv6($ip)
    {
        $bytes = array_map('dechex', explode('.', $ip));

        return vsprintf('0:0:0:0:0:ffff:%02s%02s:%02s%02s', $bytes);
    }
}
