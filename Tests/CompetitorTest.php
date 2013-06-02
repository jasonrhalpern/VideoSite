<?php
/**
 * @author Jason Halpern
 */
require_once dirname(__FILE__) . '/../Classes/MySQL.php';
require_once dirname(__FILE__) . '/../Classes/Competitor.php';
require_once dirname(__FILE__) . '/../Classes/Competition.php';
require_once dirname(__FILE__) . '/../Classes/Video.php';

class CompetitorTest extends PHPUnit_Framework_TestCase{

    protected $competition;
    protected $video;
    protected $s3Client;
    protected $user;
    protected $dbConnection;

    public function setUp(){
        $this->competition = Competition::loadCompetitionById(1);
        $this->s3Client = new S3();
        $this->user = new User('Stevie Boyko', 'georgeiee106@aol.com', 'Stevie1239', 1, 'teddie');
        $this->dbConnection = new MySQL();
    }

    public function tearDown(){
        unset($this->competition);
        unset($this->video);
        unset($this->s3Client);
        unset($this->user);
        unset($this->dbConnection);
    }

    public function testAddVideoToCompetition(){
        $this->assertTrue($this->dbConnection->insertUser($this->user));
        $user = User::login($this->user->getEmail(), $this->user->getPassword());
        $competitor = new Competitor($user);

        $video = new Video('', 'battle of tha century', $competitor->getId());
        $joinCompetitionArray = $competitor->addVideoToCompetition($this->competition, $video, '/var/www/Tests/TestFiles/test.mov');
        $this->assertFalse($joinCompetitionArray['valid']);
        $this->assertContains("You must enter a title for the video", $joinCompetitionArray['errors']);
        $this->assertNotContains("You must enter a description for the video", $joinCompetitionArray['errors']);
        $this->assertNotContains("We could not add your video to the competition at this time,
                                                please try again later", $joinCompetitionArray['errors']);

        $video = new Video('Hicks vss Gangstaz', '', $competitor->getId());
        $joinCompetitionArray = $competitor->addVideoToCompetition($this->competition, $video, '/var/www/Tests/TestFiles/test.mov');
        $this->assertFalse($joinCompetitionArray['valid']);
        $this->assertNotContains("You must enter a title for the video", $joinCompetitionArray['errors']);
        $this->assertContains("You must enter a description for the video", $joinCompetitionArray['errors']);
        $this->assertNotContains("We could not add your video to the competition at this time,
                                                please try again later", $joinCompetitionArray['errors']);

        $video = new Video('Hicks vss Gangstaz', 'battle of tha century', $competitor->getId());
        $joinCompetitionArray = $competitor->addVideoToCompetition($this->competition, $video, '/var/www/Tests/TestFiles/test.mov');
        $this->assertTrue($joinCompetitionArray['valid']);

        $videoId = $this->dbConnection->mostRecentVideoId($user->getId());
        $video->setVideoId($videoId);
        $this->assertTrue($this->dbConnection->deleteUser($user));
        $this->assertTrue($this->dbConnection->deleteVideo($video));
        $this->assertTrue($this->dbConnection->deleteCompetitionEntry($this->competition->getId(), $video->getVideoId()));

        $this->assertTrue($this->s3Client->waitUntilFileExists($this->s3Client->getFullCompetitionPath($this->competition), (string)$video->getVideoId()));
        $this->assertTrue($this->s3Client->waitUntilFileExists($this->s3Client->getFullCompetitionPath($this->competition), (string)$video->getVideoId() . '_SD.mp4'));

        $this->s3Client->deleteCompetitionEntry($this->competition, $video->getVideoId());
    }

}
