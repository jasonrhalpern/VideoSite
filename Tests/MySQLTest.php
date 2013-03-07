<?php
/**
 * @author Jason Halpern
 */

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/../MySQL.php';
require_once dirname(__FILE__) . '/../User.php';

class MySQLTest extends PHPUnit_Framework_TestCase{
    protected $user;
    protected $dbConnection;

    public function setUp(){
        $this->user = new User('Jason HollaBack', 'jasonrhalpern@gmail.com', 'JzPern67', 1, 'sailor');
        $this->dbConnection = new MySQL();
    }

    public function tearDown(){
        $this->dbConnection->deleteUser($this->user);

        unset($this->user);
        unset($this->dbConnection);
    }

    public function testConnect(){
        $this->assertTrue($this->dbConnection->isConnected());
    }

    public function testInsertUser(){
        $this->assertTrue($this->dbConnection->insertUser($this->user));
    }

    public function testUserExists(){
        $this->assertTrue($this->dbConnection->insertUser($this->user));
        $this->assertTrue($this->dbConnection->userExists($this->user));
    }
}
