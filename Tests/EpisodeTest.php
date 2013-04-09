<?php
/**
 * @author Jason Halpern
 */
require_once dirname(__FILE__) . '/../Classes/MySQL.php';
require_once dirname(__FILE__) . '/../Classes/Episode.php';

class EpisodeTest extends PHPUnit_Framework_TestCase{

    public function testLoadEpisodeById(){
        $episode = Episode::loadEpisodeById(14);
        $this->assertEquals($episode->getVideo()->getVideoId(), 14);
        $this->assertEquals($episode->getEpisodeNumber(), 1);
        $this->assertEquals($episode->getSeriesId(), 1);
        $this->assertEquals($episode->getSeasonNumber(), 1);
        $this->assertEquals($episode->getVideo()->getSubmitter(), 2);

        $video = Video::loadVideoById(14);
        $this->assertEquals($video->getVideoId(), $episode->getVideo()->getVideoId());
        $this->assertEquals($video->getSubmitter(), $episode->getVideo()->getSubmitter());
        $this->assertEquals($video->getLength(), $episode->getVideo()->getLength());
        $this->assertEquals($video->getLikes(), $episode->getVideo()->getLikes());
        $this->assertEquals($video->getPostedDate(), $episode->getVideo()->getPostedDate());
        $this->assertEquals($video->getDescription(), $episode->getVideo()->getDescription());
        $this->assertEquals($video->getTitle(), $episode->getVideo()->getTitle());

        $episodeFailure = Episode::loadEpisodeById(0);
        $this->assertFalse($episodeFailure);
    }

}

?>