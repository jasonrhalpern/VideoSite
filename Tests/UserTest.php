<?php
/**
 * @author Jason Halpern
 */

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/../Classes/User.php';
require_once dirname(__FILE__) . '/../Classes/MySQL.php';

class UserTest extends PHPUnit_Framework_TestCase{

    protected $userOne;
    protected $userTwo;
    protected $userThree;
    protected $userFour;
    protected $userFive;
    protected $dbConnection;

    public function setUp(){
        $this->userOne = new User('Donope Gangsta', 'georgeiee106@aol.com', 'GMoney5897', 1, 'tanya');
        $this->userTwo = new User('Stevie Boyko', 'georgeiee106@aol.com', 'Stevie1239', 1, 'teddie');
        $this->userThree = new User('Fty Blezie11', 'faraktadwa75@yahoo.com', 'GMoney5897', 1, 'teddie');
        $this->userFour = new User('Pooolles', 'joona1234@aol.edu', 'Tommy34LP', 1, 'teddie');
        $this->userFive = new User('xxxyyyzzz', 'faraktadwa75@yahoo.com', 'drandranw', 1, 'teddie');
        $this->dbConnection = new MySQL();
    }

    public function tearDown(){
        unset($this->userOne);
        unset($this->userTwo);
        unset($this->userThree);
        unset($this->userFour);
        unset($this->userFive);
        unset($this->dbConnection);
    }

    public function testIsRegistered(){
        $this->assertTrue($this->dbConnection->insertUser($this->userOne));
        $this->assertTrue($this->userOne->isRegistered());
        $this->assertFalse($this->userTwo->isRegistered());

        $this->dbConnection->deleteUser($this->userOne);
    }

    public function testHasDuplicateEmail(){
        $this->assertTrue($this->dbConnection->insertUser($this->userOne));
        $this->assertTrue($this->userTwo->hasDuplicateEmail());
        $this->assertFalse($this->userThree->hasDuplicateEmail());

        $this->dbConnection->deleteUser($this->userOne);
    }

    public function testHasValidPassword(){
        $one = new User('Donope Gangsta', 'tornaldz30t@aol.com', 'GMoney6612', 1, 'tanya');
        $two = new User('Donope Gangsta', 'tornaldz30t@aol.com', 'GMoney6612', 1, 'tanyaaaaaaaa');
        $three = new User('Donope Gangsta', 'tornaldz30t@aol.com', 'GMoney6612', 1, 'tany');

        $this->assertTrue($one->hasValidPassword());
        $this->assertTrue($two->hasValidPassword());
        $this->assertFalse($three->hasValidPassword());
    }

    public function testHasValidUsername(){
        $one = new User('Donope Gangsta', 'tornaldz30t@aol.com', 'GMoney6612', 1, 'tanya');
        $two = new User('Donope Gangsta', 'tornaldz30t@aol.com', 'GM6', 1, 'tanya');
        $three = new User('Donope Gangsta', 'tornaldz30t@aol.com', 'GMoney661222222222222222222222222', 1, 'tanya');

        $this->assertTrue($one->hasValidUsername());
        $this->assertFalse($two->hasValidUsername());
        $this->assertFalse($three->hasValidUsername());
    }

    public function testHasOnlyAlphanumericCharacters(){
        $one = new User('Donope Gangsta', 'tornaldz30t@aol.com', 'GMoney6612', 1, 'tanya');
        $two = new User('Donope Gangsta', 'tornaldz30t@aol.com', 'G Money_6612', 1, 'tanya');
        $three = new User('Donope Gangsta', 'tornaldz30t@aol.com', 'GMoney $6612', 1, 'tanya');
        $four = new User('Donope Gangsta', 'tornaldz30t@aol.com', 'GMoney6612$', 1, 'tanya');
        $five = new User('Donope Gangsta', 'tornaldz30t@aol.com', 'GMone#y 6612', 1, 'tanya');
        $six = new User('Donope Gangsta', 'tornaldz30t@aol.com', 'GMoney6;612$', 1, 'tanya');
        $seven = new User('Donope Gangsta', 'tornaldz30t@aol.com', '<>', 1, 'tanya');

        $this->assertTrue($one->hasOnlyAlphanumericCharacters());
        $this->assertTrue($two->hasOnlyAlphanumericCharacters());
        $this->assertFalse($three->hasOnlyAlphanumericCharacters());
        $this->assertFalse($four->hasOnlyAlphanumericCharacters());
        $this->assertFalse($five->hasOnlyAlphanumericCharacters());
        $this->assertFalse($six->hasOnlyAlphanumericCharacters());
        $this->assertFalse($seven->hasOnlyAlphanumericCharacters());
    }

    public function testHasDuplicateUsername(){
        $this->assertTrue($this->dbConnection->insertUser($this->userOne));
        $this->assertFalse($this->userTwo->hasDuplicateUsername());
        $this->assertTrue($this->userThree->hasDuplicateUsername());

        $this->dbConnection->deleteUser($this->userOne);
    }

