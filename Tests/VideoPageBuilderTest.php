<?php
/**
 * @author Jason Halpern
 */
require_once dirname(__FILE__) . '/../Classes/MySQL.php';
require_once dirname(__FILE__) . '/../Classes/Competition.php';
require_once dirname(__FILE__) . '/../Classes/DateHelper.php';
require_once dirname(__FILE__) . '/../Classes/User.php';
require_once dirname(__FILE__) . '/../Classes/Video.php';
require_once dirname(__FILE__) . '/../Classes/Comment.php';
require_once dirname(__FILE__) . '/../PageBuilders/VideoPageBuilder.php';

class VideoPageBuilderTest extends PHPUnit_Framework_TestCase{

    protected $dbConnection;
    protected $competition;
    protected $userOne;
    protected $userTwo;
    protected $videoOne;
    protected $videoTwo;
    protected $comment;

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

        $this->dbConnection->insertCompetitionEntry(646, $videoId);

        $user = User::login($this->userTwo->getEmail(), $this->userTwo->getPassword());
        $this->userTwo = $user;
        $this->videoTwo = new Video('Hicks vz Gangstas booyah', 'battezz of tha century', $user->getId());
        $this->dbConnection->insertVideo($this->videoTwo);
        $videoId = $this->dbConnection->mostRecentVideoId($user->getId());
        $this->videoTwo = Video::loadVideoById($videoId);

        $this->dbConnection->insertCompetitionEntry(646, $videoId);
    }

    public function tearDown(){
        $this->dbConnection->deleteCompetition($this->competition);
        $this->dbConnection->deleteCompetitionEntry(646, $this->videoOne->getVideoId());
        $this->dbConnection->deleteCompetitionEntry(646, $this->videoTwo->getVideoId());
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

    public function testLoadVideoWindow(){
        $videoPageBuilder = new VideoPageBuilder($this->videoOne->getVideoId());
        $videoWindow = $videoPageBuilder->loadVideoWindow();
        $this->assertTrue(true);

    }

    public function testLoadComments(){
        $videoPageBuilder = new VideoPageBuilder($this->videoOne->getVideoId());
        $comments = $videoPageBuilder->loadComments();
        $this->assertTrue(true);

    }

    public function testLoadOtherVideosInCompetitionSidebar(){
        $videoPageBuilder = new VideoPageBuilder($this->videoOne->getVideoId());
        $videoPageBuilder->buildVideoPage();
        $otherVideos = $videoPageBuilder->getOtherVideosInCompetitionSidebar();
        $this->assertTrue(true);

    }
}

?>