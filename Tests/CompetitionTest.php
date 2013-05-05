<?php
/**
 * @author Jason Halpern
 */

require_once dirname(__FILE__) . '/../Classes/MySQL.php';
require_once dirname(__FILE__) . '/../Classes/Competition.php';

class CompetitionTest extends PHPUnit_Framework_TestCase{

    public function testLoadCompetitionById(){
        $competition = Competition::loadCompetitionById(1);
        $this->assertEquals($competition->getId(), 1);
        $this->assertEquals($competition->getTitle(), "The joke is on us");
        $this->assertEquals($competition->getDescription(), "tell the funniest joke you know");
        $this->assertEquals($competition->getStartDate(), "2013-05-07");
        $this->assertEquals($competition->getEndDate(), "2013-05-12");
        $this->assertEquals($competition->getEntryFee(), 2.99);
        $this->assertEquals($competition->getType(), "Individual");
        $this->assertEquals($competition->getCategory(), "Comedy");

        $competitionFailure = Competition::loadCompetitionById(2);
        $this->assertFalse($competitionFailure);
    }

}
