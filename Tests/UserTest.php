<?php
/**
 * @author Jason Halpern
 */

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/../User.php';
require_once dirname(__FILE__) . '/../MySQL.php';

class UserTest extends PHPUnit_Framework_TestCase{

    protected $userOne;
    protected $userTwo;
    protected $userThree;
    protected $dbConnection;

    public function setUp(){
        $this->userOne = new User('Don Gangsta', 'georgeie10@aol.com', 'GMoney89', 1, 'tanya');
        $this->userTwo = new User('Stevie Boy', 'georgeie10@aol.com', 'Stevie1239', 1, 'teddie');
        $this->userThree = new User('F Blezie11', 'farakadwa@yahoo.com', 'GMoney89', 1, 'teddie');
        $this->dbConnection = new MySQL();
    }

    public function tearDown(){
        $this->dbConnection->deleteUser($this->userOne);
    }

    public function testLogin(){
        $this->assertTrue($this->dbConnection->insertUser($this->userOne));
        $this->assertTrue($this->userOne->login());
        $this->assertFalse($this->userTwo->login());
    }

    public function testHasDuplicateEmail(){
        $this->assertTrue($this->dbConnection->insertUser($this->userOne));
        $this->assertTrue($this->userTwo->hasDuplicateEmail());
        $this->assertFalse($this->userThree->hasDuplicateEmail());

    }

    public function testHasDuplicateUsername(){
        $this->assertTrue($this->dbConnection->insertUser($this->userOne));
        $this->assertFalse($this->userTwo->hasDuplicateUsername());
        $this->assertTrue($this->userThree->hasDuplicateUsername());
    }
}
