<?php
/**
 * @author Jason Halpern
 */

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/../MySQL.php';
require_once dirname(__FILE__) . '/../User.php';

class MySQLTest extends PHPUnit_Framework_TestCase
{
    protected $user;

    public function setUp()
    {
        $this->user = new User('Jason', 'jasonrhalpern@gmail.com', 'JPern', 1, 'sailor');
    }

    public function tearDown()
    {
    }

    public function testConnect()
    {
        $testConnection = new MySQL();
        $this->assertTrue($testConnection->isConnected());
    }

    public function testInsertUser(){
        $testConnection = new MySQL();
        $this->assertTrue($testConnection->insertUser($this->user));
    }
}
