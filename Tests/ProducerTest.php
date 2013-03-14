<?php
/**
 * @author Jason Halpern
 */

require_once dirname(__FILE__) . '/../Classes/Producer.php';
require_once dirname(__FILE__) . '/../Classes/Series.php';
require_once dirname(__FILE__) . '/../Classes/S3.php';

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
        $this->assertTrue($this->s3Client->deleteSeriesFolder($this->series));
    }

    public function testCreateNewSeason(){
        $this->assertTrue($this->producer->createSeries($this->series));

        $series = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');

        $this->producer->createNewSeason($series);
        $series = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');
        $this->assertEquals(2, $series->getSeasonNum());

        $this->producer->createNewSeason($series);
        $series = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');
        $this->assertEquals(3, $series->getSeasonNum());

        $this->assertTrue($this->dbConnection->deleteSeries($this->series));
    }

    public function testEditSeriesDescr(){
        $this->assertTrue($this->producer->createSeries($this->series));

        $series = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');
        $this->assertEquals($series->getDescription(),'Figaros Heroic Effort To Save The World');
        $this->assertTrue($this->producer->editSeriesDescr($series, 'Figaros Failed Heroic Effort To Save Us'));

        $seriesTwo = Series::loadSeriesByTitle('Figaro Saves The World Part Deux');
        $this->assertEquals($seriesTwo->getDescription(), 'Figaros Failed Heroic Effort To Save Us');

        $this->assertTrue($this->dbConnection->deleteSeries($this->series));
    }

}
