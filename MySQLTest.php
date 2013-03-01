<?php
/**
 * @author Jason Halpern
 */

require_once('MySQL.php');

class MySQLTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function testConnect()
    {
        $testConnection = new MySQL();
        $this->assertTrue($testConnection->connect(), true);
    }
}
