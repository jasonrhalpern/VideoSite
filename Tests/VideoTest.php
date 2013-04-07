<?php
/**
 * @author Jason Halpern
 */
require_once dirname(__FILE__) . '/../Classes/MySQL.php';
require_once dirname(__FILE__) . '/../Classes/Video.php';

class VideoTest extends PHPUnit_Framework_TestCase{

    public function testLoadVideoById(){

        $video = Video::loadVideoById(14);
        $this->assertEquals($video->getVideoId(), 14);
        $this->assertEquals($video->getTitle(), 'Hickszzzz vss Gangstas booyah');
        $this->assertEquals($video->getDescription(), 'battlezzyy of thaa century');
        $this->assertEquals($video->getSubmitter(), 2);
        $this->assertEquals($video->getViews(), 0);
        $this->assertEquals($video->getLength(), 0);
        $this->assertEquals($video->getLikes(), 0);

        $videoFailure = Video::loadVideoById(0);
        $this->assertFalse($videoFailure);
    }
}

?>