<?php
/**
 * @author Jason Halpern
 */

require_once dirname(__FILE__) . '/../Classes/MySQL.php';
require_once dirname(__FILE__) . '/../Classes/Competition.php';

class CompetitionTest extends PHPUnit_Framework_TestCase{

    public function testLoadCompetitionById(){
        $competition = Competition::loadCompetitionById(646);
        $this->assertEquals($competition->getId(), 646);
        $this->assertEquals($competition->getTitle(), "acting like deniro");
        $this->assertEquals($competition->getDescription(), "do your best deniro impression");
        $this->assertEquals($competition->getStartDate(), "2013-06-20");
        $this->assertEquals($competition->getEndDate(), "2013-06-24");
        $this->assertEquals($competition->getEntryFee(), 2.99);
        $this->assertEquals($competition->getType(), "Individual");
        $this->assertEquals($competition->getCategory(), "Acting");

        $competitionFailure = Competition::loadCompetitionById(2);
        $this->assertFalse($competitionFailure);
    }

}
