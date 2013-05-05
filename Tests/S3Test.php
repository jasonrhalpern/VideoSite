<?php
/**
 * @author Jason Halpern
 */

require_once dirname(__FILE__) . '/../Classes/Series.php';
require_once dirname(__FILE__) . '/../Classes/Competition.php';
require_once dirname(__FILE__) . '/../Classes/S3.php';

class S3Test extends PHPUnit_Framework_TestCase{

    protected $series;
    protected $seriesTwo;
    protected $competition;
    protected $s3Client;

    public function setUp(){
        $this->series = new Series(1, 'The Crazies Are Out Bakonka', 'An in depth look into an insane asylum',
            'Documentary', 1);
        $this->competition = Competition::loadCompetitionById(1);
        $this->s3Client = new S3();
    }

    public function tearDown(){
        unset($this->series);
        unset($this->competition);
        unset($this->s3Client);
    }

    public function testCreateSeriesFolder(){
        $this->assertTrue($this->s3Client->createSeriesFolder($this->series));
    }

    public function testSeriesFolderExists(){
        $this->assertTrue($this->s3Client->seriesFolderExists($this->series));
    }

    public function testCreateSeasonFolder(){
        $this->assertTrue($this->s3Client->createSeasonFolder($this->series));
    }

    public function testSeasonFolderExists(){
        $this->assertTrue($this->s3Client->seasonFolderExists($this->series, 1));
    }

    public function testDeleteSeasonFolder(){
        $this->assertTrue($this->s3Client->deleteSeasonFolder($this->series, 1));
    }

    public function testDeleteSeriesFolder(){
        $this->assertTrue($this->s3Client->deleteSeriesFolder($this->series));
    }

    public function testVideoUpload(){
        $videoPath = '/var/www/Tests/TestFiles/test.mov';
        $key = 'testVideo';
        $bucketName = 'assets.gookeyz.com/test_bucket';
        $this->assertTrue($this->s3Client->uploadVideo($videoPath, $key, $bucketName));
        $this->assertTrue($this->s3Client->deleteVideo($bucketName, $key));
    }

    public function testImageUpload(){
        $imagePath = '/var/www/Tests/TestFiles/test.jpg';
        $key = 'testImage';
        $bucketName = 'assets.gookeyz.com/test_bucket';
        $this->assertTrue($this->s3Client->uploadImage($imagePath, $key, $bucketName));
        $this->assertTrue($this->s3Client->deleteImage($bucketName, $key));
    }

    public function testCreateCompetitionFolder(){
        $this->assertTrue($this->s3Client->createCompetitionFolder($this->competition));
    }

    public function testDeleteCompetitionFolder(){
        $this->assertTrue($this->s3Client->deleteCompetitionFolder($this->competition));
    }
}
