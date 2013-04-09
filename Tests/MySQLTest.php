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

class MySQLTest extends PHPUnit_Framework_TestCase{
    protected $user;
    protected $series;
    protected $dbConnection;

    public function setUp(){
        $this->user = new User('Jason HollaBack', 'jasonrhalpern@gmail.com', 'JzPern67', 1, 'sailor');
        $this->series = new Series(1, 'The Crazies Are Out', 'An in depth look into an insane asylum',
                                        'Documentary', 1);
        $this->dbConnection = new MySQL();
    }

    public function tearDown(){
        $this->dbConnection->deleteUser($this->user);
        $this->dbConnection->deleteSeries($this->series);

        unset($this->user);
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

    public function testMostRecentVideoId(){
        $video = new Video('Hicks vss Gangstas booyah', 'battlezz of tha century', 1);
        $episode = new Episode($video, 1, 1, 1);
        $this->assertTrue($this->dbConnection->insertEpisode($episode));

        $video = HelperFunc::loadVideoByDetails($video->getTitle(), $video->getDescription());
        $videoId = $this->dbConnection->mostRecentVideoId($video->getSubmitter());
        $this->assertEquals($videoId, $video->getVideoId());
        $this->assertTrue($this->dbConnection->deleteEpisode($episode));

    }
}
