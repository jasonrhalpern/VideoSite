<?php
/**
 * @author Jason Halpern
 */
require_once dirname(__FILE__) . '/../Classes/Series.php';
require_once dirname(__FILE__) . '/../Classes/MySQL.php';

class SeriesTest extends PHPUnit_Framework_TestCase{

    protected $seriesOne;
    protected $dbConnection;

    public function setUp(){
        $this->seriesOne = new Series(1, 'Winkle and Dinkle Go West', 'Two brothers go on a wilderness adventure',
                                        'Comedy', 1);
        $this->dbConnection = new MySQL();
    }

    public function tearDown(){
        unset($this->seriesOne);
        unset($this->dbConnection);
    }

    public function testLoadSeriesById(){
        $series = Series::loadSeriesById(17);
        $this->assertEquals($series->getId(), 17);
        $this->assertEquals($series->getCreatorId(), 1);
        $this->assertEquals($series->getCategory(), 'Comedy');
        $this->assertEquals($series->getTitle(), 'Winkle and Dinkle Go West');
        $this->assertEquals($series->getDescription(), 'Two brothers go on a wilderness adventure');

        /* the next two series do not exist */
        $seriesTwo = Series::loadSeriesById(1);
        $this->assertFalse($seriesTwo);

        $seriesThree = Series::loadSeriesById(7);
        $this->assertFalse($seriesThree);
    }

    public function testLoadSeriesByTitle(){
        $series = Series::loadSeriesByTitle('Winkle and Dinkle Go West');
        $this->assertEquals($series->getId(), 17);
        $this->assertEquals($series->getCreatorId(), 1);
        $this->assertEquals($series->getCategory(), 'Comedy');
        $this->assertEquals($series->getTitle(), 'Winkle and Dinkle Go West');
        $this->assertEquals($series->getDescription(), 'Two brothers go on a wilderness adventure');

        /* the next two series do not exist */
        $seriesTwo = Series::loadSeriesByTitle('Dinkle goes west');
        $this->assertFalse($seriesTwo);

        $seriesThree = Series::loadSeriesByTitle('Winkle and Dinkle Go Wes');
        $this->assertFalse($seriesThree);
    }
}
