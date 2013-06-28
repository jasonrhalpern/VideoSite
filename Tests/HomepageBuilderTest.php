<?php
/**
 * @author Jason Halpern
 * @since 6/16/2013
 */

require_once dirname(__FILE__) . '/../Classes/MySQL.php';
require_once dirname(__FILE__) . '/../Classes/Competition.php';
require_once dirname(__FILE__) . '/../Classes/DateHelper.php';
require_once dirname(__FILE__) . '/../Classes/User.php';
require_once dirname(__FILE__) . '/../Classes/Video.php';
require_once dirname(__FILE__) . '/../PageBuilders/HomepageBuilder.php';

class HomepageBuilderTest extends PHPUnit_Framework_TestCase{

    protected $dbConnection;
    protected $competitionOne;
    protected $competitionOneId;
    protected $competitionTwo;
    protected $competitionTwoId;
    protected $competitionThree;
    protected $competitionThreeId;
    protected $competitionFour;
    protected $competitionFourId;
    protected $competitionFive;
    protected $competitionFiveId;
    protected $competitionSix;
    protected $competitionSixId;
    protected $competitionSeven;
    protected $competitionSevenId;
    protected $userOne;
    protected $video;

    public function setUp(){
        $this->dbConnection = new MySQL();

        $this->competitionOne = new Competition("acting like deniro", "do your best deniro impression",
            DateHelper::datePlusDays(DateHelper::currentDate(), -4),
            DateHelper::datePlusDays(DateHelper::currentDate(), 2),
            2.99, 'Individual', 'Acting');
        $this->dbConnection->insertCompetition($this->competitionOne);
        $this->competitionOneId = $this->dbConnection->mostRecentCompetitionId();

        $this->competitionTwo = new Competition("acting like pacino", "do your best pacino impression",
            DateHelper::datePlusDays(DateHelper::currentDate(), -3),
            DateHelper::datePlusDays(DateHelper::currentDate(), 3),
            2.99, 'Individual', 'Acting');
        $this->dbConnection->insertCompetition($this->competitionTwo);
        $this->competitionTwoId = $this->dbConnection->mostRecentCompetitionId();

        $this->competitionThree = new Competition("acting like robin williams", "do your best williams impression",
            DateHelper::datePlusDays(DateHelper::currentDate(), -2),
            DateHelper::datePlusDays(DateHelper::currentDate(), 4),
            2.99, 'Individual', 'Comedy');
        $this->dbConnection->insertCompetition($this->competitionThree);
        $this->competitionThreeId= $this->dbConnection->mostRecentCompetitionId();

        $this->competitionFour = new Competition("acting like jamie oliver", "do your best oliver impression",
            DateHelper::datePlusDays(DateHelper::currentDate(), 2),
            DateHelper::datePlusDays(DateHelper::currentDate(), 6),
            1.99, 'Individual', 'Food');
        $this->dbConnection->insertCompetition($this->competitionFour);
        $this->competitionFourId = $this->dbConnection->mostRecentCompetitionId();

        $this->competitionFive = new Competition("acting like heidi klum", "do your best klum impression",
            DateHelper::datePlusDays(DateHelper::currentDate(), 4),
            DateHelper::datePlusDays(DateHelper::currentDate(), 8),
            2.99, 'Individual', 'Fashion');
        $this->dbConnection->insertCompetition($this->competitionFive);
        $this->competitionFiveId = $this->dbConnection->mostRecentCompetitionId();

        $this->competitionSix = new Competition("acting like arnold", "do your best arnold impression",
            DateHelper::datePlusDays(DateHelper::currentDate(), 5),
            DateHelper::datePlusDays(DateHelper::currentDate(), 9),
            2.99, 'Individual', 'Acting');
        $this->dbConnection->insertCompetition($this->competitionSix);
        $this->competitionSixId = $this->dbConnection->mostRecentCompetitionId();

        $this->competitionSeven = new Competition("acting", "do your best impression",
            DateHelper::datePlusDays(DateHelper::currentDate(), -5),
            DateHelper::datePlusDays(DateHelper::currentDate(), -2),
            2.99, 'Individual', 'Acting');
        $this->dbConnection->insertCompetition($this->competitionSeven);
        $this->competitionSevenId = $this->dbConnection->mostRecentCompetitionId();

        $this->userOne = new User('blahhhh', 'blaaaahhh@aol.com', 'blah2394', 1, 'blahhh');
        $this->dbConnection->insertUser($this->userOne);

        $user = User::login($this->userOne->getEmail(), $this->userOne->getPassword());
        $this->video = new Video('Hicks vss Gangstas booyah', 'battlezz of tha century', $user->getId());
        $this->dbConnection->insertVideo($this->video);
        $videoId = $this->dbConnection->mostRecentVideoId($user->getId());
        $this->video = Video::loadVideoById($videoId);

        $this->dbConnection->insertCompetitionWinner(1, $videoId);
    }

