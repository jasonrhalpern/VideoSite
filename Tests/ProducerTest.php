<?php
/**
 * @author Jason Halpern
 */

require_once dirname(__FILE__) . '/../Classes/Producer.php';
require_once dirname(__FILE__) . '/../Classes/Series.php';
require_once dirname(__FILE__) . '/../Classes/S3.php';
require_once dirname(__FILE__) . '/../Classes/Video.php';
require_once dirname(__FILE__) . '/../Classes/Episode.php';

class ProducerTest extends PHPUnit_Framework_TestCase{

    protected $series;
    protected $producer;
    protected $s3Client;
    protected $dbConnection;

    public function setUp(){
        $this->series = new Series(1, 'Figaro Saves The World Part Deux', 'Figaros Heroic Effort To Save The World',
            'Action/Adventure', 1);
        $this->s3Client = new S3();
        $user = new User('Fintattog Gootryo', 'ffiittnneeal@aol.com', 'Finathyp98', 1, 'tanya');
        $this->producer = new Producer($user);
        $this->dbConnection = new MySQL();

        $this->dbConnection->insertUser($this->producer);
    }

    public function tearDown(){
        $this->dbConnection->deleteUser($this->producer);

        unset($this->series);
        unset($this->producer);
        unset($this->s3Client);
        unset($this->dbConnection);
    }

    public function testCreateSeries(){
        $this->assertTrue($this->producer->createSeries($this->series));
        $this->assertFalse($this->producer->createSeries($this->series));

        $this->assertTrue($this->dbConnection->deleteSeries($this->series));
        $this->assertTrue($this->s3Client->deleteSeasonFolder($this->series, 1));
        $this->assertTrue($this->s3Client->deleteSeriesFolder($this->series));
    }

    public function testCreateNewSeason(){
        $this->assertTrue($this->producer->createSeries($this->series));

        $series = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');

        $this->producer->createNewSeason($series, 'Figaro arrives for fun goodness');
        $series = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');
        $this->assertEquals(2, $series->getSeasonNum());

        $this->producer->createNewSeason($series, 'The return of Figaro');
        $series = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');
        $this->assertEquals(3, $series->getSeasonNum());

        $this->assertTrue($this->dbConnection->deleteSeries($this->series));
        $this->assertTrue($this->dbConnection->deleteSeason($series->getId(), 2));
        $this->assertTrue($this->dbConnection->deleteSeason($series->getId(), 3));
        $this->assertTrue($this->s3Client->deleteSeasonFolder($series, 1));
        $this->assertTrue($this->s3Client->deleteSeasonFolder($series, 2));
        $this->assertTrue($this->s3Client->deleteSeasonFolder($series, 3));
        $this->assertTrue($this->s3Client->deleteSeriesFolder($this->series));
    }

    public function testEditSeriesDescr(){
        $this->assertTrue($this->producer->createSeries($this->series));

        $series = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');
        $this->assertEquals($series->getDescription(),'Figaros Heroic Effort To Save The World');
        $this->assertTrue($this->producer->editSeriesDescr($series, 'Figaros Failed Heroic Effort To Save Us'));

        $seriesTwo = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');
        $this->assertEquals($seriesTwo->getDescription(), 'Figaros Failed Heroic Effort To Save Us');

        $this->assertTrue($this->dbConnection->deleteSeries($this->series));
        $this->assertTrue($this->s3Client->deleteSeasonFolder($this->series, 1));
        $this->assertTrue($this->s3Client->deleteSeriesFolder($this->series));
    }

    public function testAddEpisodeToSeries(){
        $this->assertTrue($this->producer->createSeries($this->series));
        $series = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');

        $video = new Video('Hicks vss Gangstas booyah', 'battlezz of tha century', 1);
        $episode = new Episode($video, $series->getId(), $series->getSeasonNum(),
                                ($series->getNumEpisodesInSeason($series->getSeasonNum()) + 1));

        $this->producer->addEpisodeToSeries($series, $video, '/var/www/Tests/TestFiles/test.mov');
        $this->assertTrue($this->dbConnection->deleteEpisode($episode));
        $this->assertTrue($this->dbConnection->deleteSeries($this->series));
        $this->assertTrue($this->s3Client->deleteSeasonFolder($this->series, 1));
        $this->assertTrue($this->s3Client->deleteSeriesFolder($this->series));
    }
}
