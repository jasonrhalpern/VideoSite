<?php
/**
 * @author Jason Halpern
 */
class SeriesTest extends PHPUnit_Framework_TestCase{

    protected $seriesOne;
    protected $dbConnection;

    public function setUp(){
        $this->seriesOne = new Series(1, 'The Crazies Are Out', 'An in depth look into an insane asylum',
                                        'Documentary', 1);
        $this->dbConnection = new MySQL();
    }

    public function tearDown(){
        unset($this->seriesOne);
        unset($this->dbConnection);
    }

    public function test(){
        $this->assertTrue(true);
    }
}