    public function testLogin(){
        $this->assertTrue($this->dbConnection->insertUser($this->userOne));
        $user = User::login($this->userOne->getEmail(), $this->userOne->getPassword());
        $this->assertEquals($user->getEmail(), 'georgeiee106@aol.com');
        $this->assertEquals($user->getUsername(), 'GMoney5897');
        $this->assertEquals($user->getName(), 'Donope Gangsta');
        $this->assertEquals($user->getJoined(), DateHelper::currentDate());

        /* the next three users do not exist */
        $failedLogin = User::login("bagadfasdf", "asdasdawnnosdfp");
        $this->assertFalse($failedLogin);

        $failedLoginTwo = User::login($user->getEmail(), 'tedie');
        $this->assertFalse($failedLoginTwo);

        $failedLoginThree = User::login('georgeie106@aol.com', $user->getPassword());
        $this->assertFalse($failedLoginThree);

        $this->dbConnection->deleteUser($this->userOne);
    }

    public function testRegister(){
        $registerArray = $this->userThree->register();
        $this->assertTrue($registerArray['valid']);

        $this->assertTrue($this->dbConnection->insertUser($this->userFour));
        $registerArray = $this->userFour->register();
        $this->assertFalse($registerArray['valid']);
        $this->assertContains('This username is already taken', $registerArray['errors']);
        $this->assertContains('This email is already in our system', $registerArray['errors']);
        $this->assertNotContains('Your password must be at least 5 characters long', $registerArray['errors']);
        $this->assertNotContains('Your username must be between 4 and 20 characters long', $registerArray['errors']);
        $this->assertNotContains('Your username can only contain letters and numbers', $registerArray['errors']);

        $registerArray = $this->userOne->register();
        $this->assertFalse($registerArray['valid']);
        $this->assertContains('This username is already taken', $registerArray['errors']);
        $this->assertNotContains('This email is already in our system', $registerArray['errors']);
        $this->assertNotContains('Your password must be at least 5 characters long', $registerArray['errors']);
        $this->assertNotContains('Your username must be between 4 and 20 characters long', $registerArray['errors']);
        $this->assertNotContains('Your username can only contain letters and numbers', $registerArray['errors']);

        $registerArray = $this->userFive->register();
        $this->assertFalse($registerArray['valid']);
        $this->assertContains('This email is already in our system', $registerArray['errors']);
        $this->assertNotContains('This username is already taken', $registerArray['errors']);
        $this->assertNotContains('Your password must be at least 5 characters long', $registerArray['errors']);
        $this->assertNotContains('Your username must be between 4 and 20 characters long', $registerArray['errors']);
        $this->assertNotContains('Your username can only contain letters and numbers', $registerArray['errors']);

        $seven = new User('Donope Gangsta', '@aol.com', 'G!', 1, 'tan');
        $registerArray = $seven->register();
        $this->assertFalse($registerArray['valid']);
        $this->assertContains('This email is not valid', $registerArray['errors']);
        $this->assertContains('Your username must be between 4 and 20 characters long', $registerArray['errors']);
        $this->assertContains('Your username can only contain letters and numbers', $registerArray['errors']);
        $this->assertContains('Your password must be at least 5 characters long', $registerArray['errors']);
        $this->assertNotContains('This username is already taken', $registerArray['errors']);
        $this->assertNotContains('This email is already in our system', $registerArray['errors']);

        $loggedIn = User::login($this->userThree->getEmail(), $this->userThree->getPassword());
        $this->assertContainsOnlyInstancesOf('User', array($loggedIn));


        $this->dbConnection->deleteUser($this->userFour);
        $this->dbConnection->deleteUser($this->userThree);
    }

    public function testChangeUsername(){
        $one = new User('Donope Gangsta', 'dandepa98k@aol.com', 'GMoney589', 1, 'tanya');
        $registerArray = $one->register();
        $this->assertTrue($registerArray['valid']);
        $this->assertTrue($one->changeUsername('Jameson Jones11'));
        $loggedInOne = User::login($one->getEmail(), $one->getPassword());
        $this->assertEquals($loggedInOne->getUsername(), 'Jameson Jones11');

        $two = new User('Stevance Dominque1', 'steveie.domzz@yahoo.com', 'StevieDomzz1', 1, 'tanya');
        $registerArray = $two->register();
        $this->assertTrue($registerArray['valid']);
        $this->assertFalse($two->changeUsername('Jameson Jones11'));
        $this->assertTrue($two->changeUsername('Pinkus Maximum 7'));

        $this->dbConnection->deleteUser($one);
        $this->dbConnection->deleteUser($two);

    }

    public function testChangePassword(){
        $one = new User('Donope Gangsta', 'danny82q1@aol.com', 'GMoney5896', 1, 'tanya');
        $registerArray = $one->register();
        $this->assertTrue($registerArray['valid']);
        $this->assertTrue($one->changePassword('simeon'));
        $this->assertEquals($one->getPassword(), 'simeon');
        $loggedInOne = User::login($one->getEmail(), 'simeon');
        $this->assertEquals($loggedInOne->getUsername(), 'GMoney5896');
        $this->assertEquals($loggedInOne->getPassword(), 'simeon');
        $this->assertEquals($loggedInOne->getEmail(), 'danny82q1@aol.com');
        $this->dbConnection->deleteUser($one);

        $two = new User('Stevance Dominque1', 'steveie.willieez@yahoo.com', 'StevieWillz12', 1, 'tanya');
        $this->assertFalse($two->changePassword('wow'));
    }

