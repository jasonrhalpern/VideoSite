<?php
/**
 * @author Jason Halpern
 */
require_once dirname(__FILE__) . '/../Classes/MySQL.php';
require_once dirname(__FILE__) . '/../Classes/Competition.php';
require_once dirname(__FILE__) . '/../Classes/DateHelper.php';
require_once dirname(__FILE__) . '/../Classes/User.php';
require_once dirname(__FILE__) . '/../Classes/Video.php';
require_once dirname(__FILE__) . '/../PageBuilders/CompetitionPageBuilder.php';

class CompetitionPageBuilderTest extends PHPUnit_Framework_TestCase{

    protected $dbConnection;
    protected $competition;
    protected $userOne;
    protected $userTwo;
    protected $videoOne;
    protected $videoTwo;

    public function setUp(){
        $this->dbConnection = new MySQL();

        $this->userOne = new User('blahhhh', 'blaaaahhh@aol.com', 'blah2394', 1, 'blahhh');
        $this->dbConnection->insertUser($this->userOne);

        $this->userTwo = new User('blahshh', 'blaaashhh@aol.com', 'blas2394', 1, 'blahhh');
        $this->dbConnection->insertUser($this->userTwo);

        $user = User::login($this->userOne->getEmail(), $this->userOne->getPassword());
        $this->userOne = $user;
        $this->videoOne = new Video('Hicks vss Gangstas booyah', 'battlezz of tha century', $user->getId());
        $this->dbConnection->insertVideo($this->videoOne);
        $videoId = $this->dbConnection->mostRecentVideoId($user->getId());
        $this->videoOne = Video::loadVideoById($videoId);

        $this->dbConnection->insertCompetitionWinner(646, $videoId);

        $user = User::login($this->userTwo->getEmail(), $this->userTwo->getPassword());
        $this->userTwo = $user;
        $this->videoTwo = new Video('Hicks vz Gangstas booyah', 'battezz of tha century', $user->getId());
        $this->dbConnection->insertVideo($this->videoTwo);
        $videoId = $this->dbConnection->mostRecentVideoId($user->getId());
        $this->videoTwo = Video::loadVideoById($videoId);

        $this->dbConnection->insertCompetitionRunnerUp(646, $videoId);
    }

    public function tearDown(){
        $this->dbConnection->deleteCompetition($this->competition);
        $this->dbConnection->deleteCompetitionWinner(646, $this->videoOne->getVideoId());
        $this->dbConnection->deleteCompetitionRunnerUp(646, $this->videoTwo->getVideoId());
        $this->dbConnection->deleteUser($this->userOne);
        $this->dbConnection->deleteUser($this->userTwo);
        $this->dbConnection->deleteVideo($this->videoOne);
        $this->dbConnection->deleteVideo($this->videoTwo);

        unset($this->dbConnection);
        unset($this->userOne);
        unset($this->userTwo);
        unset($this->competition);
        unset($this->videoOne);
        unset($this->videoTwo);
    }

    public function testLoadCompetition(){
        $competitionPageBuilder = new CompetitionPageBuilder(646);
        $competition = $competitionPageBuilder->loadCompetition();

        $this->assertFalse(empty($competition));

        $this->assertEquals($competition["id"], 646);
        $this->assertEquals($competition["title"], 'acting like deniro');
        $this->assertEquals($competition["description"], 'do your best deniro impression');
        $this->assertEquals($competition["startDate"], '2013-06-20');
        $this->assertEquals($competition["endDate"], '2013-06-24');
        $this->assertEquals($competition["entryFee"], '2.99');
        $this->assertEquals($competition["type"], 'Individual');
        $this->assertEquals($competition["category"], 'Acting');
        $this->assertEquals($competition["participants"], 0);
    }

    public function testLoadWinner(){
        $competitionPageBuilder = new CompetitionPageBuilder(646);
        $winner = $competitionPageBuilder->loadWinner();

        $this->assertFalse(empty($winner));

        $this->assertEquals($winner["id"], $this->videoOne->getVideoId());
        $this->assertEquals($winner["title"], $this->videoOne->getTitle());
        $this->assertEquals($winner["description"], $this->videoOne->getDescription());
        $this->assertEquals($winner["created"], $this->videoOne->getPostedDate());
        $this->assertEquals($winner["user"], $this->userOne->getUsername());
        $this->assertEquals($winner["views"], $this->videoOne->getViews());
        $this->assertEquals($winner["length"], $this->videoOne->getLength());
        $this->assertEquals($winner["likes"], $this->videoOne->getLikes());

    }

    public function testLoadRunnerUp(){
        $competitionPageBuilder = new CompetitionPageBuilder(646);
        $runnerUp = $competitionPageBuilder->loadRunnerUp();

        $this->assertFalse(empty($runnerUp));

        $this->assertEquals($runnerUp["id"], $this->videoTwo->getVideoId());
        $this->assertEquals($runnerUp["title"], $this->videoTwo->getTitle());
        $this->assertEquals($runnerUp["description"], $this->videoTwo->getDescription());
        $this->assertEquals($runnerUp["created"], $this->videoTwo->getPostedDate());
        $this->assertEquals($runnerUp["user"], $this->userTwo->getUsername());
        $this->assertEquals($runnerUp["views"], $this->videoTwo->getViews());
        $this->assertEquals($runnerUp["length"], $this->videoTwo->getLength());
        $this->assertEquals($runnerUp["likes"], $this->videoTwo->getLikes());
    }
}