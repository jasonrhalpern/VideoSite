<?php
/**
 * @author Jason Halpern
 */
require_once dirname(__FILE__) . '/../Classes/Series.php';
require_once dirname(__FILE__) . '/../Classes/MySQL.php';
require_once dirname(__FILE__) . '/../Classes/User.php';

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

    public function testGetFolderName(){
        $folder = $this->seriesOne->getFolderName();
        $this->assertEquals('Winkle_and_Dinkle_Go_West', $folder);
    }

    public function testGetFullSeriesPath(){
        $fullPath = $this->seriesOne->getFullSeriesPath();
        $this->assertEquals('assets.gookeyz.com/Winkle_and_Dinkle_Go_West/', $fullPath);
    }

    public function testAddNewSeason(){

        $series = new Series(1, 'The End of The World Is Awesomme', 'It all ends tonight and we are ready to party',
            'Sci-Fi/Fantasy', 1);

        $this->assertTrue($this->dbConnection->insertSeries($series));
        $series = Series::loadSeriesByTitle('The End of The World Is Awesomme');

        $this->assertTrue($this->dbConnection->incrSeasonNum($series));
        $series = Series::loadSeriesByTitle('The End of The World Is Awesomme');
        $this->assertEquals(2, $series->getSeasonNum());

        $this->assertTrue($this->dbConnection->incrSeasonNum($series));
        $series = Series::loadSeriesByTitle('The End of The World Is Awesomme');
        $this->assertEquals(3, $series->getSeasonNum());

        $this->dbConnection->deleteSeries($series);

    }

    public function testIsProducer(){

        $user = new User('Donope Gangsta', 'georgeiee106@aol.com', 'GMoney5897', 1, 'tanya');
        $this->assertTrue($this->dbConnection->insertUser($user));
        $user = User::login($user->getEmail(), $user->getPassword());

        $series = new Series($user->getId(), 'The End of The World Is Awesomme',
                            'It all ends tonight and we are ready to party', 'Sci-Fi/Fantasy', 1);
        $this->assertTrue($this->dbConnection->insertSeries($series));
        $series = Series::loadSeriesByTitle('The End of The World Is Awesomme');
        $this->assertTrue($series->isProducer($user));


        $userTwo = new User('asdfsdfsafasdfsadf', 'ppoosdfis13@aol.com', 'GMoney32897', 1, 'tanya');
        $this->assertTrue($this->dbConnection->insertUser($userTwo));
        $userTwo = User::login($userTwo->getEmail(), $userTwo->getPassword());
        $this->assertFalse($series->isProducer($userTwo));


        $this->assertTrue($this->dbConnection->deleteUser($user));
        $this->assertTrue($this->dbConnection->deleteUser($userTwo));
        $this->assertTrue($this->dbConnection->deleteSeries($series));
    }
}