    public function testResetPassword(){
        $one = new User('Donope Gangsta', 'tornaldz30t@aol.com', 'GMoney6612', 1, 'tanya');
        $registerArray = $one->register();
        $this->assertTrue($registerArray['valid']);
        $oldPassword = $one->getPassword();
        $newPassword = $one->resetPassword();
        $failedLogin = User::login($one->getEmail(), $oldPassword);
        $this->assertFalse($failedLogin);

        $loggedIn = User::login($one->getEmail(), $one->getPassword());
        $this->assertEquals($loggedIn->getUsername(), 'GMoney6612');
        $this->assertEquals($loggedIn->getPassword(), $newPassword);
        $this->assertEquals($loggedIn->getName(), 'Donope Gangsta');
        $this->assertEquals($loggedIn->getEmail(), 'tornaldz30t@aol.com');
        $this->dbConnection->deleteUser($one);

        $two = new User('Stevance Dominque1', 'steveie.domzz@yahoo.com', 'StevieDomzz1', 1, 'tanya');
        $this->assertFalse($two->changePassword('wow'));

    }

    public function testHasValidEmail(){
        $one = new User('Donope Gangsta', 'dan@aol.com', 'GMoney589', 1, 'tanya');
        $this->assertTrue($one->hasValidEmail());
        unset($one);

        $two = new User('Donope Gangsta', 'dan.tony1@yahoo.com', 'GMoney589', 1, 'tanya');
        $this->assertTrue($two->hasValidEmail());
        unset($two);

        $three = new User('Donope Gangsta', 'SteveDon@adelphia.edu', 'GMoney589', 1, 'tanya');
        $this->assertTrue($three->hasValidEmail());
        unset($three);

        $four = new User('Donope Gangsta', 'winston.francis.tony@me.co', 'GMoney589', 1, 'tanya');
        $this->assertTrue($four->hasValidEmail());
        unset($four);

        $five = new User('Donope Gangsta', 'winston', 'GMoney589', 1, 'tanya');
        $this->assertFalse($five->hasValidEmail());
        unset($five);

        $seven = new User('Donope Gangsta', '@aol.com', 'GMoney589', 1, 'tanya');
        $this->assertFalse($seven->hasValidEmail());
        unset($seven);

        $eight = new User('Donope Gangsta', 'ted.jon', 'GMoney589', 1, 'tanya');
        $this->assertFalse($eight->hasValidEmail());
        unset($eight);

        $nine = new User('Donope Gangsta', '5', 'GMoney589', 1, 'tanya');
        $this->assertFalse($nine->hasValidEmail());
        unset($nine);

        $ten = new User('Donope Gangsta', '', 'GMoney589', 1, 'tanya');
        $this->assertFalse($ten->hasValidEmail());
        unset($ten);
    }

    public function testHasVoted(){
        $this->dbConnection->insertUser($this->userOne);
        $loggedIn = User::login($this->userOne->getEmail(), $this->userOne->getPassword());

        $loggedIn->addVote(1, 1);
        $this->assertTrue($loggedIn->hasVoted(1));
        $this->assertFalse($loggedIn->hasVoted(2));
        $loggedIn->addVote(1, 2);
        $this->assertTrue($loggedIn->hasVoted(2));
        $loggedIn->deleteVote(1, 1);
        $loggedIn->deleteVote(1, 2);

        $this->dbConnection->deleteUser($loggedIn);
    }

    public function testGetNumberOfVotesRemaining(){
        $this->dbConnection->insertUser($this->userOne);
        $loggedIn = User::login($this->userOne->getEmail(), $this->userOne->getPassword());

        $loggedIn->addVote(1, 1);
        $this->assertTrue($loggedIn->hasVoted(1));
        $this->assertFalse($loggedIn->hasVoted(2));
        $this->assertEquals($loggedIn->getNumberOfVotesRemaining(1), 2);
        $this->assertFalse($loggedIn->addVote(1, 1));

        $loggedIn->addVote(1, 2);
        $this->assertTrue($loggedIn->hasVoted(2));
        $this->assertEquals($loggedIn->getNumberOfVotesRemaining(1), 1);
        $this->assertFalse($loggedIn->addVote(1, 2));

        $loggedIn->addVote(1, 3);
        $this->assertTrue($loggedIn->hasVoted(3));
        $this->assertEquals($loggedIn->getNumberOfVotesRemaining(1), 0);
        $this->assertFalse($loggedIn->addVote(1, 3));

        $this->assertFalse($loggedIn->addVote(1, 4));
        $this->assertFalse($loggedIn->addVote(1, 5));

        $loggedIn->deleteVote(1, 1);
        $loggedIn->deleteVote(1, 2);
        $loggedIn->deleteVote(1, 3);

        $this->dbConnection->deleteUser($loggedIn);
    }
}
