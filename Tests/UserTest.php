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

    public function testIsRegistered(){
        $this->assertTrue($this->dbConnection->insertUser($this->userOne));
        $this->assertTrue($this->userOne->isRegistered());
        $this->assertFalse($this->userTwo->isRegistered());
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

    public function testLogin(){
        $this->assertTrue($this->dbConnection->insertUser($this->userOne));
        $user = User::login($this->userOne->getEmail(), $this->userOne->getPassword());
        $this->assertEquals($user->getEmail(), 'georgeie10@aol.com');
        $this->assertEquals($user->getUsername(), 'GMoney89');
        $this->assertEquals($user->getName(), 'Don Gangsta');
        $this->assertEquals($user->getJoined(), DateHelper::currentDate());

        $failedLogin = User::login("bagadfasdf", "asdasdawnnosdfp");
        $this->assertFalse($failedLogin);
    }
}
