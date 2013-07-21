<?php
/**
 * This handles loading the information that we need for the sections on the competition page
 *
 * @author Jason Halpern
 * @since 7/6/2013
 */

class CompetitionPageBuilder{

    protected $competitionId;
    protected $competition;
    protected $state; //closed, current or upcoming
    protected $winner;
    protected $runnerUp;
    protected $finalists;
    protected $otherParticipants;
    protected $db;

    public function __construct($competitionId){
        $this->competitionId = $competitionId;
        $this->db = new MySQL();
    }

    public function buildCompetitionPage(){
        $this->competition = $this->loadCompetition();
        $this->determineState($this->competition->getStartDate(), $this->competition->getEndDate());
        $this->winner = $this->loadWinner();
        $this->runnerUp = $this->loadRunnerUp();
        $this->loadParticipants();
    }

    /**
     * Load the competition details for this page
     *
     * @return array with the competition details
     */
    public function loadCompetition(){
        $query = $this->getDBConnection()->prepare("select * from competition where id = ?");
        $query->bind_param("i", $this->competitionId);
        $query->execute();
        $query->store_result();
        $query->bind_result($id, $title, $description, $startDate, $endDate, $entryFee, $compType, $category);

        $competition = array();
        if($query->fetch()){
            $competition["id"] = $id;
            $competition["title"] = $title;
            $competition["description"] = $description;
            $competition["startDate"] = $startDate;
            $competition["endDate"] = $endDate;
            $competition["entryFee"] = $entryFee;
            $competition["type"] = $compType;
            $competition["category"] = $category;
            $competition["participants"] = $this->db->getNumParticipants($id);
        }

        return $competition;
    }

    /**
     * Load the winner's details for this page
     *
     * @return array with the winner's details
     */
    public function loadWinner(){
        $query = $this->getDBConnection()->prepare("select v.video_id, v.title, v.description, v.created,
                                                    v.views, v.video_length, v.likes, u.username
                                                    from competition_winner cw, video v, users u
                                                    where cw.competition_id = ? and cw.video_id = v.video_id
                                                    and v.created_by = u.user_id");
        $query->bind_param("i", $this->competitionId);
        $query->execute();
        $query->store_result();
        $query->bind_result($videoId,  $title, $description, $created, $views, $length, $likes, $username);

        $winner = array();
        if($query->fetch()){
            $winner["id"] = $videoId;
            $winner["title"] = $title;
            $winner["description"] = $description;
            $winner["created"] = $created;
            $winner["user"] = $username;
            $winner["views"] = $views;
            $winner["length"] = $length;
            $winner["likes"] = $likes;
        }

        return $winner;
    }

    /**
     * Load the runner up's details for this page
     *
     * @return array with the runner up's details
     */
    public function loadRunnerUp(){
        $query = $this->getDBConnection()->prepare("select v.video_id, v.title, v.description, v.created,
                                                    v.views, v.video_length, v.likes, u.username
                                                    from competition_runner_up cru, video v, users u
                                                    where cru.competition_id = ? and cru.video_id = v.video_id
                                                    and v.created_by = u.user_id");
        $query->bind_param("i", $this->competitionId);
        $query->execute();
        $query->store_result();
        $query->bind_result($videoId,  $title, $description, $created, $views, $length, $likes, $username);

        $runnerUp = array();
        if($query->fetch()){
            $runnerUp["id"] = $videoId;
            $runnerUp["title"] = $title;
            $runnerUp["description"] = $description;
            $runnerUp["created"] = $created;
            $runnerUp["user"] = $username;
            $runnerUp["views"] = $views;
            $runnerUp["length"] = $length;
            $runnerUp["likes"] = $likes;
        }

        return $runnerUp;

    }

    /**
     * Load the all the participants for this page
     *
     * @return array with the participants' details
     */
    public function loadParticipants(){
        $query = $this->getDBConnection()->prepare("select v.video_id, v.title, v.description, v.created,
                                                    v.views, v.video_length, v.likes, u.username
                                                    from competition comp, competition_entries ce, video v, users u
                                                    where comp.id = ? and comp.id = ce.competition_id
                                                    and ce.video_id = v.video_id
                                                    and v.created_by = u.user_id
                                                    order by v.likes desc");
        $query->bind_param("i", $this->competitionId);
        $query->execute();
        $query->store_result();
        $query->bind_result($videoId,  $title, $description, $created, $views, $length, $likes, $username);
        $count = 0;
        $participants = array();
        while($query->fetch()){
            $participants[$count]["id"] = $videoId;
            $participants[$count]["title"] = $title;
            $participants[$count]["description"] = $description;
            $participants[$count]["created"] = $created;
            $participants[$count]["user"] = $username;
            $participants[$count]["views"] = $views;
            $participants[$count]["length"] = $length;
            $participants[$count]["likes"] = $likes;
            if($count >= 2 && $count <=7){
                $this->finalists[] = $participants[$count];
            } else if($count > 7) {
                $this->otherParticipants[] = $participants[$count];
            }
            $count++;
        }
    }

    /**
     * Determine the state of this competition
     *
     * @return string The state of the competition
     */
    public function determineState($startDate, $endDate){
        $currentDate = strtotime(DateHelper::currentDate());
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        if($currentDate > $endDate)
            $this->setState('closed');

        if($currentDate <= $endDate && $currentDate >= $startDate )
            $this->setState('current');

        if($currentDate < $startDate)
            $this->setState('upcoming');

    }

    public function getCompetitionId(){
        return $this->competitionId;
    }

    public function setCompetitionId($competitionId){
        $this->competitionId = $competitionId;
    }

    public function getCompetition(){
        return $this->competition;
    }

    public function setCompetition($competition){
        $this->competition = $competition;
    }

    public function getState(){
        return $this->state;
    }

    public function setState($state){
        $this->state = $state;
    }

    public function getWinner(){
        return $this->winner;
    }

    public function setWinner($winner){
        $this->winner = $winner;
    }

    public function getRunnerUp(){
        return $this->runnerUp;
    }

    public function setRunnerUp($runnerUp){
        $this->runnerUp = $runnerUp;
    }

    public function getFinalists(){
        return $this->finalists;
    }

    public function setFinalists($finalists){
        $this->finalists = $finalists;
    }

    public function getOtherParticipants(){
        return $this->otherParticipants;
    }

    public function setOtherParticipants($otherParticipants){
        $this->otherParticipants = $otherParticipants;
    }

    /**
     * @return mysqli The database handle
     */
    public function getDBConnection(){
        return $this->db->getDBConnection();
    }

}