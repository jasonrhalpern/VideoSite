<?php
/**
 * @author Jason Halpern
 */

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/../MySQL.php';
require_once dirname(__FILE__) . '/../User.php';

class MySQLTest extends PHPUnit_Framework_TestCase{
    protected $user;
    protected $dupeEmail;
    protected $dupeUsername;
    protected $dbConnection;

    public function setUp(){
        $this->user = new User('Jason HollaBack', 'jasonrhalpern@gmail.com', 'JPern67', 1, 'sailor');
        $this->dupeEmail = new User('Stanley', 'jasonrhalpern@gmail.com', 'Willyz', 1, 'sailor1');
        $this->dupeUsername = new User('Jason HollaBack', 'tom@gmail.com', 'JPern67', 1, 'sailor2');
        $this->dbConnection = new MySQL();
    }

    public function tearDown(){
        $this->dbConnection->deleteUser($this->user);
    }

    public function testConnect(){
        $this->assertTrue($this->dbConnection->isConnected());
    }

    public function testInsertUser(){
        $this->assertTrue($this->dbConnection->insertUser($this->user));
        $this->assertFalse($this->dbConnection->insertUser($this->dupeEmail));
        $this->assertFalse($this->dbConnection->insertUser($this->dupeUsername));
    }
}
