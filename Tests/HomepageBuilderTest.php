<?php
/**
 * @author Jason Halpern
 * @since 6/16/2013
 */

require_once dirname(__FILE__) . '/../Classes/MySQL.php';
require_once dirname(__FILE__) . '/../Classes/Competition.php';
require_once dirname(__FILE__) . '/../Classes/DateHelper.php';

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

    public function setUp(){
        $this->dbConnection = new MySQL();

        $this->competitionOne = new Competition("acting like deniro", "do your best deniro impression",
            DateHelper::datePlusDays(DateHelper::currentDate(), -3),
            DateHelper::datePlusDays(DateHelper::currentDate(), 1),
            2.99, 'Individual', 'Acting');
        $this->dbConnection->insertCompetition($this->competitionOne);
        $this->competitionOneId = $this->dbConnection->mostRecentCompetitionId();

        $this->competitionTwo = new Competition("acting like pacino", "do your best pacino impression",
            DateHelper::datePlusDays(DateHelper::currentDate(), -2),
            DateHelper::datePlusDays(DateHelper::currentDate(), 2),
            2.99, 'Individual', 'Acting');
        $this->dbConnection->insertCompetition($this->competitionTwo);
        $this->competitionTwoId = $this->dbConnection->mostRecentCompetitionId();

        $this->competitionThree = new Competition("acting like robin williams", "do your best williams impression",
            DateHelper::datePlusDays(DateHelper::currentDate(), 0),
            DateHelper::datePlusDays(DateHelper::currentDate(), 4),
            2.99, 'Individual', 'Comedy');
        $this->dbConnection->insertCompetition($this->competitionThree);
        $this->competitionThreeId= $this->dbConnection->mostRecentCompetitionId();

        $this->competitionFour = new Competition("acting like jamie oliver", "do your best oliver impression",
            DateHelper::datePlusDays(DateHelper::currentDate(), 1),
            DateHelper::datePlusDays(DateHelper::currentDate(), 5),
            2.99, 'Individual', 'Food');
        $this->dbConnection->insertCompetition($this->competitionFour);
        $this->competitionFourId = $this->dbConnection->mostRecentCompetitionId();

        $this->competitionFive = new Competition("acting like heidi klum", "do your best klum impression",
            DateHelper::datePlusDays(DateHelper::currentDate(), 3),
            DateHelper::datePlusDays(DateHelper::currentDate(), 7),
            2.99, 'Individual', 'Fashion');
        $this->dbConnection->insertCompetition($this->competitionFive);
        $this->competitionFiveId = $this->dbConnection->mostRecentCompetitionId();

        $this->competitionSix = new Competition("acting like arnold", "do your best arnold impression",
            DateHelper::datePlusDays(DateHelper::currentDate(), 4),
            DateHelper::datePlusDays(DateHelper::currentDate(), 8),
            2.99, 'Individual', 'Acting');
        $this->dbConnection->insertCompetition($this->competitionSix);
        $this->competitionSixId = $this->dbConnection->mostRecentCompetitionId();

        $this->competitionSeven = new Competition("acting", "do your best impression",
            DateHelper::datePlusDays(DateHelper::currentDate(), -5),
            DateHelper::datePlusDays(DateHelper::currentDate(), -1),
            2.99, 'Individual', 'Acting');
        $this->dbConnection->insertCompetition($this->competitionSeven);
        $this->competitionSevenId = $this->dbConnection->mostRecentCompetitionId();
    }

    public function tearDown(){
        $this->dbConnection->deleteCompetition($this->competitionOneId);
        $this->dbConnection->deleteCompetition($this->competitionTwoId);
        $this->dbConnection->deleteCompetition($this->competitionThreeId);
        $this->dbConnection->deleteCompetition($this->competitionFourId);
        $this->dbConnection->deleteCompetition($this->competitionFiveId);
        $this->dbConnection->deleteCompetition($this->competitionSixId);
        $this->dbConnection->deleteCompetition($this->competitionSevenId);

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

        unset($this->dbConnection);
    }

    public function testBuildHomepage(){
        $this->assertTrue(true);
    }

    public function testLoadCurrentCompetitions(){
        $this->assertTrue(true);
    }

    public function testLoadUpcomingCompetitions(){
        $this->assertTrue(true);
    }

    public function testLoadRecentWinners(){
        $this->assertTrue(true);
    }
}