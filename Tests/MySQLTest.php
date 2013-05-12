<?php
/**
 * @author Jason Halpern
 */

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/../Classes/MySQL.php';
require_once dirname(__FILE__) . '/../Classes/User.php';
require_once dirname(__FILE__) . '/../Classes/Series.php';
require_once dirname(__FILE__) . '/../Classes/Video.php';
require_once dirname(__FILE__) . '/../Classes/Episode.php';
require_once dirname(__FILE__) . '/../Classes/Competition.php';
require_once dirname(__FILE__) . '/../Classes/Comment.php';


class MySQLTest extends PHPUnit_Framework_TestCase{
    protected $user;
    protected $series;
    protected $competition;
    protected $comment;
    protected $dbConnection;

    public function setUp(){
        $this->user = new User('Jason HollaBack', 'jasonrhalpern@gmail.com', 'JzPern67', 1, 'sailor');
        $this->series = new Series(1, 'The Crazies Are Out', 'An in depth look into an insane asylum',
                                        'Documentary', 1);
        $this->competition = new Competition("acting like deniro", "do your best deniro impression",
                                            DateHelper::datePlusDays(DateHelper::currentDate(), 2),
                                            DateHelper::datePlusDays(DateHelper::currentDate(), 7),
                                            2.99, 'Individual', 'Acting');
        $this->comment = new Comment(1, 'Daisies Egg', 'Comment text', DateHelper::currentDateAndTime());
        $this->dbConnection = new MySQL();
    }

    public function tearDown(){
        $this->dbConnection->deleteUser($this->user);

        $this->dbConnection->deleteSeries($this->series);

        unset($this->user);
        unset($this->competition);
        unset($this->series);
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

    public function testInsertSeries(){
        $this->assertTrue($this->dbConnection->insertSeries($this->series));

        $failedSeries = new Series(1, 'The Crazies Are Out', 'Crazies just going crazy',
                                    'Mystery/Suspense', 1);
        $this->assertFalse($this->dbConnection->insertSeries($failedSeries));
    }

    public function testInsertVideo(){
        $video = new Video('Hicks vss Gangstas booyah', 'battlezz of tha century', 1);
        $this->assertTrue($this->dbConnection->insertVideo($video));

        $video = HelperFunc::loadVideoByDetails($video->getTitle(), $video->getDescription());
        $this->assertTrue($this->dbConnection->deleteVideo($video));
    }

    public function testInsertEpisode(){
        $video = new Video('Hicks vss Gangstas booyah', 'battlezz of tha century', 1);
        $episode = new Episode($video, 1, 1, 1);

        $this->assertTrue($this->dbConnection->insertEpisode($episode));

        $video = HelperFunc::loadVideoByDetails($video->getTitle(), $video->getDescription());
        $episode = new Episode($video, 1, 1, 1);
        $this->assertTrue($this->dbConnection->deleteEpisode($episode));

    }

    public function testInsertSeason(){
        $this->assertTrue($this->dbConnection->insertSeason(1, 1, 'Thiiss season rules'));
        $this->assertFalse($this->dbConnection->insertSeason(1, 1, 'Thiiss season rules'));
        $this->assertTrue($this->dbConnection->deleteSeason(1, 1));

    }

    public function testInsertCompetition(){
        $this->assertTrue($this->dbConnection->insertCompetition($this->competition));
        $id = $this->dbConnection->mostRecentCompetitionId();
        $this->dbConnection->deleteCompetition($id);
    }

    public function testInsertCompetitionEntry(){
        $this->assertTrue($this->dbConnection->insertCompetitionEntry(1, 1));
    }

    public function testDeleteCompetitionEntry(){
        $this->assertTrue($this->dbConnection->deleteCompetitionEntry(1, 1));
    }

    public function testInsertComment(){
        $this->assertTrue($this->dbConnection->insertComment($this->comment));
    }

    public function testDeleteComment(){
        //$this->assertTrue($this->dbConnection->insertComment($this->comment));
    }

}
