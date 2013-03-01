<?php
/**
 * @author Jason Halpern
 */

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/../MySQL.php';

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
