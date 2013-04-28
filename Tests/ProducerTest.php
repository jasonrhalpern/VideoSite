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
        $this->assertTrue($this->producer->createSeries($this->series, 'best season ever'));
        $this->assertFalse($this->producer->createSeries($this->series, 'second best season ever'));
        $series = Series::loadSeriesByTitle($this->series->getTitle());

        $this->assertTrue($this->dbConnection->deleteSeason($series->getId(), 1));
        $this->assertTrue($this->dbConnection->deleteSeries($this->series));
        $this->assertTrue($this->s3Client->deleteSeasonFolder($this->series, 1));
        $this->assertTrue($this->s3Client->deleteSeriesFolder($this->series));

    }

    public function testCreateNewSeason(){
        $this->assertTrue($this->producer->createSeries($this->series, 'this season will rock'));

        $series = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');

        $this->producer->createNewSeason($series, 'Figaro arrives for fun goodness');
        $series = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');
        $this->assertEquals(2, $series->getSeasonNum());

        $this->producer->createNewSeason($series, 'The return of Figaro');
        $series = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');
        $this->assertEquals(3, $series->getSeasonNum());

        $this->assertTrue($this->dbConnection->deleteSeries($this->series));
        $this->assertTrue($this->dbConnection->seasonExists($series->getId(), 1));
        $this->assertTrue($this->dbConnection->seasonExists($series->getId(), 2));
        $this->assertTrue($this->dbConnection->seasonExists($series->getId(), 3));
        $this->assertTrue($this->dbConnection->deleteSeason($series->getId(), 1));
        $this->assertTrue($this->dbConnection->deleteSeason($series->getId(), 2));
        $this->assertTrue($this->dbConnection->deleteSeason($series->getId(), 3));
        $this->assertTrue($this->s3Client->deleteSeasonFolder($series, 1));
        $this->assertTrue($this->s3Client->deleteSeasonFolder($series, 2));
        $this->assertTrue($this->s3Client->deleteSeasonFolder($series, 3));
        $this->assertTrue($this->s3Client->deleteSeriesFolder($this->series));
    }

    public function testEditSeriesDescr(){
        $this->assertTrue($this->producer->createSeries($this->series, 'great season brah'));

        $series = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');
        $this->assertEquals($series->getDescription(),'Figaros Heroic Effort To Save The World');
        $this->assertTrue($this->producer->editSeriesDescr($series, 'Figaros Failed Heroic Effort To Save Us'));

        $seriesTwo = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');
        $this->assertEquals($seriesTwo->getDescription(), 'Figaros Failed Heroic Effort To Save Us');

        $this->assertTrue($this->dbConnection->deleteSeries($this->series));
        $this->assertTrue($this->s3Client->deleteSeasonFolder($this->series, 1));
        $this->assertTrue($this->s3Client->deleteSeriesFolder($this->series));
        $this->assertTrue($this->dbConnection->deleteSeason($series->getId(), 1));

    }

    public function testAddEpisodeToSeries(){

        $this->assertTrue($this->producer->createSeries($this->series, 'awesome season brah'));
        $series = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');

        $video = new Video('Hicks vss Gangstas booyah', 'battlezz of tha century', 1);
        $episodeOne = new Episode($video, $series->getId(), $series->getSeasonNum(),
                                ($series->getNumEpisodesInSeason($series->getSeasonNum()) + 1));

        $this->producer->addEpisodeToSeries($series, $video, '/var/www/Tests/TestFiles/test.mov');

        $this->assertTrue($this->s3Client->waitUntilFileExists($this->s3Client->getSeasonFolderPath($series, 1) , '1_HD.mp4'));
        $this->assertTrue($this->s3Client->waitUntilFileExists($this->s3Client->getSeasonFolderPath($series, 1) , '1_SD.mp4'));

        $this->assertEquals($series->getNumEpisodesInSeason(1), 1);

        $videoTwo = new Video('Hicks vss Gangstas booyah', 'battlezz of tha century', 1);
        $episodeTwo = new Episode($videoTwo, $series->getId(), $series->getSeasonNum(),
            ($series->getNumEpisodesInSeason($series->getSeasonNum()) + 1));

        $this->producer->addEpisodeToSeries($series, $videoTwo, '/var/www/Tests/TestFiles/test.mov');

        $this->assertTrue($this->s3Client->waitUntilFileExists($this->s3Client->getSeasonFolderPath($series, 1) , '2_HD.mp4'));
        $this->assertTrue($this->s3Client->waitUntilFileExists($this->s3Client->getSeasonFolderPath($series, 1) , '2_SD.mp4'));

        $this->assertEquals($series->getNumEpisodesInSeason(1), 2);
        $this->producer->createNewSeason($series, 'The return of Figaro');
        $this->assertEquals($series->getSeasonNum(), 2);

        $videoThree = new Video('Hicks vss Gangstas booyah', 'battlezz of tha century', 1);
        $episodeThree = new Episode($videoThree, $series->getId(), $series->getSeasonNum(),
            ($series->getNumEpisodesInSeason($series->getSeasonNum()) + 1));

        $this->producer->addEpisodeToSeries($series, $videoThree, '/var/www/Tests/TestFiles/test.mov');
        $this->assertTrue($this->s3Client->waitUntilFileExists($this->s3Client->getSeasonFolderPath($series, 2) , '1_HD.mp4'));
        $this->assertTrue($this->s3Client->waitUntilFileExists($this->s3Client->getSeasonFolderPath($series, 2) , '1_SD.mp4'));

        $this->assertEquals($series->getNumEpisodesInSeason(2), 1);

        $this->assertTrue($this->dbConnection->deleteEpisode($episodeOne));
        $this->assertTrue($this->dbConnection->deleteEpisode($episodeTwo));
        $this->assertTrue($this->dbConnection->deleteEpisode($episodeThree));

        $this->s3Client->deleteEpisode($series, 1, 1);
        $this->s3Client->deleteEpisode($series, 1, 2);
        $this->s3Client->deleteEpisode($series, 2, 1);

        $this->s3Client->deleteSeasonFolder($series, 1);
        $this->s3Client->deleteSeasonFolder($series, 2);

        $this->assertTrue($this->dbConnection->deleteSeason($series->getId(), 1));
        $this->assertTrue($this->dbConnection->deleteSeason($series->getId(), 2));
        $this->assertTrue($this->dbConnection->deleteSeries($this->series));
        $this->s3Client->deleteSeriesFolder($series);
    }
}
