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

        $this->assertEquals($videoWindow["videoId"], $this->videoOne->getVideoId());
        $this->assertEquals($videoWindow["competitionId"], 646);
        $this->assertEquals($videoWindow["videoTitle"], $this->videoOne->getTitle());
        $this->assertEquals($videoWindow["videoDescription"], $this->videoOne->getDescription());
        $this->assertEquals($videoWindow["userId"], $this->userOne->getId());
        $this->assertEquals($videoWindow["createdDate"], $this->videoOne->getPostedDate());
        $this->assertEquals($videoWindow["videoLength"], $this->videoOne->getLength());
        $this->assertEquals($videoWindow["votes"], $this->videoOne->getLikes());
        $this->assertEquals($videoWindow["username"], $this->userOne->getUsername());
        $this->assertEquals($videoWindow["competitionTitle"], 'acting like deniro');
        $this->assertEquals($videoWindow["competitionDescription"], 'do your best deniro impression');
        $this->assertEquals($videoWindow["competitionType"], 'Individual');
        $this->assertEquals($videoWindow["category"], 'Acting');
        $this->assertEquals($videoWindow["competitionStartDate"], '2013-06-20');
        $this->assertEquals($videoWindow["competitionEndDate"], '2013-06-24');

    }

    public function testLoadComments(){
        $this->comment = new Comment($this->videoOne->getVideoId(), $this->userOne->getUsername(), 'This vid sucks');
        $this->dbConnection->insertComment($this->comment);

        $videoPageBuilder = new VideoPageBuilder($this->videoOne->getVideoId());
        $comments = $videoPageBuilder->loadComments();

        $this->assertEquals($comments[0]["production_id"], $this->videoOne->getVideoId());
        $this->assertEquals($comments[0]["username"], $this->userOne->getUsername());
        $this->assertEquals($comments[0]["comment_text"], 'This vid sucks');

        $this->assertTrue(empty($comments[1]));


        $this->dbConnection->deleteComment($comments[0]["comment_id"]);
        unset($this->comment);
    }

    public function testLoadOtherVideosInCompetitionSidebar(){
        $videoPageBuilder = new VideoPageBuilder($this->videoOne->getVideoId());
        $videoPageBuilder->buildVideoPage();
        $participants = $videoPageBuilder->getOtherVideosInCompetitionSidebar();

        $this->assertEquals($participants[0]["id"], $this->videoTwo->getVideoId());
        $this->assertEquals($participants[0]["title"], $this->videoTwo->getTitle());
        $this->assertEquals($participants[0]["description"], $this->videoTwo->getDescription());
        $this->assertEquals($participants[0]["created"], $this->videoTwo->getPostedDate());
        $this->assertEquals($participants[0]["user"], $this->userTwo->getUsername());
        $this->assertEquals($participants[0]["views"], $this->videoTwo->getViews());
        $this->assertEquals($participants[0]["length"], $this->videoTwo->getLength());
        $this->assertEquals($participants[0]["votes"], $this->videoTwo->getLikes());

        $this->assertTrue(empty($participants[1]));
    }
}

?>