<?php
/**
 * @author Jason Halpern
 */

require_once dirname(__FILE__) . '/../Classes/Series.php';
require_once dirname(__FILE__) . '/../Classes/S3.php';

class S3Test extends PHPUnit_Framework_TestCase{

    protected $series;
    protected $seriesTwo;
    protected $s3Client;

    public function setUp(){
        $this->series = new Series(1, 'The Crazies Are Out Bakonka', 'An in depth look into an insane asylum',
            'Documentary', 1);
        $this->s3Client = new S3();
    }

    public function tearDown(){
        unset($this->series);
        unset($this->s3Client);
    }

    public function testCreateSeriesFolder(){
        $this->assertTrue($this->s3Client->createSeriesFolder($this->series));
    }

    public function testSeriesFolderExists(){
        $this->assertTrue($this->s3Client->seriesFolderExists($this->series));
    }

    public function testDeleteSeriesFolder(){
        $this->assertTrue($this->s3Client->deleteSeriesFolder($this->series));
    }
}