    public function tearDown(){
        $this->dbConnection->deleteCompetition($this->competitionOneId);
        $this->dbConnection->deleteCompetition($this->competitionTwoId);
        $this->dbConnection->deleteCompetition($this->competitionThreeId);
        $this->dbConnection->deleteCompetition($this->competitionFourId);
        $this->dbConnection->deleteCompetition($this->competitionFiveId);
        $this->dbConnection->deleteCompetition($this->competitionSixId);
        $this->dbConnection->deleteCompetition($this->competitionSevenId);
        $this->dbConnection->deleteCompetitionWinner(1, $this->video->getVideoId());
        $this->dbConnection->deleteUser($this->userOne);
        $this->dbConnection->deleteVideo($this->video);


        unset($this->competitionOne);
        unset($this->competitionTwo);
        unset($this->competitionThree);
        unset($this->competitionFour);
        unset($this->competitionFive);
        unset($this->competitionSix);
        unset($this->competitionSeven);

        unset($this->competitionOneId);
        unset($this->competitionTwoId);
        unset($this->competitionThreeId);
        unset($this->competitionFourId);
        unset($this->competitionFiveId);
        unset($this->competitionSixId);
        unset($this->competitionSevenId);

        unset($this->userOne);
        unset($this->video);
        unset($this->dbConnection);
    }

    public function testBuildHomepage(){

        $this->assertTrue(true);
    }

    public function testLoadCurrentCompetitions(){
        $homepageBuilder = new HomepageBuilder();
        $currentCompetitions = $homepageBuilder->loadCurrentCompetitions();

        var_dump($currentCompetitions);

        $this->assertFalse(empty($currentCompetitions));
        $this->assertEquals(count($currentCompetitions), 3);

        $this->assertEquals($currentCompetitions[0]["title"], "acting like deniro");
        $this->assertEquals($currentCompetitions[0]["description"], "do your best deniro impression");
        $this->assertEquals($currentCompetitions[0]["entry_fee"], 2.99);
        $this->assertEquals($currentCompetitions[0]["type"], "Individual");
        $this->assertEquals($currentCompetitions[0]["category"], "Acting");
        $this->assertEquals($currentCompetitions[0]["participants"], 0);

        $this->assertEquals($currentCompetitions[1]["title"], "acting like pacino");
        $this->assertEquals($currentCompetitions[1]["description"], "do your best pacino impression");
        $this->assertEquals($currentCompetitions[1]["entry_fee"], 2.99);
        $this->assertEquals($currentCompetitions[1]["type"], "Individual");
        $this->assertEquals($currentCompetitions[1]["category"], "Acting");
        $this->assertEquals($currentCompetitions[1]["participants"], 0);

        $this->assertEquals($currentCompetitions[2]["title"], "acting like robin williams");
        $this->assertEquals($currentCompetitions[2]["description"], "do your best williams impression");
        $this->assertEquals($currentCompetitions[2]["entry_fee"], 2.99);
        $this->assertEquals($currentCompetitions[2]["type"], "Individual");
        $this->assertEquals($currentCompetitions[2]["category"], "Comedy");
        $this->assertEquals($currentCompetitions[2]["participants"], 0);
    }

    public function testLoadUpcomingCompetitions(){
        $homepageBuilder = new HomepageBuilder();
        $upcomingCompetitions = $homepageBuilder->loadUpcomingCompetitions();

        var_dump($upcomingCompetitions);

        $this->assertFalse(empty($upcomingCompetitions));
        $this->assertEquals(count($upcomingCompetitions), 3);

        $this->assertEquals($upcomingCompetitions[0]["title"], "acting like jamie oliver");
        $this->assertEquals($upcomingCompetitions[0]["description"], "do your best oliver impression");
        $this->assertEquals($upcomingCompetitions[0]["entry_fee"], 1.99);
        $this->assertEquals($upcomingCompetitions[0]["type"], "Individual");
        $this->assertEquals($upcomingCompetitions[0]["category"], "Food");

        $this->assertEquals($upcomingCompetitions[1]["title"], "acting like heidi klum");
        $this->assertEquals($upcomingCompetitions[1]["description"], "do your best klum impression");
        $this->assertEquals($upcomingCompetitions[1]["entry_fee"], 2.99);
        $this->assertEquals($upcomingCompetitions[1]["type"], "Individual");
        $this->assertEquals($upcomingCompetitions[1]["category"], "Fashion");

        $this->assertEquals($upcomingCompetitions[2]["title"], "acting like arnold");
        $this->assertEquals($upcomingCompetitions[2]["description"], "do your best arnold impression");
        $this->assertEquals($upcomingCompetitions[2]["entry_fee"], 2.99);
        $this->assertEquals($upcomingCompetitions[2]["type"], "Individual");
        $this->assertEquals($upcomingCompetitions[2]["category"], "Acting");
    }

    public function testLoadRecentWinners(){
        $this->assertTrue(true);
    }
}